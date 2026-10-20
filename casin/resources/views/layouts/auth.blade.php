<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f8f5">
    <title>{{ $title }} · CasIn.id</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <a class="brand auth-brand" href="{{ route('home') }}" aria-label="CasIn.id, halaman utama">
            <span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span>
            <span>CasIn<span class="brand-dot">.</span>id</span>
        </a>

        <section class="auth-card" aria-labelledby="auth-title">
            <div class="auth-heading">
                <p class="eyebrow">MOBILITAS LEBIH MUDAH</p>
                <h1 id="auth-title">{{ $heading }}</h1>
                <p class="auth-subtitle">{{ $subtitle }}</p>
            </div>

            @if (session('status'))
                <div class="notice notice-success" role="status">{{ session('status') }}</div>
            @endif

            @if ($errors->has('google'))
                <div class="notice notice-error" role="alert">{{ $errors->first('google') }}</div>
            @endif

            @yield('content')
        </section>

        <p class="auth-footer">Dengan melanjutkan, kamu menyetujui Syarat Penggunaan dan Kebijakan Privasi CasIn.id.</p>
    </main>
</body>
</html>
