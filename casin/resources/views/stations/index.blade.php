<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <title>Daftar Stasiun Terdekat - CasIn.id</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="stations-page">
    <header class="site-header stations-header">
        @include('partials.brand-logo')

        <div class="header-right-group">
            <button type="button" class="hamburger-btn" aria-label="Buka menu navigasi" aria-expanded="false" data-menu-toggle>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <line x1="4" y1="6" x2="20" y2="6"></line>
                    <line x1="4" y1="12" x2="20" y2="12"></line>
                    <line x1="4" y1="18" x2="20" y2="18"></line>
                </svg>
            </button>
        </div>
    </header>

    <nav class="mobile-drawer" hidden data-mobile-drawer aria-label="Menu utama">
        <div class="mobile-drawer-inner">
            <a href="{{ route('home') }}" class="drawer-link">Beranda</a>
            <a href="{{ route('stations.index') }}" class="drawer-link is-active">Daftar Stasiun</a>
            <button type="button" class="drawer-link drawer-btn" data-vehicle-modal-open>
                Kendaraan Aktif: <span data-active-vehicle-brand>Semua Konektor</span>
            </button>
            @auth
                <span class="drawer-user">Masuk sebagai {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="drawer-link drawer-logout">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="drawer-link">Masuk</a>
                <a href="{{ route('register') }}" class="drawer-link">Daftar Akun</a>
            @endauth
        </div>
    </nav>

    <main class="stations-main">
        @include('partials.station-catalog', [
            'showIntro' => false,
            'formAction' => route('stations.index'),
        ])
    </main>

    @include('partials.vehicle-modal')
</body>
</html>
