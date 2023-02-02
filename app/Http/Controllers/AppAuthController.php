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
    public function register(AppUserRequest $request): JsonResponse
    {
        $app = new App($request->post());

        try {
            $res = $app->register();
        } catch (AuthServiceException $e) {
            return $this->responseErrorFromException($e, 400);
        }

        return Response::json([
            'user' => UserResource::make($res['user']),
            'token' => $res['token']
        ], 201);
    }

    public function login(AppUserRequest $request): JsonResponse
    {
        $app = new App($request->post());

        try {
            $res = $app->login();
        } catch (AuthServiceException $e) {
            return $this->responseErrorFromException($e, 400);
        }

        return Response::json([
            'user' => UserResource::make($res['user']),
            'token' => $res['token']
        ]);
    }

    public function addAccount(AppUserRequest $request): JsonResponse
    {
        $user = $request->user();
        $app = new App(['user' => $user, 'request' => $request->post()]);

        try {
            $res = $app->addAccount();
        } catch (AuthServiceException $e) {
            return $this->responseErrorFromException($e, 400);
        }

        return Response::json([
           'message' => $res['message']
        ]);
    }
}
