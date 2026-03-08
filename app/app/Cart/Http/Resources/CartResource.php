<?php

namespace App\Cart\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Cart\Models\Cart
 * @property \App\Cart\Models\Cart $resource
 */
class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'items' => CartItemResource::collection($this->items),
            'total_price' => $this->getTotalPrice(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
