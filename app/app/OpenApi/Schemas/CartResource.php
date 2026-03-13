<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CartResource',
    required: ['id', 'user_id', 'items'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(
                required: ['id', 'product_id', 'quantity'],
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 10),
                    new OA\Property(property: 'product_id', type: 'integer', example: 5),
                    new OA\Property(property: 'quantity', type: 'integer', example: 2),
                    new OA\Property(property: 'product', ref: '#/components/schemas/ProductResource')
                ]
            )
        ),
        new OA\Property(property: 'total_price', type: 'number', format: 'float', example: 119999.98)
    ]
)]
class CartResource
{
}