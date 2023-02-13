<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class LogoutController extends Controller
{
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return Response::json([
            'message' => 'logged out successfully'
        ]);
    }
}
