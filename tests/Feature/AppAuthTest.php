<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppAuthTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return mixed
     */
    public function test_register(): mixed
    {
        $data = [
            'email' => $this->faker->email,
            'password' => $this->faker->password
        ];

        $response = $this->postJson('/api/auth/app/register', $data);

        $response->assertStatus(201)->assertJson([
            'user' => true,
            'token' => true
        ]);

        return $response->json('token');
    }

    public function test_bad_register(): void
    {
        $data = [
            'email' => $this->faker->randomAscii,
        ];

        $response = $this->postJson('/api/auth/app/register', $data);

        $response->assertStatus(422)->assertJson([
            'message' => true,
            'errors' => true
        ]);
    }

    public function test_bad_add_account(): void
    {
        $token = $this->test_register();
        $response = $this->postJson('/api/auth/app/add', [
            'email' => $this->faker->email,
            'password' => $this->faker->password
        ], [
            'Authorization' => "Bearer $token"
        ]);
        $response->assertStatus(400)->assertJson([
            'error' => true
        ]);
    }
}
