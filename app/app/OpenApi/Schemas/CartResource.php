<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="CartResource",
 *     required={"id", "user_id", "items"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="id", type="integer", example=10),
 *             @OA\Property(property="product_id", type="integer", example=5),
 *             @OA\Property(property="quantity", type="integer", example=2),
 *             @OA\Property(
 *                 property="product",
 *                 ref="#/components/schemas/ProductResource"
 *             )
 *         )
 *     ),
 *     @OA\Property(property="total_price", type="number", format="float", example=119999.98)
 * )
 */
class CartResource
{
}