<?php

namespace App\Services\Auth;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class OAuth extends OAuthService
{
    protected string $provider;
    protected string $urlToCode;
    protected string $urlToToken;
    protected string $redirectUri;
    protected array $oauthClient;
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
            $this->urlToToken = "https://github.com/login/oauth/access_token";
        }
        if ($this->provider === 'google') {
            $this->oauthClient = [
                'client_id' => getenv('GOOGLE_CLIENT_ID'),
                'client_secret' => getenv('GOOGLE_CLIENT_SECRET')
            ];
            $this->redirectUri = 'http://localhost/api/v1/auth/google/token';
            $this->urlToCode = 'https://accounts.google.com/o/oauth2/auth?scope=https://www.googleapis.com/auth/userinfo.email&redirect_uri=http://localhost/api/v1/auth/google/token&response_type=code&client_id=' . $this->oauthClient['client_id'];
            $this->urlToToken = "https://oauth2.googleapis.com/token";
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

    public function login(): array
    {
        return [];
    }

    public function addAccount(): array
    {
        return [];
    }
}