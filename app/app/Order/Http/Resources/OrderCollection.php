<?php

declare(strict_types=1);

namespace App\Order\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

final class OrderCollection extends ResourceCollection
{
    /**
     * @var class-string
     */
    public $collects = OrderResource::class;
}
