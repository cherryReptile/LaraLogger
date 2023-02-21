<?php

namespace App\Services\Auth;

use App\Models\User;

abstract class AppAuthService
{
    abstract public function register(array $request): array;
    abstract public function login(array $request): array;
    abstract public function addAccount(User $user, array $request): array;
}