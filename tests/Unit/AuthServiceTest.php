<?php
namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_register_a_user()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->authService->registerUser($data);

        $this->assertEmpty($response);
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

        $response = $this->authService->registerUser($data);

        $this->assertNotEmpty($response['error']);
    }

    public function it_can_login_a_user()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $resp = $this->authService->registerUser($data);
        $credentials = ['email' => $data['email'], 'password' => $data['password']];

        $response = $this->authService->loginUser($credentials);

        $this->assertArrayHasKey('access_token', $response);
        $this->assertArrayHasKey('token_type', $response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_error_for_invalid_login_details()
    {
        $credentials = ['email' => 'wrong@example.com', 'password' => 'wrongpassword'];
        $response = $this->authService->loginUser($credentials);

        $this->assertEquals('Invalid login details', $response['error']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_logout_a_user()
    {
        $user = User::factory()->create();
        $user->createToken('auth_token');

        $this->authService->logoutUser($user);

        $this->assertEmpty($user->tokens);
    }
}
