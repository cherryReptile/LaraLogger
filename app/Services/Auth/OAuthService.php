<?php

namespace App\Services\Auth;

use App\Models\User;

abstract class OAuthService
{
    abstract public function getUrl(): array;
    abstract public function getToken(array $request): array;
    abstract public function login(array $request): array;
    abstract public function addAccount(User $user, array $request): array;
}