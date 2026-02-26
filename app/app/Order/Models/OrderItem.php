<?php

declare(strict_types=1);

namespace App\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Product\Models\Product;

final class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(related: Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(related: Product::class);
    }
}
