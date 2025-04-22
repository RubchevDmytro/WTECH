<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'stock', 'price', 'rating'];
    public $timestamps = false;


    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
public function getPrimaryImageAttribute()
{
    return $this->images()->where('is_primary', true)->first();
}
public function primaryImage()
{
    $image = $this->images()->where('is_primary', true)->first();
    if ($image) {
        return (object) [
            'image_data' => $image->image_data, // Исправлено с data на image_data
            'mime_type' => $image->mime_type,
        ];
    }
    return null;
}

}
