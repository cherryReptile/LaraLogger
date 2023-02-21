<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    public function find(Request $request)
    {
        return Response::json(UserResource::make(User::find($request->id)));
    }

    public function getAllWithSortAndFilter(Request $request)
    {
        $request->validate([
            'field' => 'required|string',
            'order_by' => 'required|string',
            'filter' => 'sometimes|array:login,created_at,updated_at,first_name,last_name,address'
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