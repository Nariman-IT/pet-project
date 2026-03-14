<?php

namespace App\OpenApi\Endpoints;

use OpenApi\Attributes as OA;
use App\OpenApi\Schemas\OrderResource;

#[OA\Post(
    path: '/api/v1/orders',
    summary: 'Создать новый заказ',
    description: 'Оформляет заказ на основе текущей корзины пользователя. После успешного создания корзина очищается.',
    tags: ['Orders'],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['delivery_address'],
            properties: [
                new OA\Property(property: 'delivery_address', type: 'string', example: 'ул. Пушкина, д. 10, кв. 42')
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: 'Заказ успешно создан',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'order', ref: '#/components/schemas/OrderResource')
                ]
            )
        ),
        new OA\Response(response: 404, description: 'Корзина не найдена или пуста'),
        new OA\Response(response: 422, description: 'Ошибка валидации входящих данных'),
        new OA\Response(response: 500, description: 'Внутренняя ошибка сервера')
    ]
)]
class OrderEndpoints
{
}