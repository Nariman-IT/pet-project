<?php

declare(strict_types=1);

namespace App\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

/**
 * @property-read int $id
 * @property-read int $user_id
 * @property-read string $status
 * @property-read string $delivery_address
 * @property-read float $full_price
 * @property-read \Illuminate\Support\Collection|\App\Order\Http\Resources\OrderItemResource[] $items
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
final class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'delivery_address' => $this->delivery_address,
            'full_price' => $this->full_price,
            'items' => OrderItemResource::collection($this->items),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}