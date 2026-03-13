<?php

namespace App\OpenApi\Endpoints;

use OpenApi\Attributes as OA;
use App\OpenApi\Schemas\ProductResource;

#[OA\Get(
    path: '/api/v1/products',
    summary: 'Получить список товаров с пагинацией',
    description: 'Возвращает постраничный список товаров (по 10 на страницу). Результаты кэшируются.',
    tags: ['Products'],
    parameters: [
        new OA\Parameter(
            name: 'page',
            in: 'query',
            description: 'Номер страницы для пагинации',
            required: false,
            schema: new OA\Schema(type: 'integer', default: 1, example: 2)
        )
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Успешный ответ со списком товаров',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'data',
                        type: 'array',
                        items: new OA\Items(ref: '#/components/schemas/ProductResource')
                    ),
                    new OA\Property(
                        property: 'links',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'first', type: 'string', example: 'http://localhost/api/v1/products?page=1'),
                            new OA\Property(property: 'last', type: 'string', example: 'http://localhost/api/v1/products?page=10'),
                            new OA\Property(property: 'prev', type: 'string', example: 'null'),
                            new OA\Property(property: 'next', type: 'string', example: 'http://localhost/api/v1/products?page=2')
                        ]
                    ),
                    new OA\Property(
                        property: 'meta',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'current_page', type: 'integer', example: 1),
                            new OA\Property(property: 'from', type: 'integer', example: 1),
                            new OA\Property(property: 'last_page', type: 'integer', example: 10),
                            new OA\Property(property: 'per_page', type: 'integer', example: 10),
                            new OA\Property(property: 'to', type: 'integer', example: 10),
                            new OA\Property(property: 'total', type: 'integer', example: 100)
                        ]
                    )
                ]
            )
        ),
        new OA\Response(response: 500, description: 'Внутренняя ошибка сервера')
    ]
)]
class ProductEndpoints
{
}