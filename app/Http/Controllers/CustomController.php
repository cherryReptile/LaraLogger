<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class CustomController extends Controller
{
    public function responseError(Exception $e, int $code): JsonResponse
    {
        return Response::json([
            'error' => $e->getMessage()
        ], $code);
    }
}