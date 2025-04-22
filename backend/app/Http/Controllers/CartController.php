<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
class CartController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        } else {
            // Guest user - get items from session
            $sessionCart = session()->get('cart', []);
            $cartItems = collect($sessionCart)->map(function ($item) {
                $product = Product::find($item['product_id']);
                return (object)[
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'id' => $item['product_id'],
                ];
            });
        }

        $totalPrice = $cartItems->sum(fn($item) => $item->quantity * $item->product->price);

        return view('cart', compact('cartItems', 'totalPrice'));
    }
    public function add(Request $request, Product $product)
    {
        if ($product->stock < $request->quantity) {
            return redirect()->back()->with('error', 'Not enough stock available.');
        }

        if (Auth::check()) {
            Cart::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                ],
                [
                    'quantity' => $request->quantity,
                ]
            );
        } else {
            $cart = Session::get('cart', []);
            $productId = $product->id;

            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] += $request->quantity;
            } else {
                $cart[$productId] = [
                    'product_id' => $productId,
                    'quantity' => $request->quantity,
                ];
            }

            Session::put('cart', $cart);
        }

        return redirect()->route('cart')->with('success', 'Product added to cart.');
    }

    public function update(Request $request, $id)
    {
        if (Auth::check()) {
            $cart = Cart::findOrFail($id);

            if ($cart->user_id !== Auth::id()) {
                return redirect()->route('cart')->with('error', 'Unauthorized action.');
            }

            $action = $request->input('action');
            $quantity = $cart->quantity;

            if ($action === 'increase' && $cart->product->stock > $quantity) {
                $quantity++;
            } elseif ($action === 'decrease' && $quantity > 1) {
                $quantity--;
            }

            $cart->update(['quantity' => $quantity]);
        } else {
            // Guest user
            $cart = session()->get('cart', []);
            if (!isset($cart[$id])) {
                return redirect()->route('cart')->with('error', 'Product not found in cart.');
            }

            $quantity = $cart[$id]['quantity'];
            $product = Product::find($id);

            $action = $request->input('action');

            if ($action === 'increase' && $product && $product->stock > $quantity) {
                $quantity++;
            } elseif ($action === 'decrease' && $quantity > 1) {
                $quantity--;
            }

            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart')->with('success', 'Cart updated.');
    }

    public function remove(Request $request, $id)
    {
        if (Auth::check()) {
            $cart = Cart::findOrFail($id);

            if ($cart->user_id !== Auth::id()) {
                return redirect()->route('cart')->with('error', 'Unauthorized action.');
            }

            $cart->delete();
        } else {
            // Guest user
            $cart = session()->get('cart', []);
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart')->with('success', 'Product removed from cart.');
    }
}
