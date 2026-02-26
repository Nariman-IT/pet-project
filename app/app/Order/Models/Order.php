<?php

namespace App\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Order\Database\Factories\OrderFactory;
use App\Models\User;

class Order extends Model
{
    use HasFactory;

    public const CREATED = 'created';
    public const PAID = 'paid';
    public const IN_PROGRESS = 'in_progress';
    public const DELIVERING = 'delivering';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'status',
        'delivery_address',
        'full_price',
    ];

    protected $casts = [
        'delivery_address' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(related: OrderItem::class);
    }

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }
}
