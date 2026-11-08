<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#eefbfc">
    <title>CasIn.id - Temukan Stasiun Pengisian Daya Terdekat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="landing-page">
    <header class="site-header">
        @include('partials.brand-logo')

        <nav class="desktop-nav-links" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="desktop-nav-link is-active">Home</a>
            <button type="button" class="desktop-nav-link desktop-nav-btn" data-vehicle-modal-open>Kendaraanku</button>
            <a href="{{ route('stations.index') }}" class="desktop-nav-link">Daftar Stasiun</a>
        </nav>

        <div class="header-right-group">
            <nav class="nav-actions" aria-label="Navigasi akun">
                @auth
                    <span class="welcome-user">Hai, {{ Auth::user()->name }}</span>
                    <a class="nav-stations-link" href="{{ route('stations.index') }}">Daftar Stasiun</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline-logout-form">
                        @csrf
                        <button class="nav-login-pill" type="submit">Keluar</button>
                    </form>
                @else
                    <a class="nav-login-pill nav-login" href="{{ route('login') }}">Sign In</a>
                    <a class="nav-join" href="{{ route('register') }}">Sign Up</a>
                @endauth
            </nav>

            <button type="button" class="hamburger-btn" aria-label="Buka menu navigasi" aria-expanded="false" data-menu-toggle>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <line x1="4" y1="6" x2="20" y2="6"></line>
                    <line x1="4" y1="12" x2="20" y2="12"></line>
                    <line x1="4" y1="18" x2="20" y2="18"></line>
                </svg>
            </button>
        </div>
    </header>

    <nav class="mobile-drawer" hidden data-mobile-drawer aria-label="Menu navigasi mobile">
        <div class="mobile-drawer-inner">
            <a href="{{ route('home') }}" class="drawer-link is-active">Home</a>
            <button type="button" class="drawer-link drawer-btn" data-vehicle-modal-open>
                Kendaraanku (<span data-active-vehicle-brand>Pilih Kendaraan</span>)
            </button>
            <a href="{{ route('stations.index') }}" class="drawer-link">Daftar Stasiun</a>
            @auth
                <span class="drawer-user">Masuk sebagai {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="drawer-link drawer-logout">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="drawer-link">Sign In</a>
                <a href="{{ route('register') }}" class="drawer-link">Sign Up</a>
            @endauth
        </div>
    </nav>

    @if (session('status'))
        <div class="site-status-banner" role="status">
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <main class="landing-main">
        <section class="figma-hero-section">
            <div class="figma-hero-inner">
                <div class="figma-hero-grid">
                    <div class="figma-hero-content">
                        <div class="verified-pill">
                            <span class="verified-dot" aria-hidden="true"></span>
                            <span>1.000+ Stasiun Pengisian Daya Terverifikasi</span>
                        </div>

                        <h1 class="figma-hero-title">
                            Temukan Stasiun Pengisian Daya Terdekat &amp; Sesuai Kendaraanmu
                        </h1>

                        <p class="figma-hero-copy hero-copy">
                            Informasi stasiun pengisian daya kendaraan listrik yang akurat, real-time, dan kompatibel dengan mobilmu.
                        </p>

                        <div class="figma-hero-actions">
                            <a class="btn-gradient-cta hero-cta" href="#cari-stasiun">
                                <span>Cari Stasiun Sekarang</span>
                                <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                    <path d="M7.5 4.5L13 10L7.5 15.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="figma-hero-visual" aria-hidden="true">
                        <div class="hero-image-container">
                            <img
                                src="{{ asset('images/hero-ev-car.jpg') }}"
                                alt="Mobil Listrik CasIn.id"
                                class="hero-ev-image"
                                loading="eager"
                            >
                        </div>
                    </div>
                </div>

                <div class="figma-hero-bottom">
                    <div class="active-vehicle-card" id="active-vehicle-widget">
                        <div class="active-vehicle-left">
                            <div class="active-vehicle-icon" aria-hidden="true">
                                <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7 15L9 9.2C9.4 8 10.5 7.2 11.8 7.2H20.2C21.5 7.2 22.6 8 23 9.2L25 15" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"/>
                                    <rect x="5" y="15" width="22" height="8" rx="2.5" stroke="#FFFFFF" stroke-width="2"/>
                                    <path d="M7 23V25.5M25 23V25.5" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round"/>
                                    <circle cx="9.5" cy="19" r="1.3" fill="#FFFFFF"/>
                                    <circle cx="22.5" cy="19" r="1.3" fill="#FFFFFF"/>
                                    <path d="M16.6 10.5L14.2 14H16.2L15.4 17.5L17.8 14H15.8L16.6 10.5Z" fill="#00D2E8"/>
                                </svg>
                            </div>
                            <div class="active-vehicle-info">
                                <p class="active-vehicle-heading">Kendaraan Aktif</p>
                                <p class="active-vehicle-meta" data-active-vehicle-brand>Merek Kendaraan</p>
                                <p class="active-vehicle-meta" data-active-vehicle-connector>Jenis Konektor</p>
                            </div>
                        </div>
                        <button type="button" class="btn-choose-vehicle" data-vehicle-modal-open>
                            Pilih Kendaraan
                        </button>
                    </div>

                    <div class="location-widget" id="location-widget" aria-live="polite">
                        <span class="location-dot" aria-hidden="true"></span>
                        <div class="location-copy">
                            <p class="location-title">Lokasi perangkatmu</p>
                            <p class="location-status" data-location-status>Belum aktif - aktifkan akses lokasi untuk mengurutkan stasiun terdekat secara real-time.</p>
                        </div>
                        <button class="location-button" type="button" data-location-button>Aktifkan lokasi</button>
                    </div>
                </div>
            </div>
        </section>

        <section class="figma-catalog-section">
            @include('partials.station-catalog', [
                'showIntro' => true,
                'formAction' => route('home'),
            ])
        </section>
    </main>

    @include('partials.vehicle-modal')
</body>
</html>
