<?php

declare(strict_types=1);

namespace App\Product\Models;

use App\Product\Database\Factories\ProductFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Product\Models\Traits\SyncsWithElasticsearch;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property float $weight
 * @property string $category
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method static \App\Product\Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereWeight($value)
 * @mixin \Eloquent
 */
final class Product extends Model
{
    use HasFactory;
    use SyncsWithElasticsearch;
    
    public const CATEGORY_PIZZA = 'pizza';
    public const CATEGORY_DRINK = 'drink';

    protected $table = 'products';

    protected $fillable = ['name', 'description', 'price', 'weight', 'category'];

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    protected static function booted(): void
    {
        self::saved(callback: static function (): void {
            Cache::tags(['products'])->flush();
        });

        self::updated(callback: static function (): void {
            Cache::tags(['products'])->flush();
        });

        self::deleted(callback: static function (): void {
            Cache::tags(['products'])->flush();
        });
    }
}
