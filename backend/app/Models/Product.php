<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'left_stock', 'price', 'rating'];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage()
    {
        // Предположим, у вас есть связь с таблицей изображений
        $image = $this->images()->where('is_primary', true)->first();
        if ($image) {
            return (object) [
                'image_data' => $image->data, // Данные изображения (должны быть строкой, например, BLOB из базы данных)
                'mime_type' => $image->mime_type, // MIME-тип (например, 'image/jpeg')
            ];
        }
        return null;
    }
}
