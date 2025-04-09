<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('ProductController::index accessed', [
//            'user' => Auth::check() ? Auth::user()->email : 'guest',
//            'is_admin' => Auth::check() ? Auth::user()->is_admin : 'not logged in',
        ]);

        try {
            $categories = Category::with('subcategories')->get();
        } catch (\Exception $e) {
            \Log::error('Failed to load categories', [
                'error' => $e->getMessage(),
            ]);
            $categories = collect(); // Пустая коллекция в случае ошибки
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
    public function create()
    {
        return view('admin.product_form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'rating' => 'required|numeric|min:0|max:5',
            'category_id' => 'required|exists:categories,id',
            'left_stock' => 'required|integer|min:0',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'rating' => $validated['rating'],
            'category_id' => $validated['category_id'],
            'left_stock' => $validated['left_stock'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $imageData = file_get_contents($image->getRealPath());
                $mimeType = $image->getMimeType();

                $product->images()->create([
                    'image_data' => $imageData,
                    'mime_type' => $mimeType,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Produkt bol úspešne pridaný.');
    }

    public function edit(Product $product)
    {
        return view('admin.product_form', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'rating' => 'required|numeric|min:0|max:5',
            'category_id' => 'required|exists:categories,id',
            'left_stock' => 'required|integer|min:0',
            'images' => 'sometimes|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'rating' => $validated['rating'],
            'category_id' => $validated['category_id'],
            'left_stock' => $validated['left_stock'],
        ]);

        if ($request->hasFile('images')) {
            $product->images()->delete();

            foreach ($request->file('images') as $index => $image) {
                $imageData = file_get_contents($image->getRealPath());
                $mimeType = $image->getMimeType();

                $product->images()->create([
                    'image_data' => $imageData,
                    'mime_type' => $mimeType,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Produkt bol úspešne aktualizovaný.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['success' => true]);
    }
}
