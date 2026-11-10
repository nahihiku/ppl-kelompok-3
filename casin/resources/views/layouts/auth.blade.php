<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - CasIn.id</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body class="auth-page">
    <header class="auth-topbar">
        @include('partials.brand-logo', ['class' => 'auth-brand'])
    </header>

    <main class="auth-shell">
        <section class="auth-card" aria-labelledby="auth-title">
            <div class="auth-heading">
                <h1 id="auth-title">{{ $heading }}</h1>
                <p class="auth-subtitle">{{ $subtitle }}</p>
            </div>

            <div class="auth-card-panel">
                @if (session('status'))
                    <div class="notice notice-success" role="status">{{ session('status') }}</div>
                @endif

                @if ($errors->has('google'))
                    <div class="notice notice-error" role="alert">{{ $errors->first('google') }}</div>
                @endif

                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
