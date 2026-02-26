<?php

declare(strict_types=1);

namespace App\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Order\Http\Requests\CancelOrderRequest;
use App\Order\Http\Requests\UpdateOrderRequest;
use App\Order\Http\Requests\StoreOrderRequest;
use App\Order\Models\Order;
use App\Cart\Models\Cart;
use App\Order\Http\Resources\OrderResource;
use App\Order\Http\Resources\OrderCollection;

final class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = $request->get(key: 'page', default: 1);

        $orders = Order::where('user_id', auth()->id())
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'page', $page);

        return response()->json([
            'orders' => new OrderCollection($orders),
        ], Response::HTTP_OK);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $data = $request->validated();
        $cart = Cart::where('user_id', auth()->id())
            ->with('items.product')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            throw new ModelNotFoundException();
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'status' => Order::CREATED,
                'delivery_address' => $data,
                'full_price' => $cart->getTotalPrice(),
            ]);

            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;
                $price = (float) $product->price * $cartItem->quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $cartItem->quantity,
                    'price' => $price,
                ]);
            }

            $cart->items()->delete();
            DB::commit();
            return response()->json([
                'order' => new OrderResource($order->load('items')),
            ], Response::HTTP_CREATED);
            
            
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Ошибка при создание продукта', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }  
    }



    public function show(Order $order): JsonResponse
    {
        $order->load('items');
        return response()->json([
            'order' => new OrderResource($order),
        ]);
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'order' => new OrderResource($order->load('items')),
        ], Response::HTTP_OK);
    }


    public function cancel(CancelOrderRequest $request, Order $order): JsonResponse
    {
        if($order->status === 'cancelled') {
            return response()->json([
                'order' => new OrderResource($order->load('items')),
            ], Response::HTTP_OK);
        }

        $order->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'order' => new OrderResource($order->load('items')),
        ], Response::HTTP_OK);
    }

}