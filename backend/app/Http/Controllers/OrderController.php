<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function confirm()
    {
        if (auth()->check()) {
            $userId = auth()->id();
            $cartItems = Cart::with('product')->where('user_id', $userId)->get()->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'image' => $item->product->image,
                    'quantity' => $item->quantity,
                ];
            });
        } else {
            $sessionCart = session('cart', []);
            $cartItems = [];

            foreach ($sessionCart as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                if ($product) {
                    $cartItems[] = [
                        'name' => $product->name,
                        'price' => $product->price,
                        'image' => $product->image,
                        'quantity' => $item['quantity'],
                    ];
                }
            }
        }

        $totalPrice = collect($cartItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return view('confirmation', compact('cartItems', 'totalPrice'));
    }

    public function create(Request $request)
    {
        // Validate shipping address
        $request->validate([
            'shipping_address' => 'required|string|max:255',
        ]);

        // Get cart items based on authentication status
        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        } else {
            $cartItems = session()->get('cart', []);
        }

        // Check if cart is empty
        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        return DB::transaction(function () use ($request, $cartItems) {

            if (!Auth::check()) {
                // Handle guest cart by fetching product details
                $productIds = array_keys($cartItems);
                $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');
            } else {
                // Handle authenticated user's cart
                $productIds = $cartItems->pluck('product_id')->toArray();
                $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');
            }

            // Check product stock availability before proceeding
            foreach ($cartItems as $item) {
                $product = $products[$item->product_id] ?? null;
                if (!$product || $product->stock < $item->quantity) {
                    throw new \Exception("Not enough stock available for {$item->product->name}.");
                }
            }

            // Calculate total price
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += $item->product->price * $item->quantity;
            }

            \Log::info('Creating order', [
                'user_id' => Auth::id(),
                'total_price' => $totalPrice,
                'shipping_address' => $request->shipping_address,
            ]);

            // Create the order
            $order = Order::create([
                'user_id' => Auth::id() ?: null, // Handle guest orders
                'date' => now(),
                'shipping_address' => $request->shipping_address,
                'total_price' => $totalPrice,
            ]);

            // Create order details and update product stock
            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $product = $products[$item->product_id];
                \Log::info('Updating product stock', [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'stock_before' => $product->stock,
                ]);

                $product->stock -= $item->quantity;
                $product->save();

                \Log::info('Product stock updated', [
                    'product_id' => $item->product_id,
                    'stock_after' => $product->stock,
                ]);
            }

            // Clear the cart after order is placed
            if (Auth::check()) {
                Cart::where('user_id', Auth::id())->delete();
            } else {
                session()->forget('cart');
            }

            \Log::info('Order created', ['order_id' => $order->id]);

            return redirect()->route('main_page')->with('success', 'Order placed successfully!');
        }, 5);
    }

    public function index()
    {
        // Retrieve orders with their details and associated products
        $orders = Order::where('user_id', Auth::id())->with('orderDetails.product')->get();
        return view('orders.index', compact('orders'));
    }
}
