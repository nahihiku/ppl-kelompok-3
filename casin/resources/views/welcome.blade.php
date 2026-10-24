<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f8f5">
    <title>CasIn.id · Temukan stasiun pengisian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="landing-page">
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}" aria-label="CasIn.id, halaman utama">
            <span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span>
            <span>CasIn<span class="brand-dot">.</span>id</span>
        </a>
        <nav class="nav-actions" aria-label="Navigasi akun">
            @auth
                <span class="welcome-user">Hai, {{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav-login" style="border:0;background:transparent;cursor:pointer" type="submit">Keluar</button>
                </form>
            @else
                <a class="nav-login" href="{{ route('login') }}">Masuk</a>
                <a class="nav-join" href="{{ route('register') }}">Daftar</a>
            @endauth
        </nav>
    </header>

    <main class="hero">
        <section>
            <p class="eyebrow">SATU PETA UNTUK PERJALANAN LISTRIK</p>
            <h1>Temukan stasiun pengisian <span>yang tepat.</span></h1>
            <p class="hero-copy">Cari lokasi pengisian daya dari berbagai penyedia, bandingkan konektor yang tersedia, dan rencanakan perjalanan kendaraan listrikmu dengan lebih tenang.</p>
            @guest
                <a class="hero-cta" href="{{ route('register') }}">Mulai dengan akun gratis <span aria-hidden="true">→</span></a>
            @endguest

            <div class="location-widget" id="location-widget" aria-live="polite">
                <span class="location-dot" aria-hidden="true"></span>
                <div class="location-copy">
                    <p class="location-title">Lokasi perangkatmu</p>
                    <p class="location-status" data-location-status>Belum aktif — aktifkan akses lokasi untuk mencari stasiun terdekat.</p>
                </div>
                <button class="location-button" type="button" data-location-button>Aktifkan lokasi</button>
            </div>
        </section>
        <div class="map-art" aria-label="Ilustrasi peta stasiun pengisian daya">
            <span class="map-label one">SPKLU · 1,2 km</span>
            <span class="map-pin" aria-hidden="true">⌁</span>
            <span class="map-label two">Pengisian cepat · 3,5 km</span>
        </div>
    </main>
</body>
</html>
