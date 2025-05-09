<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
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

$minPrice = Product::min('price') ?? 0;
$maxPrice = Product::max('price') ?? 1000;
    $products = Product::query();

    if ($request->has('category')) {
        $products->whereHas('subcategory', function ($q) use ($request) {
            $q->where('slug', $request->query('category'));
        });
    }

    // Обрабатываем min_price и max_price с запятой
    $minPriceInput = $request->has('min_price') ? str_replace(',', '.', $request->min_price) : $minPrice;
    $maxPriceInput = $request->has('max_price') ? str_replace(',', '.', $request->max_price) : $maxPrice;

    $minPriceInput = (float) $minPriceInput;
    $maxPriceInput = (float) $maxPriceInput;

    $products->where('price', '>=', $minPriceInput);
    $products->where('price', '<=', $maxPriceInput);

    // Логируем значения для отладки
    \Log::info('Price filter applied', [
        'min_price' => $minPriceInput,
        'max_price' => $maxPriceInput,
    ]);

    if ($request->has('search')) {
        $search = trim($request->query('search'));
        $search = str_replace(['%', '_', '\\'], ['\%', '\_', '\\\\'], $search);
        if ($search) {
            $products->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }
    }

    if ($request->has('sort')) {
        if ($request->sort === 'price_asc') {
            $products->orderBy('price', 'asc');
        } elseif ($request->sort === 'price_desc') {
            $products->orderBy('price', 'desc');
        } elseif ($request->sort === 'rating') {
            $products->orderBy('rating', 'desc');
        }
    }

    $perPage = $request->query('per_page', 12);
    $products = $products->paginate($perPage);
    $products->appends($request->query());

    return view('main_page', compact('categories', 'products', 'minPrice', 'maxPrice'));
}public function autocomplete(Request $request)
{
    $search = trim($request->query('search'));
    if (!$search) {
        return response()->json([]);
    }

    // Экранируем специальные символы для LIKE
    $search = str_replace(['%', '_', '\\'], ['\%', '\_', '\\\\'], $search);

    $suggestions = Product::query()
        ->where('name', 'ILIKE', "%{$search}%")
        ->orWhere('description', 'ILIKE', "%{$search}%")
        ->select('name')
        ->distinct()
        ->take(5) // Ограничиваем количество предложений
        ->get()
        ->pluck('name')
        ->toArray();

    \Log::info('Autocomplete suggestions', [
        'search' => $search,
        'suggestions' => $suggestions,
    ]);

    return response()->json($suggestions);
}

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

    $product = Product::create([
        'description'=> 'sadasda',
        'category_id' => 1,
        'stock'=>10,
        'name' => $request->name,
        'price' => $request->price,
        'rating' => $request->rating,
        'subcategory_id' => $request->subcategory_id,
    ]);

    // Если есть изображение, сохраняем его в таблице product_images
    if ($request->hasFile('image')) {
        $imageContent = base64_encode(file_get_contents($request->file('image')->getRealPath()));
        $mimeType = $request->file('image')->getClientMimeType();

        ProductImageController::create([
            'product_id' => $product->id,
            'image_data' => $imageContent,
            'mime_type' => $mimeType,
            'is_primary' => true, // Первое изображение становится основным
        ]);
    }

    return redirect()->route('products.index')->with('success', 'Product created successfully.');
     }
    /* Показать форму для редактирования продукта.
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

    $product->update([
        'name' => $request->name,
        'price' => $request->price,
        'rating' => $request->rating,
        'subcategory_id' => $request->subcategory_id,
    ]);

    // Если загружено новое изображение, добавляем его в таблицу product_images
    if ($request->hasFile('image')) {
        $imageContent = base64_encode(file_get_contents($request->file('image')->getRealPath()));
        $mimeType = $request->file('image')->getClientMimeType();

        // Делаем новое изображение основным, сбрасываем флаг у старых
        $product->images()->update(['is_primary' => false]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_data' => $imageContent,
            'mime_type' => $mimeType,
            'is_primary' => true,
        ]);
    }

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
    // Изображения удалятся автоматически благодаря onDelete('cascade') в миграции
    $product->delete();

    \Log::info('Product deleted', [
        'product_id' => $product->id,
        'name' => $product->name,
    ]);

    return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
}}

