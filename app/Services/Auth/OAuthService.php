<?php

namespace App\Services\Auth;

abstract class OAuthService
{
    abstract public function getUrl(): array;
    abstract public function getToken(): array;
    abstract public function login(): array;
    abstract public function addAccount(): array;
}