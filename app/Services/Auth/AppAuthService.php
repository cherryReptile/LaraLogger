<?php

namespace App\Services\Auth;

abstract class AppAuthService
{
    abstract public function register(): array;
    abstract public function login(): array;
    abstract public function addAccount(): array;
}