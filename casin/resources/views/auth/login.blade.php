@extends('layouts.auth', [
    'title' => 'Masuk',
    'heading' => 'Senang melihatmu lagi',
    'subtitle' => 'Masuk untuk menemukan stasiun pengisian yang cocok dengan kendaraanmu.',
])

@section('content')
    <a class="google-button" href="{{ route('google.redirect') }}">
        <svg aria-hidden="true" viewBox="0 0 48 48" class="google-icon"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5Z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.91a11.03 11.03 0 0 1-4.79 7.23l7.73 6C44.37 38.16 46.98 31.98 46.98 24.55Z"/><path fill="#FBBC05" d="M10.53 28.59A14.4 14.4 0 0 1 9.75 24c0-1.59.27-3.13.76-4.59l-7.98-6.19A23.87 23.87 0 0 0 0 24c0 3.88.93 7.55 2.56 10.78l7.97-6.19Z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.9-5.75l-7.73-6c-2.14 1.44-4.88 2.3-8.17 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48Z"/></svg>
        <span>Masuk dengan Google</span>
    </a>

    <div class="auth-divider"><span>atau masuk dengan email</span></div>

    <form method="POST" action="{{ route('login.store') }}" class="auth-form">
        @csrf
        <div class="field-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="nama@email.com" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
            @error('email') <p class="field-error" id="email-error">{{ $message }}</p> @enderror
        </div>

        <div class="field-group">
            <div class="label-row"><label for="password">Kata sandi</label></div>
            <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Masukkan kata sandi" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
            @error('password') <p class="field-error" id="password-error">{{ $message }}</p> @enderror
        </div>

        <label class="checkbox-row"><input type="checkbox" name="remember" value="1"><span>Ingat saya di perangkat ini</span></label>
        <button class="primary-button" type="submit">Masuk <span aria-hidden="true">→</span></button>
    </form>

    <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar gratis</a></p>
@endsection
