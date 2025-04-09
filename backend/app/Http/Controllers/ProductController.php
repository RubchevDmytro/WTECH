<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('products')->get();

        $productsQuery = Product::query();

        // Фильтр по категории
        if ($category = $request->query('category')) {
            $productsQuery->where('category_id', $category);
        }

        // Поиск
        if ($search = $request->query('search')) {
            $productsQuery->where('name', 'like', "%{$search}%");
        }

        // Фильтр по цене
        $minPrice = $request->query('min_price', 0);
        $maxPrice = $request->query('max_price', 10000);
        $productsQuery->whereBetween('price', [$minPrice, $maxPrice]);

        // Сортировка
        $sort = $request->query('sort');
        if ($sort == 'price_asc') {
            $productsQuery->orderBy('price', 'asc');
        } elseif ($sort == 'price_desc') {
            $productsQuery->orderBy('price', 'desc');
        } elseif ($sort == 'rating') {
            $productsQuery->orderBy('rating', 'desc');
        }

        $products = $productsQuery->paginate(10);

        return view('main_page', compact('categories', 'products'));
    }

    public function show(Product $product)
    {
        return view('product', compact('product'));
    }
}
