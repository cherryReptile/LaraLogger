<?php

namespace App\Services\Auth;

use App\Exceptions\AuthServiceException;
use App\Models\Provider;
use App\Models\ProvidersData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AppAuth extends AppAuthService
{
    protected Provider $provider;

    public function __construct()
    {
        $this->provider = Provider::where('provider', '=', 'app')->firstOrFail();
    }

    /**
     * @throws AuthServiceException
     */
    public function register(array $request): array
    {
        $providersData = ProvidersData::findByProviderIdAndUsername($this->provider->id, $request['email']);
        if ($providersData != null) {
            throw new AuthServiceException('this user already exists');
        }

        $user = User::create(['login' => $request['email']]);

        $data = [
            'email' => $request['email'],
            'password' => Hash::make($request['password'])
        ];

        $providersData = new ProvidersData();
        $providersData->addProviderWithData($user, $this->provider, $data);
        $user->profile()->create();

        $token = $user->createToken('api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * @throws AuthServiceException
     */
    public function login(array $request): array
    {
        $providersData = ProvidersData::findByProviderIdAndUsername($this->provider->id, $request['email']);
        if ($providersData === null) {
            throw new AuthServiceException('user not found');
        }

        $data = json_decode($providersData->data);
        if (!Hash::check($request['password'], $data->password)) {
            throw new AuthServiceException('invalid password');
        }

        $user = $providersData->user;
        $token = $user->createToken('api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * @throws AuthServiceException
     */
    public function addAccount(User $user, array $request): array
    {
        $pd = $user->providersData()->where('provider_id', '=', "{$this->provider->id}")->first();
        if ($pd != null) {
            throw new AuthServiceException('you already have account in app service');
        }

        $providersData = ProvidersData::findByProviderIdAndUsername($this->provider->id, $request['email']);
        if ($providersData != null) {
            throw new AuthServiceException('this user already exists');
        }
        $providersData = new ProvidersData();

        $data = [
            'email' => $request['request']['email'],
            'password' => Hash::make($request['request']['password'])
        ];

        $providersData->addProviderWithData($user, $this->provider, $data);

        return [
            'message' => 'account added successfully through app service'
        ];
    }
}