<?php

namespace App\Services\Auth;

use App\Exceptions\AuthServiceException;
use App\Models\Provider;
use App\Models\ProvidersData;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
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

    /**
     * @throws AuthServiceException
     */
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
        if ($this->provider != 'google' && $this->provider != 'github') {
            throw new AuthServiceException('unknown service');
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
     * @throws AuthServiceException|GuzzleException
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
            throw new AuthServiceException("{$res->error}");
        }

        $data = json_decode((string)$res->getBody());

        return ['access_token' => $data->access_token];
    }

    /**
     * @throws AuthServiceException
     */
    public function login(): array
    {
        $res = $this->requestToService($this->request);
        $data = json_decode((string)$res->getBody(), true);

        $provider = Provider::where('provider', '=', $this->provider)->firstOrFail();

        $providersData = ProvidersData::findByProviderIdAndUsername($provider->id, $data[$provider->unique_key]);
        if ($providersData != null) {
            $user = $providersData->user;
            $token = $user->createToken('api')->plainTextToken;
            return [
                'user' => $user,
                'token' => $token
            ];
        }

        $user = User::create(['login' => $data[$provider->unique_key]]);

        $providersData = new ProvidersData();
        $providersData->addProviderWithData($user, $provider, $data);

        $token = $user->createToken('api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * @throws AuthServiceException
     */
    public function addAccount(): array
    {
        $user = $this->request['user'];
        $request = $this->request['request'];

        $res = $this->requestToService($request);

        $data = json_decode((string)$res->getBody(), true);

        $provider = Provider::where('provider', '=', $this->provider)->firstOrFail();

        $providersData = ProvidersData::findByProviderIdAndUsername($provider->id, $data[$provider->unique_key]);
        if ($providersData != null) {
            throw new AuthServiceException('you already have added github account');
        }

        $providersData = new ProvidersData();
        $providersData->addProviderWithData($user, $provider, $data);
        $user->profile()->create();

        return [
            'message' => 'account added successfully through ' . $this->provider
        ];
    }

    /**
     * @throws AuthServiceException
     */
    private function requestToService(array $request): ResponseInterface
    {
        $client = new Client(['headers' => ['Authorization' => 'Bearer ' . $request['access_token']]]);

        try {
            $res = $client->request('GET', $this->serviceApiUrl);
        } catch (GuzzleException) {
            throw new AuthServiceException('failed to get user from ' . $this->provider);
        }

        return $res;
    }
}