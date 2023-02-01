<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Response;

class ProfileController extends Controller
{
    public function get(Request $request)
    {
        return Response::make([
            'user' => UserResource::make($request->user())
        ]);
    }

    public function update(Request $request)
    {
        $profile = $request->user()->profile()->firstOrFail();
        $fields = $request->post();
        if (isset($fields['other_data'])) {
            $fields['other_data'] = json_encode($fields['other_data']);
        }
        $profile->update($fields);
        return Response::make([
            'user' => UserResource::make($request->user())
        ]);
    }
}