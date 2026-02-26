<?php

namespace App\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Cart\Database\Factories\CartItemFactory;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';
    protected $fillable = ['cart_id', 'product_id', 'quantity'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    protected static function newFactory(): CartItemFactory
    {
        return CartItemFactory::new();
    }
}
