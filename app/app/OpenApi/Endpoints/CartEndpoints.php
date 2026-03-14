<?php

namespace App\OpenApi\Endpoints;

use OpenApi\Attributes as OA;
use App\OpenApi\Schemas\CartResource;

#[OA\Post(
    path: '/api/v1/cart',
    summary: 'Добавить товар в корзину',
    description: 'Добавляет указанное количество товара в корзину текущего пользователя. Если товар уже есть в корзине, увеличивает количество.',
    tags: ['Cart'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['product_id', 'quantity'],
            properties: [
                new OA\Property(property: 'product_id', type: 'integer', example: 5),
                new OA\Property(property: 'quantity', type: 'integer', example: 2)
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 200,
            description: 'Товар успешно добавлен в корзину',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'cart', ref: '#/components/schemas/CartResource')
                ]
            )
        ),
        new OA\Response(response: 400, description: 'Ошибка валидации правил корзины'),
        new OA\Response(response: 404, description: 'Товар не найден'),
        new OA\Response(response: 422, description: 'Ошибка валидации входящих данных'),
        new OA\Response(response: 500, description: 'Внутренняя ошибка сервера')
    ]
)]
class CartEndpoints
{
}