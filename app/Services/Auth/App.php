<?php

namespace App\Services\Auth;

use App\Models\Provider;
use App\Models\ProvidersData;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class App extends AppAuthService
{
    protected array $request;
    protected Provider $provider;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->provider = Provider::where('provider', '=', 'app')->firstOrFail();
    }

    /**
     * @throws Exception
     */
    public function register(): array
    {
        $providersData = ProvidersData::findByProviderIdAndUsername($this->provider->id, $this->request['email']);
        if ($providersData != null) {
            throw new Exception('this user already exists');
        }

        $user = User::create(['login' => $this->request['email']]);

        $data = [
            'email' => $this->request['email'],
            'password' => Hash::make($this->request['password'])
        ];

        $providersData = new ProvidersData();
        $providersData->addProviderWithData($user, $this->provider, $data);

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
        $providersData = ProvidersData::findByProviderIdAndUsername($this->provider->id, $this->request['email']);
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

    /**
     * @throws Exception
     */
    public function addAccount(): array
    {
        $user = $this->request['user'];
        $request = $this->request['request'];
        $pd = $user->providersData()->where('provider_id', '=', "{$this->provider->id}")->first();
        if ($pd != null) {
            throw new Exception('you already have account in app service');
        }

        $providersData = ProvidersData::findByProviderIdAndUsername($this->provider->id, $request['email']);
        if ($providersData != null) {
            throw new Exception('this user already exists');
        }
        $providersData = new ProvidersData();

        $data = [
            'email' => $this->request['request']['email'],
            'password' => Hash::make($this->request['request']['password'])
        ];

        $providersData->addProviderWithData($user, $this->provider, $data);

        return [
            'message' => 'account added successfully through app service'
        ];
    }
}