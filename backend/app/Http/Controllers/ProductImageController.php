<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function showUploadForm()
    {
        $products = Product::all();
        return view('product.upload_image', compact('products'));
    }

public function uploadImage(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    try {
        $product = Product::findOrFail($request->product_id);

        // Читаем содержимое файла и кодируем в base64
        $imageContent = base64_encode(file_get_contents($request->file('image')->getRealPath()));
        $mimeType = $request->file('image')->getClientMimeType();

        // Если это первое изображение или мы хотим сделать его основным
        $isPrimary = $request->has('is_primary') ? true : ($product->images()->count() === 0);

        // Если новое изображение основное, сбрасываем флаг is_primary у других изображений
        if ($isPrimary) {
            $product->images()->update(['is_primary' => false]);
        }

        // Сохраняем изображение в таблице product_images
        ProductImage::create([
            'product_id' => $product->id,
            'image_data' => $imageContent, // Сохраняем как base64-строку
            'mime_type' => $mimeType,
            'is_primary' => $isPrimary,
        ]);

        return redirect()->route('product.upload_image')
            ->with('success', 'Image uploaded successfully for product: ' . $product->name);
    } catch (\Exception $e) {
        return redirect()->route('product.upload_image')
            ->with('error', 'Failed to upload image: ' . $e->getMessage());
    }
}



}
