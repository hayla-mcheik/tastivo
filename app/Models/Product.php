<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
protected $table='products';
protected $fillable = [
    'category_id', 'name', 'desc', 'price', 'quantity', 'rate', 'image', 'status'
]; 
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function additions()
    {
        return $this->belongsToMany(Addition::class, 'product_additions');
    }
}
