<?php

namespace App\OpenApi\Endpoints;

use App\OpenApi\Schemas\OrderResource;

/**
 * @OA\Post(
 *     path="/api/v1/orders",
 *     summary="Создать новый заказ",
 *     description="Оформляет заказ на основе текущей корзины пользователя. После успешного создания корзина очищается.",
 *     tags={"Orders"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"delivery_address"},
 *             @OA\Property(
 *                 property="delivery_address",
 *                 type="string",
 *                 example="ул. Пушкина, д. 10, кв. 42",
 *                 description="Адрес доставки заказа"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Заказ успешно создан",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="order",
 *                 ref="#/components/schemas/OrderResource"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Корзина не найдена или пуста",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="No query results for model...")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Ошибка валидации входящих данных"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера при создании заказа",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation...")
 *         )
 *     )
 * )
 */
class OrderEndpoints
{
}