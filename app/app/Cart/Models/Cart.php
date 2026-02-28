<?php

namespace App\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Cart\Database\Factories\CartFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Product\Models\Product;
use Illuminate\Database\Eloquent\Collection;


/**
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Collection<int, CartItem> $items
 * @property-read User $user
 * @property-read int|null $items_count
 * @method static \App\Cart\Database\Factories\CartFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cart whereUserId($value)
 * @mixin \Eloquent
 */
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
        
        /** @var Collection<int, CartItem> $items */
        $items = $this->items;

        return $items->sum(function (CartItem $item): float {
            /** @var Product $product */
            $product = $item->product;
            return $item->quantity * $product->price;
        });
    }

    protected static function newFactory(): CartFactory
    {
        return CartFactory::new();
    }

}
