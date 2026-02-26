<?php

namespace App\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Cart\Database\Factories\CartFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';
    protected $fillable = ['user_id'];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function pizzaCount(): int
    {
        $query = $this->items()
            ->whereHas('product', fn($q) => $q->where('category', 'pizza'));

        return $query->sum('quantity');
    }

    public function drinkCount(): int
    {
        $query = $this->items()
            ->whereHas('product', fn($q) => $q->where('category', 'drink'));

        return $query->sum('quantity');
    }

    public function getTotalPrice(): float
    {
        $this->loadMissing('items.product');

        return $this->items->sum(
            fn(CartItem $item) => $item->quantity * $item->product->price
        );
    }

    protected static function newFactory(): CartFactory
    {
        return CartFactory::new();
    }

}
