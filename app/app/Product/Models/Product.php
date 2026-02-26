<?php

declare(strict_types=1);

namespace App\Product\Models;

use App\Product\Database\Factories\ProductFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property float $weight
 * @property string $category
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class Product extends Model
{
    use HasFactory;
    public const CATEGORY_PIZZA = 'pizza';
    public const CATEGORY_DRINK = 'drink';

    protected $table = 'products';

    protected $fillable = ['name', 'description', 'price', 'weight', 'category'];

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
