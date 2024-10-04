<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'regular_price',
        'sale_price',
        'SKU',
        'stock_status',
        'featured',
        'quantity',
        'critical_level',
        'image',
        'images',
        'category_id',
        'brand_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'related_id');
    }

    public function toggleStatus()
    {
        $this->is_active = !$this->is_active;
        $this->save();
    }


public function reduceStock($quantity)
{
    $this->quantity -= $quantity; 
    $this->save(); 
}


}
