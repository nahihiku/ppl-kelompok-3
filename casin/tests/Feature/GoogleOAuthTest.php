<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleOAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.google.client_id', 'mock-google-client-id');
        Config::set('services.google.client_secret', 'mock-google-client-secret');
        Config::set('services.google.redirect', 'http://localhost/auth/google/callback');
    }

    public function test_api_google_redirect_returns_json_url(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('redirect->getTargetUrl')
            ->once()
            ->andReturn('https://accounts.google.com/o/oauth2/v2/auth?mock=1');

        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);

        $response = $this->getJson(route('api.google.redirect'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'redirect_url' => 'https://accounts.google.com/o/oauth2/v2/auth?mock=1',
            ]);
    }

    public function test_api_google_callback_creates_user_and_returns_sanctum_token(): void
    {
        $mockUser = Mockery::mock(SocialiteUser::class);
        $mockUser->shouldReceive('getId')->andReturn('google-id-12345');
        $mockUser->shouldReceive('getEmail')->andReturn('google.rider@example.com');
        $mockUser->shouldReceive('getName')->andReturn('Google Rider');
        $mockUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('user')->once()->andReturn($mockUser);

        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);

        $response = $this->getJson(route('api.google.callback', ['code' => 'fake-auth-code']));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Autentikasi Google berhasil.',
                'data' => [
                    'user' => [
                        'name' => 'Google Rider',
                        'email' => 'google.rider@example.com',
                        'google_id' => 'google-id-12345',
                    ],
                    'token_type' => 'Bearer',
                ],
            ])
            ->assertJsonStructure([
                'data' => ['token'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'google.rider@example.com',
            'google_id' => 'google-id-12345',
        ]);
    }

    public function test_api_google_callback_links_existing_user_by_email(): void
    {
        $existingUser = User::create([
            'name' => 'Existing Rider',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ]);

        $mockUser = Mockery::mock(SocialiteUser::class);
        $mockUser->shouldReceive('getId')->andReturn('google-id-99999');
        $mockUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $mockUser->shouldReceive('getName')->andReturn('Existing Rider');
        $mockUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('user')->once()->andReturn($mockUser);

        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);

        $response = $this->getJson(route('api.google.callback', ['code' => 'fake-auth-code']));

        $response->assertStatus(200);

        $this->assertEquals('google-id-99999', $existingUser->fresh()->google_id);
    }

    public function test_api_google_login_with_id_token(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/tokeninfo*' => Http::response([
                'sub' => 'google-id-token-111',
                'email' => 'idtoken.user@example.com',
                'name' => 'ID Token User',
                'picture' => 'https://example.com/pic.jpg',
            ], 200),
        ]);

        $response = $this->postJson(route('api.google.token'), [
            'id_token' => 'sample-google-jwt-token',
            'device_name' => 'mobile_app',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Autentikasi Google berhasil.',
                'data' => [
                    'user' => [
                        'email' => 'idtoken.user@example.com',
                        'google_id' => 'google-id-token-111',
                    ],
                    'token_type' => 'Bearer',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'idtoken.user@example.com',
            'google_id' => 'google-id-token-111',
        ]);
    }

    public function test_web_google_redirect_and_callback(): void
    {
        $mockUser = Mockery::mock(SocialiteUser::class);
        $mockUser->shouldReceive('getId')->andReturn('google-web-123');
        $mockUser->shouldReceive('getEmail')->andReturn('web.user@example.com');
        $mockUser->shouldReceive('getName')->andReturn('Web User');
        $mockUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($mockUser);

        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'web.user@example.com',
            'google_id' => 'google-web-123',
        ]);
    }
}
