<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductResource',
    required: ['id', 'name', 'price'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Смартфон Galaxy S23'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 59999.99),
        new OA\Property(property: 'description', type: 'string', example: 'Флагманский смартфон с отличной камерой', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'datetime', example: '2024-01-01T12:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'datetime', example: '2024-01-01T12:00:00Z')
    ]
)]
class ProductResource
{
}