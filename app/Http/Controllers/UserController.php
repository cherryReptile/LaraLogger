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
        try {
            $user = new User();
            if (isset($request['filter'])) {
                $users = $user->getClientUsersWithFilterAndOrderBy($request->post());
            } else {
                $users = $user->joinProfile($request['field'], $request['order_by'])->get();
            }
        } catch (CustomException $e) {
            return $this->responseErrorFromException($e, 400);
        }
        if (!isset($users[0])) {
            return $this->responseError("users not found for this filter", 400);
        }
        return Response::json(UserResource::collection($users));
    }
}