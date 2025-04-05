<?php 
namespace App\Services;

use App\Models\Product;

class CartService
{
const MAX_QUANTITY_PER_PRODUCT = 10;

public function addToCart($productId , $quantity = 1 , $userId = null)
{
$product = Product::findOrFail($productId);

//check product availability
if($product->quantity < $quantity)
{
    throw new \Exception('Not enough stock available');
}

}

}