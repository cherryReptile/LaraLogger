<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\Auth\OAuth;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OAuthController extends Controller
{
    public function getUrl(Request $request): JsonResponse
    {
        $oauth = new OAuth($request->provider, null);
        $res = $oauth->getUrl();
        return Response::json($res);
    }

    public function getToken(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required'
        ]);

        $oauth = new OAuth($request->provider, $request->post());
        try {
            $res = $oauth->getToken();
        } catch (Exception $e) {
            return Response::json([
                'error' => $e->getMessage()
            ], 400);
        }
        return Response::json($res);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'access_token' => 'required'
        ]);

        $oauth = new OAuth($request->provider, $request->post());
        try {
            $res = $oauth->login();
        } catch (Exception $e) {
            return Response::json([
                'error' => $e->getMessage()
            ], 400);
        }
        return Response::json([
            'user' => UserResource::make($res['user']),
            'token' => $res['token']
        ]);
    }
}
