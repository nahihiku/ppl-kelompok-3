@extends('layouts.auth', [
    'title' => 'Daftar',
    'heading' => 'Daftar Akun CasIn.id',
    'subtitle' => 'Buat akun untuk menyimpan kendaraan dan menemukan Stasiun Pengisian Daya yang sesuai.',
])

@section('content')
    <form method="POST" action="{{ route('register.store') }}" class="auth-form" data-auth-form="register" data-api-endpoint="{{ route('api.register') }}">
        @csrf
        <div class="field-group">
            <label for="name">Nama Lengkap</label>
            <div class="input-with-icon">
                <span class="input-icon-left" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20C4 16.6863 7.58172 14 12 14C16.4183 14 20 16.6863 20 20" stroke-linecap="round"></path>
                    </svg>
                </span>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    autocomplete="name"
                    required
                    autofocus
                    placeholder="Masukkan nama lengkap"
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                >
            </div>
            @error('name') <p class="field-error" id="name-error">{{ $message }}</p> @enderror
        </div>

        <div class="field-group">
            <label for="email">Email</label>
            <div class="input-with-icon">
                <span class="input-icon-left" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="5" width="18" height="14" rx="2.5"></rect>
                        <path d="M3.5 6.5L12 13L20.5 6.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </span>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                    placeholder="Masukkan alamat email"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                >
            </div>
            @error('email') <p class="field-error" id="email-error">{{ $message }}</p> @enderror
        </div>

        <div class="field-group">
            <label for="phone">Nomor HP Aktif</label>
            <div class="input-with-icon">
                <span class="input-icon-left" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="6" y="2.5" width="12" height="19" rx="2.5"></rect><path d="M10 5H14M11 18.5H13" stroke-linecap="round"></path></svg>
                </span>
                <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" placeholder="Masukkan nomor HP aktif" inputmode="tel" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
            </div>
            @error('phone') <p class="field-error" id="phone-error">{{ $message }}</p> @enderror
        </div>

        <div class="field-group">
            <label for="password">Password</label>
            <div class="input-with-icon">
                <span class="input-icon-left" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="5" y="11" width="14" height="10" rx="2.5"></rect>
                        <path d="M8 11V7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7V11" stroke-linecap="round"></path>
                    </svg>
                </span>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    required
                    minlength="8"
                    placeholder="Minimal 8 karakter"
                    data-password-input
                    @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                >
                <button type="button" class="password-toggle-btn" aria-label="Tampilkan atau sembunyikan password" data-password-toggle>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M2 12C3.8 7.5 7.5 5 12 5C16.5 5 20.2 7.5 22 12C20.2 16.5 16.5 19 12 19C7.5 19 3.8 16.5 2 12Z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            @error('password') <p class="field-error" id="password-error">{{ $message }}</p> @enderror
        </div>

        <div class="field-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <div class="input-with-icon">
                <span class="input-icon-left" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="5" y="11" width="14" height="10" rx="2.5"></rect>
                        <path d="M8 11V7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7V11" stroke-linecap="round"></path>
                    </svg>
                </span>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    minlength="8"
                    placeholder="Ulangi password"
                >
            </div>
        </div>

        <button class="primary-button" type="submit">Daftar</button>
    </form>

    <div class="auth-divider"><span>atau daftar dengan</span></div>

    <button class="google-button" type="button" data-google-auth data-google-client-id="{{ config('services.google.client_id') }}" data-api-endpoint="{{ route('api.google.token') }}">
        <svg aria-hidden="true" viewBox="0 0 48 48" class="google-icon"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5Z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.91a11.03 11.03 0 0 1-4.79 7.23l7.73 6C44.37 38.16 46.98 31.98 46.98 24.55Z"/><path fill="#FBBC05" d="M10.53 28.59A14.4 14.4 0 0 1 9.75 24c0-1.59.27-3.13.76-4.59l-7.98-6.19A23.87 23.87 0 0 0 0 24c0 3.88.93 7.55 2.56 10.78l7.97-6.19Z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.9-5.75l-7.73-6c-2.14 1.44-4.88 2.3-8.17 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48Z"/></svg>
        <span>Daftar dengan Google</span>
    </button>

    <div class="auth-api-error" data-auth-api-error role="alert" hidden></div>

    <p class="auth-switch">Sudah Punya Akun? <a href="{{ route('login') }}">Masuk Sekarang</a></p>
@endsection
