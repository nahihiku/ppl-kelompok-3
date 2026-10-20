<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
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
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'email.lowercase' => 'Gunakan huruf kecil pada alamat email.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi belum cocok.',
            'password.min' => 'Kata sandi harus terdiri dari minimal 8 karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('status', 'Akun CasIn.id kamu berhasil dibuat.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Kamu sudah keluar dari akun.');
    }

    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $redirectUri = config('services.google.redirect');

        if (! $clientId || ! $redirectUri) {
            return redirect()->route('login')->withErrors([
                'google' => 'Login Google belum dikonfigurasi. Silakan masuk menggunakan email dan kata sandi.',
            ]);
        }

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
        ]));
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        $expectedState = $request->session()->pull('google_oauth_state');
        if (! $expectedState || ! hash_equals($expectedState, (string) $request->query('state'))) {
            return redirect()->route('login')->withErrors([
                'google' => 'Sesi login Google tidak valid atau sudah kedaluwarsa. Silakan coba lagi.',
            ]);
        }

        if ($request->filled('error') || ! $request->filled('code')) {
            return redirect()->route('login')->withErrors([
                'google' => 'Login Google dibatalkan atau tidak berhasil. Silakan coba lagi.',
            ]);
        }

        try {
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $request->query('code'),
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect'),
                'grant_type' => 'authorization_code',
            ])->throw()->json();

            $googleUser = Http::withToken($tokenResponse['access_token'] ?? '')
                ->get('https://openidconnect.googleapis.com/v1/userinfo')
                ->throw()
                ->json();

            if (empty($googleUser['sub']) || empty($googleUser['email']) || ! filter_var($googleUser['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \UnexpectedValueException('Google did not return a valid user profile.');
            }

            if (! filter_var($googleUser['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                return redirect()->route('login')->withErrors([
                    'google' => 'Akun Google perlu memiliki email terverifikasi untuk masuk ke CasIn.id.',
                ]);
            }

            $user = User::where('google_id', $googleUser['sub'])->first();

            if (! $user) {
                $email = Str::lower($googleUser['email']);
                $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

                if ($user) {
                    $user->forceFill([
                        'google_id' => $googleUser['sub'],
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ])->save();
                } else {
                    $user = User::create([
                        'name' => $googleUser['name'] ?? Str::before($email, '@'),
                        'email' => $email,
                        'google_id' => $googleUser['sub'],
                        'email_verified_at' => now(),
                        'password' => Str::random(64),
                    ]);
                }
            }

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('home'))->with('status', 'Kamu berhasil masuk dengan Google.');
        } catch (Throwable $exception) {
            Log::warning('Google sign-in failed.', ['exception' => $exception->getMessage()]);

            return redirect()->route('login')->withErrors([
                'google' => 'Kami belum bisa menghubungkan akun Google kamu. Periksa koneksi lalu coba lagi.',
            ]);
        }
    }
}
