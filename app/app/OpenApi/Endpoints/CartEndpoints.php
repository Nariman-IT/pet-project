<?php

namespace App\OpenApi\Endpoints;

use App\OpenApi\Schemas\CartResource;

/**
 * @OA\Post(
 *     path="/api/v1/cart",
 *     summary="Добавить товар в корзину",
 *     description="Добавляет указанное количество товара в корзину текущего пользователя. Если товар уже есть в корзине, увеличивает количество.",
 *     tags={"Cart"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"product_id", "quantity"},
 *             @OA\Property(
 *                 property="product_id",
 *                 type="integer",
 *                 example=5,
 *                 description="ID товара для добавления"
 *             ),
 *             @OA\Property(
 *                 property="quantity",
 *                 type="integer",
 *                 example=2,
 *                 description="Количество товара (должно быть положительным)"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Товар успешно добавлен в корзину",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="cart",
 *                 ref="#/components/schemas/CartResource"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Ошибка валидации правил корзины (например, превышен лимит)"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Товар не найден"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации входящих данных"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера"
 *     )
 * )
 */
class CartEndpoints
{
}