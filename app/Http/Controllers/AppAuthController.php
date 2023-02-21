<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthServiceException;
use App\Http\Requests\AppUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AppAuth;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;

class AppAuthController extends Controller
{
    protected AppAuth $auth;

    public function __construct(AppAuth $auth)
    {
        $this->auth = $auth;
    }
    /**
     * @throws AuthServiceException
     */
    public function register(AppUserRequest $request): JsonResponse
    {
        $res = $this->auth->register($request->post());

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
        $res = $this->auth->login($request->post());

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
        $res = $this->auth->addAccount($user, $request->post());

        return Response::json([
            'message' => $res['message']
        ]);
    }
}
