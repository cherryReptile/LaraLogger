<?php

namespace App\Services\Auth;

use App\Models\Provider;
use App\Models\ProvidersData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class App extends AppAuthService
{
    public function register(array $fields): User|null
    {
        $provider = Provider::where('provider', '=', 'app')->first();
        $providersData = ProvidersData::query()->select()->whereRaw("provider_id={$provider->id} and username='{$fields['email']}'")->first();
        if ($providersData != null) {
            return null;
        }

        $user = User::create(['login' => $fields['email']]);
        $user->providersData()->create([
            'data' => json_encode([
                'email' => $fields['email'],
                'password' => Hash::make($fields['password'])
            ]),
            'username' => $user->login,
            'provider_id' => $provider->id
        ]);

        DB::table('users_providers')->insert([
            'user_id' => $user->id,
            'provider_id' => $provider->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $user;
    }
//    public function login()
//    {
//        // TODO: Implement login() method.
//    }
//    public function addAccount()
//    {
//        // TODO: Implement addAccount() method.
//    }
}