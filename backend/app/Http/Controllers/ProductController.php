<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Показать главную страницу с продуктами (для обычных пользователей).
     */
    public function index(Request $request)
    {
        \Log::info('ProductController::index accessed', [
            'user' => Auth::check() ? Auth::user()->email : 'guest',
            'is_admin' => Auth::check() ? Auth::user()->is_admin : 'not logged in',
        ]);

        try {
            $categories = Category::with('subcategories')->get();
        } catch (\Exception $e) {
            \Log::error('Failed to load categories', [
                'error' => $e->getMessage(),
            ]);
            $categories = collect();
        }

        $products = Product::query();

        if ($request->has('sort')) {
            if ($request->sort === 'price_asc') {
                $products->orderBy('price', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $products->orderBy('price', 'desc');
            } elseif ($request->sort === 'rating') {
                $products->orderBy('rating', 'desc');
            }
        }

        if ($request->has('min_price')) {
            $products->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $products->where('price', '<=', $request->max_price);
        }

        $products = $products->paginate(10);

        return view('main_page', compact('categories', 'products'));
    }

    /**
     * Отобразить список всех продуктов (для админ-панели).
     */
    public function adminIndex(Request $request)
    {
        \Log::info('ProductController::adminIndex accessed', [
            'user' => Auth::check() ? Auth::user()->email : 'guest',
            'is_admin' => Auth::check() ? Auth::user()->is_admin : 'not logged in',
        ]);

        $products = Product::query();

        if ($request->has('sort')) {
            if ($request->sort === 'price_asc') {
                $products->orderBy('price', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $products->orderBy('price', 'desc');
            } elseif ($request->sort === 'rating') {
                $products->orderBy('rating', 'desc');
            }
        }

        $products = $products->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Показать страницу конкретного продукта.
     */
    public function show(Product $product)
    {
        return view('product.show', compact('product'));
    }

    /**
     * Показать форму для создания нового продукта.
     */
    public function create()
    {
        $categories = Category::with('subcategories')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Сохранить новый продукт в базе данных.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'rating' => $request->rating,
            'image' => $imagePath,
            'subcategory_id' => $request->subcategory_id,
        ]);

        \Log::info('Product created', [
            'product_id' => $product->id,
            'name' => $product->name,
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Показать форму для редактирования продукта.
     */
    public function edit(Product $product)
    {
        $categories = Category::with('subcategories')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Обновить продукт в базе данных.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'rating' => $request->rating,
            'image' => $imagePath,
            'subcategory_id' => $request->subcategory_id,
        ]);

        \Log::info('Product updated', [
            'product_id' => $product->id,
            'name' => $product->name,
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Удалить продукт из базы данных.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        \Log::info('Product deleted', [
            'product_id' => $product->id,
            'name' => $product->name,
        ]);

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
