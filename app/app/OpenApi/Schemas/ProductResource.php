<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     required={"id", "name", "price"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Смартфон Galaxy S23"),
 *     @OA\Property(property="price", type="number", format="float", example=59999.99),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T12:00:00Z")
 * )
 */
class ProductResource
{
}