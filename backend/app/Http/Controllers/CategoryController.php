<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Отобразить список всех категорий.
     */
    public function index()
    {
        $categories = Category::with('subcategories')->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Показать форму для создания новой категории.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Сохранить новую категорию в базе данных.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        \Log::info('Category created', [
            'category_id' => $category->id,
            'name' => $category->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Показать форму для редактирования категории.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Обновить категорию в базе данных.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        \Log::info('Category updated', [
            'category_id' => $category->id,
            'name' => $category->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Удалить категорию из базы данных.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        \Log::info('Category deleted', [
            'category_id' => $category->id,
            'name' => $category->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
