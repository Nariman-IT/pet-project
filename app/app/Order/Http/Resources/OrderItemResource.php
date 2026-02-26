<?php

declare(strict_types=1);

namespace App\Order\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Product\Http\Resources\ProductResource;

final class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'product' => new ProductResource($this->product),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
