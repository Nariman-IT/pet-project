<?php

namespace App\Auth\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Auth\Http\Requests\RegisterRequest;
use App\Auth\Http\Requests\LoginRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Auth\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;


class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse 
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], Response::HTTP_CREATED);
    }



    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'token' => $token
        ], Response::HTTP_OK);
    }



    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json([
            'message' => 'Logged out'
        ], Response::HTTP_OK);
    }
}
