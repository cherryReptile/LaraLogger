<?php

namespace Tests\Feature;

use App\Services\Auth\OAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GithubAuthTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_get_url(): void
    {
        $response = $this->get('/api/auth/oauth/github/url');

        $response->assertStatus(200)->assertJson([
            'url_to_code' => true
        ]);
    }

    public function test_bad_get_token(): void
    {
        $response = $this->postJson('/api/auth/oauth/github/token', ['code' => $this->faker->randomAscii]);
        $response->assertStatus(400)->assertJson(['error' => true]);
    }


    public function test_bad_login(): void
    {
        $response = $this->postJson('/api/auth/oauth/github/login', [
            'access_token' => $this->faker->randomAscii
        ]);
        $response->assertStatus(400)->assertJson([
            'error' => true
        ]);
    }
}
