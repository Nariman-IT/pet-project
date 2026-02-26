<?php

declare(strict_types=1);

namespace App\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Cart\Models\Cart;
use App\Cart\Models\CartItem;
use App\Product\Models\Product;
use App\Cart\Http\Requests\StoreCartRequest;
use App\Cart\Http\Requests\UpdateCartItemRequest;
use App\Cart\Traits\CartTrait;
use App\Cart\Http\Resources\CartResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;


final class CartController extends Controller
{
    use CartTrait;

    public function index(): JsonResponse
    {
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $cart->load('items.product');

        return response()->json([
            'cart' => new CartResource($cart),
        ], Response::HTTP_OK);
    }


    public function store(StoreCartRequest $request): JsonResponse
    {
        $data = $request->validated();
        $product = Product::find($data['product_id']);
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $cartItem = CartItem::firstOrCreate(['cart_id' => $cart->id, 'product_id' => $product->id]);

        $oldQuantity = $cartItem->quantity ?? 0;
        $newQuantity = $oldQuantity + $data['quantity'];

        $this->validateCartRules($cart, $product, $data['quantity']);

        $cartItem->quantity = $newQuantity;
        $cartItem->save();
        $cart->load('items.product');

        return response()->json(['cart' => new CartResource($cart)], Response::HTTP_OK);
    }


    public function update(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        if($cart->id !== $cartItem->cart_id) {
            throw new ModelNotFoundException();
        }

        $oldQuantity = $cartItem->quantity ?? 0;
        $newQuantity = $oldQuantity + $request->quantity;

        $product = Product::find($cartItem->product_id);
        $this->validateCartRules($cart, $product, $request->quantity);
        $cartItem->update(['quantity' => $newQuantity]);

        return response()->json(['cart' => new CartResource($cart)], Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $cartItem = CartItem::findOrFail($id);

        if($cart->id !== $cartItem->cart_id) {
            throw new ModelNotFoundException();
        }
        
        $cartItem->delete();

        return response()->json([
            'id' => "$id",
        ], Response::HTTP_OK);
    }

    public function clear(): JsonResponse
    {
        $cart = Cart::where('user_id', auth()->id())->firstOrFail();
        $cart->items()->delete();
        return response()->json([
            'cart' => new CartResource($cart)
        ], Response::HTTP_OK);
    }
}
