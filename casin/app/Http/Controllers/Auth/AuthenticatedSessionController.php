<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GoogleAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class AuthenticatedSessionController extends Controller
{
    public function createLogin(): View
    {
        return view('auth.login');
    }

    public function createRegistration(): View
    {
        return view('auth.register');
    }

    public function storeLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);
        $credentials['email'] = Str::lower($credentials['email']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau kata sandi yang kamu masukkan tidak sesuai.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('status', 'Selamat datang kembali di CasIn.id!');
    }

    public function storeRegistration(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+()\-\s]+$/'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'phone.regex' => 'Nomor HP hanya boleh berisi angka dan simbol telepon yang valid.',
            'email.lowercase' => 'Gunakan huruf kecil pada alamat email.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi belum cocok.',
            'password.min' => 'Kata sandi harus terdiri dari minimal 8 karakter.',
        ]);

        $user = User::create([
            'nama' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'user',
            'password' => $validated['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('status', 'Akun CasIn.id kamu berhasil dibuat.');
    }

    public function destroy(Request $request): RedirectResponse|JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kamu sudah keluar dari akun.',
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login')->with('status', 'Kamu sudah keluar dari akun.');
    }

    public function redirectToGoogle(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect');

        if (! $clientId || ! $redirectUri) {
            return redirect()->route('login')->withErrors([
                'google' => 'Login Google belum dikonfigurasi. Silakan masuk menggunakan email dan kata sandi.',
            ]);
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (Throwable $exception) {
            Log::warning('Google redirect failed.', ['exception' => $exception->getMessage()]);

            return redirect()->route('login')->withErrors([
                'google' => 'Gagal mengarahkan ke Google. Silakan coba lagi nanti.',
            ]);
        }
    }

    public function handleGoogleCallback(Request $request, GoogleAuthService $googleAuthService): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()->route('login')->withErrors([
                'google' => 'Login Google dibatalkan atau tidak berhasil. Silakan coba lagi.',
            ]);
        }

        try {
            try {
                $googleUser = Socialite::driver('google')->user();
            } catch (InvalidStateException $e) {
                $googleUser = Socialite::driver('google')->stateless()->user();
            }

            if (empty($googleUser->getId()) || empty($googleUser->getEmail()) || ! filter_var($googleUser->getEmail(), FILTER_VALIDATE_EMAIL)) {
                throw new \UnexpectedValueException('Google did not return a valid user profile.');
            }

            $user = $googleAuthService->findOrCreateUser($googleUser);

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('home'))->with('status', 'Kamu berhasil masuk dengan Google.');
        } catch (Throwable $exception) {
            Log::warning('Google sign-in failed.', [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('login')->withErrors([
                'google' => 'Kami belum bisa menghubungkan akun Google kamu. Periksa koneksi lalu coba lagi.',
            ]);
        }
    }

    /**
     * Synchronize web session using an active Sanctum token.
     */
    public function syncSession(Request $request): JsonResponse
    {
        $rawToken = $request->bearerToken() ?: $request->input('token');

        if (! $rawToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token autentikasi tidak ditemukan.',
            ], 401);
        }

        $token = PersonalAccessToken::findToken($rawToken);

        if (! $token || ! $token->tokenable) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau telah kedaluwarsa.',
            ], 401);
        }

        $user = $token->tokenable;

        if (! Auth::check() || (int) Auth::id() !== (int) $user->id) {
            Auth::login($user);
            $request->session()->regenerate();
        }

        return response()->json([
            'success' => true,
            'message' => 'Sesi web berhasil disinkronkan.',
            'data' => [
                'csrf_token' => csrf_token(),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role ?? 'user',
                    'is_admin' => $user->isAdmin(),
                ],
            ],
        ]);
    }
}
