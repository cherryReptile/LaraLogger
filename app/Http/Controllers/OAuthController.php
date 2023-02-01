<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthServiceException;
use App\Http\Resources\UserResource;
use App\Services\Auth\OAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OAuthController extends CustomController
{
    public function getUrl(Request $request): JsonResponse
    {
        try {
            $oauth = new OAuth($request->provider, null);
        } catch (AuthServiceException $e) {
            return $this->responseError($e, 400);
        }
        $res = $oauth->getUrl();
        return Response::json($res);
    }

    public function getToken(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required'
        ]);

        try {
            $oauth = new OAuth($request->provider, $request->post());
            $res = $oauth->getToken();
        } catch (AuthServiceException $e) {
            return $this->responseError($e, 400);
        }
        return Response::json($res);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'access_token' => 'required'
        ]);

        try {
            $oauth = new OAuth($request->provider, $request->post());
            $res = $oauth->login();
        } catch (AuthServiceException $e) {
            return $this->responseError($e, 400);
        }
        return Response::json([
            'user' => UserResource::make($res['user']),
            'token' => $res['token']
        ]);
    }

    public function addAccount(Request $request)
    {
        $request->validate(['access_token' => 'required']);

        try {
            $oauth = new OAuth($request->provider, [
                'user' => $request->user(),
                'request' => $request->post()
            ]);
            $res = $oauth->addAccount();
        } catch (AuthServiceException $e) {
            return $this->responseError($e, 400);
        }

        return Response::json([
            'message' => $res['message']
        ]);
    }
}
