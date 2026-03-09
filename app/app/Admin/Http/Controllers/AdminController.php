<?php

namespace App\Admin\Http\Controllers;

use App\Admin\Http\Resources\AdminResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin\Http\Requests\AdminUpdateRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $users = User::paginate(10, ['*'], 'page', $page);

        return response()->json([
                'users' => AdminResource::collection($users),
            ], Response::HTTP_OK);
    }


    public function update(int $id, AdminUpdateRequest $request): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());
        
        return response()->json([
                'users' => new AdminResource($user),
            ], Response::HTTP_OK);
    }
}
