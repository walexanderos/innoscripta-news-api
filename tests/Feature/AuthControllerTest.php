<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_register_a_user()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Registration successful',
                'data' => [],
            ]);
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_error_for_invalid_registration_data()
    {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'nomatch',
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(400)
            ->assertJsonStructure(['message', 'errors']);
    }

    public function it_can_login_a_user()
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);

        $data = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data' => ['access_token', 'token_type']]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_error_for_invalid_login_details()
    {
        $data = [
            'email' => 'invalid@example.com',
            'password' => 'invalidpassword',
        ];

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Bad request',
                'errors' => 'Invalid login details',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_logout_a_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully',
                'data' => null,
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fails_logout_without_authentication()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }
}
