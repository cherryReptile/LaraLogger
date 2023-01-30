<?php

namespace App\Services\Auth;

use App\Models\User;

abstract class AppAuthService
{
    abstract public function register(array $fields): User|null;
//    abstract public function login();
//    abstract public function addAccount();
}