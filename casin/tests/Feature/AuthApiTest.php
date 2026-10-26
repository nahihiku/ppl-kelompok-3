<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully_via_api(): void
    {
        $payload = [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '+628123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_name' => 'flutter_app',
        ];

        $response = $this->postJson(route('api.register'), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Registrasi akun berhasil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                    'token',
                    'token_type',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '+628123456789',
        ]);

        $user = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertCount(1, $user->tokens);
    }

    public function test_registration_validation_errors(): void
    {
        $response = $this->postJson(route('api.register'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registration_fails_when_password_confirmation_does_not_match(): void
    {
        $payload = [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'password' => 'password123',
            'password_confirmation' => 'mismatch123',
        ];

        $response = $this->postJson(route('api.register'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_if_email_already_exists(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
        ]);

        $payload = [
            'name' => 'New User',
            'email' => 'duplicate@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->postJson(route('api.register'), $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'Ahmad Dani',
            'email' => 'ahmad@example.com',
            'password' => 'secret1234',
        ]);

        $response = $this->postJson(route('api.login'), [
            'email' => 'ahmad@example.com',
            'password' => 'secret1234',
            'device_name' => 'test_device',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login berhasil.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                    'token_type',
                ],
            ]);

        $this->assertCount(1, $user->fresh()->tokens);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::create([
            'name' => 'Ahmad Dani',
            'email' => 'ahmad@example.com',
            'password' => 'secret1234',
        ]);

        $response = $this->postJson(route('api.login'), [
            'email' => 'ahmad@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Email atau kata sandi yang kamu masukkan tidak sesuai.',
            ]);
    }

    public function test_authenticated_user_can_access_me_profile(): void
    {
        $user = User::create([
            'name' => 'Profile User',
            'email' => 'profile@example.com',
            'password' => 'password123',
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(route('api.me'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => 'profile@example.com',
                    ],
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_access_me_profile(): void
    {
        $response = $this->getJson(route('api.me'));

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_logout_and_revoke_token(): void
    {
        $user = User::create([
            'name' => 'Logout User',
            'email' => 'logout@example.com',
            'password' => 'password123',
        ]);

        $token = $user->createToken('logout_test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('api.logout'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout berhasil. Token telah dihapus.',
            ]);

        $this->assertCount(0, $user->fresh()->tokens);
    }
}
