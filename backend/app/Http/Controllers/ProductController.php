<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;  
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
        try {
            $categories = Category::all();
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
            $categoryId = $request->query('category');
            $products->where('category_id', $categoryId);
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
        $products = Product::with('category');

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
        $products->appends($request->query());

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
        $categories = Category::all();
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
            'description' => 'required|string',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create([
            'description' => $request->description,
            'category_id' => $request->category_id,
            'stock' => $request->stock,
            'name' => $request->name,
            'price' => $request->price,
            'rating' => $request->rating,
        ]);

        // Обработка нескольких изображений
        if ($request->hasFile('images')) {
            $isPrimarySet = false;
            foreach ($request->file('images') as $index => $image) {
                $imageContent = base64_encode(file_get_contents($image->getRealPath()));
                $mimeType = $image->getClientMimeType();
                $isPrimary = $request->input("is_primary_{$index}", false) && !$isPrimarySet;

                if ($isPrimary) {
                    $isPrimarySet = true;
                    ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
                }

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_data' => $imageContent,
                    'mime_type' => $mimeType,
                    'is_primary' => $isPrimary,
                ]);

                // Простая индикация прогресса (текстовый вывод)
            }
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }
    
    /* Показать форму для редактирования продукта.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
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
            'description' => 'required|string',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);

        // Получаем все данные чекбоксов is_primary
        $isPrimaryData = $request->input('is_primary', []);

        // Обновляем is_primary для существующих изображений
        $existingImages = $product->images;
        foreach ($existingImages as $image) {
            $isPrimary = isset($isPrimaryData[$image->id]) && $isPrimaryData[$image->id] == '1';
            $image->update(['is_primary' => $isPrimary]);
        }

        // Если есть новые изображения, добавляем их
        if ($request->hasFile('images')) {
            $isPrimarySet = $existingImages->contains('is_primary', true);
            foreach ($request->file('images') as $index => $image) {
                if (!$image) continue; // Пропускаем пустые файлы

                $imageContent = base64_encode(file_get_contents($image->getRealPath()));
                $mimeType = $image->getClientMimeType();
                $isPrimary = isset($isPrimaryData[$index]) && $isPrimaryData[$index] == '1' && !$isPrimarySet;

                if ($isPrimary) {
                    $isPrimarySet = true;
                    $product->images()->update(['is_primary' => false]);
                }

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_data' => $imageContent,
                    'mime_type' => $mimeType,
                    'is_primary' => $isPrimary,
                ]);
            }
        }

        // Убедимся, что хотя бы одно изображение помечено как primary
        if (!$product->images()->where('is_primary', true)->exists() && $product->images()->count() > 0) {
            $firstImage = $product->images()->first();
            $firstImage->update(['is_primary' => true]);
        }

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

