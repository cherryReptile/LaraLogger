<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthServiceException;
use App\Http\Resources\UserResource;
use App\Services\Auth\OAuth;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OAuthController extends Controller
{
    protected OAuth $oauth;

    /**
     * @throws AuthServiceException
     */
    public function __construct(Request $request, OAuth $oauth)
    {
        $oauth->index($request->provider);
        $this->oauth = $oauth;
    }

    public function getUrl(Request $request): JsonResponse
    {
        $res = $this->oauth->getUrl();
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

        $res = $this->oauth->getToken($request->post());

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

        $res = $this->oauth->login($request->post());
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
        $res = $this->oauth->addAccount($request->user(), $request->post());

        return Response::json([
            'message' => $res['message']
        ]);
    }
}
