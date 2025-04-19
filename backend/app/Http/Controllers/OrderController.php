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
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return view('confirmation', compact('cartItems', 'totalPrice'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:255',
        ]);

        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        // Используем транзакцию для атомарности операций
        return DB::transaction(function () use ($request, $cartItems) {
            // Загружаем продукты с блокировкой для предотвращения конкурентных изменений
            $productIds = $cartItems->pluck('product_id')->toArray();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            // Проверяем наличие товара
            foreach ($cartItems as $item) {
                $product = $products[$item->product_id] ?? null;
                if (!$product || $product->stock < $item->quantity) {
                    throw new \Exception("Not enough stock available for {$item->product->name}.");
                }
            }

            // Вычисляем общую стоимость
            $totalPrice = 0;
            foreach ($cartItems as $item) {
                $totalPrice += $item->product->price * $item->quantity;
            }

            // Логируем создание заказа
            \Log::info('Creating order', [
                'user_id' => Auth::id(),
                'total_price' => $totalPrice,
                'shipping_address' => $request->shipping_address,
            ]);

            // Создаём заказ
            $order = Order::create([
                'user_id' => Auth::id(),
                'date' => now(),
                'shipping_address' => $request->shipping_address,
                'total_price' => $totalPrice,
            ]);

            // Создаём детали заказа и уменьшаем stock
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

            // Очищаем корзину
            Cart::where('user_id', Auth::id())->delete();

            \Log::info('Order created', ['order_id' => $order->id]);

            return redirect()->route('main_page')->with('success', 'Order placed successfully!');
        }, 5); // 5 попыток для транзакции в случае deadlock
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->with('orderDetails.product')->get();
        return view('orders.index', compact('orders'));
    }
}
