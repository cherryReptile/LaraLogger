<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthServiceException;
use App\Http\Resources\UserResource;
use App\Services\Auth\OAuth;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OAuthController extends CustomController
{
    /**
     * @throws AuthServiceException
     */
    public function getUrl(Request $request): JsonResponse
    {
        $oauth = new OAuth($request->provider, null);
        $res = $oauth->getUrl();
        return Response::json($res);
    }

    /**
     * @throws AuthServiceException|GuzzleException
     */
    public function getToken(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required'
        ]);

        $oauth = new OAuth($request->provider, $request->post());
        $res = $oauth->getToken();

        return Response::json($res);
    }

    /**
     * @throws AuthServiceException
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'access_token' => 'required'
        ]);
        $oauth = new OAuth($request->provider, $request->post());
        $res = $oauth->login();
        return Response::json([
            'user' => UserResource::make($res['user']),
            'token' => $res['token']
        ]);
    }

    /**
     * @throws AuthServiceException
     */
    public function addAccount(Request $request)
    {
        $request->validate(['access_token' => 'required']);
        $oauth = new OAuth($request->provider, [
            'user' => $request->user(),
            'request' => $request->post()
        ]);
        $res = $oauth->addAccount();

        return Response::json([
            'message' => $res['message']
        ]);
    }
}
