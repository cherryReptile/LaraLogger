<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthServiceException;
use App\Http\Requests\AppUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\App;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;

class AppAuthController extends CustomController
{
    /**
     * @throws AuthServiceException
     */
    public function register(AppUserRequest $request): JsonResponse
    {
        $app = new App($request->post());
        $res = $app->register();

        return Response::json([
            'user' => UserResource::make($res['user']),
            'token' => $res['token']
        ], 201);
    }

    /**
     * @throws AuthServiceException
     */
    public function login(AppUserRequest $request): JsonResponse
    {
        $app = new App($request->post());
        $res = $app->login();

        return Response::json([
            'user' => UserResource::make($res['user']),
            'token' => $res['token']
        ]);
    }

    /**
     * @throws AuthServiceException
     */
    public function addAccount(AppUserRequest $request): JsonResponse
    {
        $user = $request->user();
        $app = new App(['user' => $user, 'request' => $request->post()]);
        $res = $app->addAccount();

        return Response::json([
            'message' => $res['message']
        ]);
    }
}
