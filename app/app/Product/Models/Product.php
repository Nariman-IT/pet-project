<?php

declare(strict_types=1);

namespace App\Product\Models;

use App\Product\Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property float $weight
 * @property string $category
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
*/
final class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = ['name', 'description', 'price', 'weight', 'category'];

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }
}
