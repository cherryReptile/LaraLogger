<?php

namespace App\Services\Auth;

use App\Models\Provider;
use App\Models\ProvidersData;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ResponseInterface;

class OAuth extends OAuthService
{
    protected string $provider;
    protected string $urlToCode;
    protected string $urlToToken;
    protected string $redirectUri;
    protected array $oauthClient;
    protected string $serviceApiUrl;
    protected array $request;

    public function __construct(string $provider, ?array $request)
    {
        $this->provider = $provider;
        if ($this->provider === 'github') {
            $this->oauthClient = [
                'client_id' => getenv('GITHUB_CLIENT_ID'),
                'client_secret' => getenv('GITHUB_CLIENT_SECRET')
            ];
            $this->redirectUri = getenv('APP_URL') . '/api/v1/auth/github/token';
            $this->urlToCode = 'https://github.com/login/oauth/authorize?client_id=' . $this->oauthClient['client_id'];
            $this->urlToToken = 'https://github.com/login/oauth/access_token';
            $this->serviceApiUrl = 'https://api.github.com/user';
        }
        if ($this->provider === 'google') {
            $this->oauthClient = [
                'client_id' => getenv('GOOGLE_CLIENT_ID'),
                'client_secret' => getenv('GOOGLE_CLIENT_SECRET')
            ];
            $this->redirectUri = 'http://localhost/api/v1/auth/google/token';
            $this->urlToCode = 'https://accounts.google.com/o/oauth2/auth?scope=https://www.googleapis.com/auth/userinfo.email&redirect_uri=http://localhost/api/v1/auth/google/token&response_type=code&client_id=' . $this->oauthClient['client_id'];
            $this->urlToToken = "https://oauth2.googleapis.com/token";
            $this->serviceApiUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        }
        if ($request != null) {
            $this->request = $request;
        }
    }

    public function getUrl(): array
    {
        return ['url_to_code' => $this->urlToCode];
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function getToken(): array
    {
        $fields = [
            'code' => $this->request['code'],
            'client_id' => $this->oauthClient['client_id'],
            'client_secret' => $this->oauthClient['client_secret'],
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code'
        ];

        $client = new Client(['headers' => ['Accept' => 'application/json']]);

        try {
            $res = $client->request('POST', $this->urlToToken, [
                'form_params' => $fields,
            ]);
        } catch (ClientException $e) {
            $res = (string)$e->getResponse()->getBody();
            $res = json_decode($res);
            throw new Exception("{$res->error}");
        }

        if ($res->getStatusCode() != 200) {
            throw new Exception('failed request to ' . $this->provider);
        }

        $data = json_decode((string)$res->getBody());

        return ['access_token' => $data->access_token];
    }

    /**
     * @throws Exception
     */
    public function login(): array
    {
        $client = new Client(['headers' => ['Authorization' => 'Bearer ' . $this->request['access_token']]]);

        try {
            $res = $client->request('GET', $this->serviceApiUrl);
        } catch (GuzzleException) {
            throw new Exception('failed to get user from ' . $this->provider);
        }

        $data = json_decode((string)$res->getBody());

        $provider = Provider::where('provider', '=', $this->provider)->firstOrFail();
        $uniqueKey = $provider->unique_key;
        $providersData = ProvidersData::query()->select()->whereRaw("provider_id={$provider->id} and username='{$data->$uniqueKey}'")->first();
        if ($providersData != null) {
            $user = $providersData->user;
            $token = $user->createToken('api')->plainTextToken;
            return [
                'user' => $user,
                'token' => $token
            ];
        }

        $user = User::create(['login' => $data->$uniqueKey]);
        $json = json_encode($data);

        $user->providersData()->create([
            'data' => $json,
            'username' => $user->login,
            'provider_id' => $provider->id
        ]);

        DB::table('users_providers')->insert([
            'user_id' => $user->id,
            'provider_id' => $provider->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function addAccount(): array
    {
        return [];
    }
}