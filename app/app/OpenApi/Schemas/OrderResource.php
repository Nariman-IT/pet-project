<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="OrderResource",
 *     required={"id", "user_id", "status", "delivery_address", "full_price", "items"},
 *     @OA\Property(property="id", type="integer", example=100),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="created"),
 *     @OA\Property(property="delivery_address", type="string", example="ул. Пушкина, д. 10, кв. 42"),
 *     @OA\Property(property="full_price", type="number", format="float", example=119999.98),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=50),
 *             @OA\Property(property="product_id", type="integer", example=5),
 *             @OA\Property(property="product_name", type="string", example="Смартфон Galaxy S23"),
 *             @OA\Property(property="quantity", type="integer", example=2),
 *             @OA\Property(property="price", type="number", format="float", example=119999.98)
 *         )
 *     ),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2024-01-01T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2024-01-01T12:00:00Z")
 * )
 */
class OrderResource
{
}