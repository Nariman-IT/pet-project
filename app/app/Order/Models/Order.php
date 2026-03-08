<?php

namespace App\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Order\Database\Factories\OrderFactory;
use App\Models\User;

/**
 * @property int $id
 * @property int $user_id
 * @property string $status
 * @property string $delivery_address
 * @property float $full_price
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order\Models\OrderItem[] $items
 * @property-read User $user
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read int|null $items_count
 * @method static \App\Order\Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereFullPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
 * @mixin \Eloquent
 */

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
