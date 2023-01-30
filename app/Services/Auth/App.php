<?php

namespace App\Services\Auth;

use App\Models\Provider;
use App\Models\ProvidersData;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class App extends AppAuthService
{
    protected array $request;
    protected Provider $provider;

    public function __construct(array $request) {
        $this->request = $request;
        $this->provider = Provider::where('provider', '=', 'app')->firstOrFail();
    }

    /**
     * @throws Exception
     */
    public function register(): array
    {
        $providersData = ProvidersData::query()->select()->whereRaw("provider_id={$this->provider->id} and username='{$this->request['email']}'")->first();
        if ($providersData != null) {
            throw new Exception('this user already exists');
        }

        $user = User::create(['login' => $this->request['email']]);
        $user->providersData()->create([
            'data' => json_encode([
                'email' => $this->request['email'],
                'password' => Hash::make($this->request['password'])
            ]),
            'username' => $user->login,
            'provider_id' => $this->provider->id
        ]);

        DB::table('users_providers')->insert([
            'user_id' => $user->id,
            'provider_id' => $this->provider->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * @throws Exception
     */
    public function login(): array
    {
        $providersData = ProvidersData::query()->select()->whereRaw("provider_id={$this->provider->id} and username='{$this->request['email']}'")->first();
        if ($providersData === null) {
            throw new Exception('user not found');
        }

        $data = json_decode($providersData->data);
        if (!Hash::check($this->request['password'], $data->password)) {
            throw new Exception('invalid password');
        }

        $user = $providersData->user;
        $token = $user->createToken('api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
//    public function addAccount()
//    {
//        // TODO: Implement addAccount() method.
//    }
}