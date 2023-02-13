<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_url(): void
    {
        $response = $this->get('/api/auth/oauth/google/url');

        $response->assertStatus(200)->assertJson([
            'url_to_code' => true
        ]);
    }

    public function test_bad_get_token(): void
    {
        $response = $this->postJson('/api/auth/oauth/google/token', ['code' => $this->faker->randomAscii]);
        $response->assertStatus(400)->assertJson(['error' => true]);
    }

    public function test_bad_login(): void
    {
        $response = $this->postJson('/api/auth/oauth/google/login', [
            'access_token' => $this->faker->randomAscii
        ]);
        $response->assertStatus(400)->assertJson([
            'error' => true
        ]);
    }
}
