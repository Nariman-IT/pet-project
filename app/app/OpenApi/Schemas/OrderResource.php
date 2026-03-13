<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'OrderResource',
    required: ['id', 'user_id', 'status', 'delivery_address', 'full_price', 'items'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 100),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'status', type: 'string', example: 'created'),
        new OA\Property(property: 'delivery_address', type: 'string', example: 'ул. Пушкина, д. 10, кв. 42'),
        new OA\Property(property: 'full_price', type: 'number', format: 'float', example: 119999.98),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(
                required: ['id', 'product_id', 'product_name', 'quantity', 'price'],
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 50),
                    new OA\Property(property: 'product_id', type: 'integer', example: 5),
                    new OA\Property(property: 'product_name', type: 'string', example: 'Смартфон Galaxy S23'),
                    new OA\Property(property: 'quantity', type: 'integer', example: 2),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 119999.98)
                ]
            )
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'datetime', example: '2024-01-01T12:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'datetime', example: '2024-01-01T12:00:00Z')
    ]
)]
class OrderResource
{
}