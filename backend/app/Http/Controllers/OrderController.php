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
        if (Auth::check()) {
            // Retrieve cart items with associated products
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        } else {
            // If user is not authenticated, get the cart from session
        $sessionCart = session()->get('cart', []);
        $cartItems = collect($sessionCart)->map(function ($item) {
            $product = Product::find($item['product_id']);
            return (object) [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'product' => $product, // Add product relation
            ];
        })
            ;}

        $totalPrice = 0;
        foreach ($cartItems as $item) {
            // Check if 'product' exists and calculate total price
            if (isset($item->product)) {
                $totalPrice += $item->quantity * $item->product->price;
            }
        }

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
        // Преобразуем в коллекцию объектов для унификации
        $cartItems = collect($cartItems)->map(function ($item, $productId) {
            return (object) [
                'product_id' => $productId,
                'quantity' => $item['quantity'],
            ];
        })->values();
    }

    // Check if cart is empty
    if ($cartItems->isEmpty()) {
        return redirect()->back()->with('error', 'Your cart is empty.');
    }

    // Load products and attach them to cart items
    $productIds = $cartItems->pluck('product_id')->toArray();
    $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

    // Attach product to each cart item
    $cartItems = $cartItems->map(function ($item) use ($products) {
        $item->product = $products[$item->product_id] ?? null;
        return $item;
    });

    return DB::transaction(function () use ($request, $cartItems) {
        $products = Product::whereIn('id', $cartItems->pluck('product_id')->toArray())->lockForUpdate()->get()->keyBy('id');

        // Check product stock availability before proceeding
        foreach ($cartItems as $item) {
            $product = $products[$item->product_id] ?? null;
            if (!$product || $product->stock < $item->quantity) {
                throw new \Exception("Not enough stock available for product ID {$item->product_id}.");
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
            'user_id' => Auth::id() ?: null,
            'date' => now(),
            'shipping_address' => $request->shipping_address,
            'total_price' => $totalPrice,
        ]);

        // Create order details and update product stock
        foreach ($cartItems as $item) {
            $product = $products[$item->product_id];
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price, // Теперь работает для гостей
            ]);

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
