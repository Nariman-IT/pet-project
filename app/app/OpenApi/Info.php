<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'API моего магазина',
    version: '1.0.0',
    description: 'Документация для API корзины и товаров'
)]
#[OA\Server(
    url: 'http://localhost',
    description: 'Локальный сервер'
)]
#[OA\Tag(name: 'Products', description: 'Всё, что связано с товарами')]
#[OA\Tag(name: 'Cart', description: 'Управление корзиной')]
#[OA\Tag(name: 'Orders', description: 'Оформление заказов')]
class Info
{
}