<?php

namespace App\OpenApi\Endpoints;

use App\OpenApi\Schemas\ProductResource;

/**
 * @OA\Get(
 *     path="/api/v1/products",
 *     summary="Получить список товаров с пагинацией",
 *     description="Возвращает постраничный список товаров (по 10 на страницу). Результаты кэшируются.",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Номер страницы для пагинации",
 *         required=false,
 *         @OA\Schema(
 *             type="integer",
 *             default=1,
 *             example=2
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Успешный ответ со списком товаров",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="products",
 *                 type="object",
 *                 @OA\Property(
 *                     property="data",
 *                     type="array",
 *                     @OA\Items(ref="#/components/schemas/ProductResource")
 *                 ),
 *                 @OA\Property(property="links", type="object"),
 *                 @OA\Property(property="meta", type="object")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Внутренняя ошибка сервера"
 *     )
 * )
 */
class ProductEndpoints
{
}