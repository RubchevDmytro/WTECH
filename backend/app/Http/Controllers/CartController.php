<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return view('cart', compact('cartItems', 'totalPrice'));
    }

    public function add(Request $request, Product $product)
    {
        if ($product->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Not enough stock available.');
        }

        Cart::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ],
            [
                'quantity' => $request->quantity,
            ]
        );

        return redirect()->route('cart')->with('success', 'Product added to cart.');
    }

    public function update(Request $request, Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            return redirect()->route('cart')->with('error', 'Unauthorized action.');
        }

        $action = $request->input('action');
        $quantity = $cart->quantity;

        if ($action === 'increase') {
            if ($cart->product->stock <= $quantity) {
                return redirect()->route('cart')->with('error', 'Not enough stock available.');
            }
            $quantity++;
        } elseif ($action === 'decrease' && $quantity > 1) {
            $quantity--;
        }

        $cart->update(['quantity' => $quantity]);

        return redirect()->route('cart')->with('success', 'Cart updated.');
    }

    public function remove(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            return redirect()->route('cart')->with('error', 'Unauthorized action.');
        }

        $cart->delete();

        return redirect()->route('cart')->with('success', 'Product removed from cart.');
    }
}
