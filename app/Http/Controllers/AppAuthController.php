<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;;
use App\Services\Auth\App;
use Response;
use Illuminate\Http\JsonResponse;

class AppAuthController extends Controller
{
    public function register(CreateUserRequest $request): JsonResponse
    {
        $app = new App();
        $user = $app->register($request->post());

        if ($user === null) {
            return Response::json([
               'error' => 'This user already exists'
            ], 400);
        }

        $token = $user->createToken('api')->plainTextToken;

        return Response::json([
            'user' => UserResource::make($user),
            'token' => $token
        ], 201);
    }
}
