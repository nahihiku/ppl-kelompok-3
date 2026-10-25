<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GoogleAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Throwable;

class GoogleAuthController extends Controller
{
    public function __construct(
        protected GoogleAuthService $googleAuthService
    ) {}

    /**
     * Redirect the user to Google OAuth or return the redirect URL.
     */
    public function redirect(Request $request): JsonResponse|RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $redirect = config('services.google.redirect');

        if (! $clientId || ! $redirect) {
            return response()->json([
                'success' => false,
                'message' => 'Google OAuth belum dikonfigurasi. Pastikan GOOGLE_CLIENT_ID dan GOOGLE_REDIRECT_URI telah diatur.',
            ], 500);
        }

        try {
            $redirectUrl = Socialite::driver('google')
                ->stateless()
                ->redirect()
                ->getTargetUrl();

            if ($request->wantsJson() || $request->query('format') === 'json') {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                ]);
            }

            return redirect()->away($redirectUrl);
        } catch (Throwable $e) {
            Log::error('Gagal membuat redirect URL Google OAuth: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengarahkan ke layanan Google: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Google OAuth callback and return Sanctum Bearer token.
     */
    public function callback(Request $request): JsonResponse
    {
        if ($request->has('error')) {
            return response()->json([
                'success' => false,
                'message' => 'Login Google dibatalkan atau ditolak: '.$request->query('error_description', $request->query('error')),
            ], 400);
        }

        if (! $request->has('code')) {
            return response()->json([
                'success' => false,
                'message' => 'Kode otorisasi Google tidak ditemukan pada permintaan.',
            ], 400);
        }

        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            $user = $this->googleAuthService->findOrCreateUser($googleUser);
            $deviceName = $request->query('device_name', $request->input('device_name', 'google_token'));
            $token = $user->createToken($deviceName)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Autentikasi Google berhasil.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'google_id' => $user->google_id,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Google OAuth callback failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses autentikasi Google: '.$e->getMessage(),
            ], 401);
        }
    }

    /**
     * Authenticate using Google access token or ID token (ideal for mobile/SPA clients).
     */
    public function loginWithToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'access_token' => ['nullable', 'string', 'required_without_all:id_token,code'],
            'id_token' => ['nullable', 'string'],
            'code' => ['nullable', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ], [
            'access_token.required_without_all' => 'Salah satu dari access_token, id_token, atau code harus disertakan.',
        ]);

        try {
            $googleUser = null;

            if (! empty($validated['access_token'])) {
                $googleUser = Socialite::driver('google')->userFromToken($validated['access_token']);
            } elseif (! empty($validated['code'])) {
                // Exchange authorization code for user
                $request->merge(['code' => $validated['code']]);
                $googleUser = Socialite::driver('google')->stateless()->user();
            } elseif (! empty($validated['id_token'])) {
                // Verify Google ID token via Google TokenInfo
                $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $validated['id_token'],
                ]);

                if ($response->failed() || empty($response->json('sub'))) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token identitas Google (id_token) tidak valid atau telah kedaluwarsa.',
                    ], 401);
                }

                $tokenData = $response->json();
                $googleUser = (new SocialiteUser)->map([
                    'id' => $tokenData['sub'],
                    'name' => $tokenData['name'] ?? null,
                    'email' => $tokenData['email'] ?? null,
                    'avatar' => $tokenData['picture'] ?? null,
                ]);
            }

            if (! $googleUser || empty($googleUser->getEmail())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil pengguna tidak valid atau tidak memiliki email dari Google.',
                ], 422);
            }

            $user = $this->googleAuthService->findOrCreateUser($googleUser);
            $deviceName = $validated['device_name'] ?? 'google_auth_token';
            $token = $user->createToken($deviceName)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Autentikasi Google berhasil.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'google_id' => $user->google_id,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Google login with token failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Autentikasi Google gagal: '.$e->getMessage(),
            ], 401);
        }
    }
}
