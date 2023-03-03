<?php

namespace App\Http\Controllers;

use App\Models\LogLevel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\New_;

class LogController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $user = $request->user();
        $post = $request->post();
        $level = strtoupper((string)$request->level);
        $user->logs()->create([
            'file' => $post['file'],
            'class' => $post['class'],
            'changed_properties' => json_encode($post['changed_properties']),
            'all_properties' => json_encode($post['all_properties']),
            'calling_line' => $post['calling_on_line'],
            'level' => $level
        ]);
        return Response::json(['message' => 'log created successfully', 'level' => $level]);
    }
}
