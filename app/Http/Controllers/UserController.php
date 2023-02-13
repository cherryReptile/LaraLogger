<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UserController extends CustomController
{
    public function getAllWithSortAndFilter(Request $request)
    {
        $request->validate([
            'field' => 'required|string',
            'order_by' => 'required|string',
            'filter' => 'sometimes|array'
        ]);
        $users = new User();
        if (!isset($request['filter'])) {
            $users = $users->orderBy($request['field'], $request['order_by'])->get();
        } else {
            $users = $users->getClientUsersWithFilterAndOrderBy($request->post());
        }
        return Response::json(UserResource::collection($users));
    }
}