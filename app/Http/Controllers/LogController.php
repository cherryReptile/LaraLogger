<?php

namespace App\Http\Controllers;

use App\Exceptions\LogException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * @throws LogException
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'file_where_calling' => 'required|string',
            'file_where_defined' => 'sometimes|string',
            'data' => 'sometimes',
            'class' => 'sometimes|string',
            'changed_properties' => 'sometimes|array',
            'all_properties' => 'sometimes|array',
            'calling_on_line' => 'required|int'
        ]);

        $user = $request->user();
        $post = $request->post();
        $level = strtoupper((string)$request->level);

        match ($level) {
            'INFO', 'WARN', 'ERROR', 'FATAL' => null,
            default => throw new LogException('unknown log level')
        };

        $user->logs()->create([
            'file_where_calling' => $post['file_where_calling'],
            'data' => json_encode($post['data'] ?? null),
            'file_where_defined' => $post['file_where_defined'] ?? null,
            'class' => $post['class'] ?? null,
            'changed_properties' => json_encode($post['changed_properties'] ?? null),
            'all_properties' => json_encode($post['all_properties'] ?? null),
            'calling_line' => $post['calling_on_line'],
            'level' => $level
        ]);

        return Response::json(['message' => 'log created successfully', 'level' => $level]);
    }
}
