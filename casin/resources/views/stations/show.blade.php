<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <title>{{ $station['name'] }} - CasIn.id</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="station-detail-page">
    <div class="detail-layout">
        <div
            class="detail-map-wrapper"
            id="station-detail-map"
            data-lat="{{ $station['latitude'] }}"
            data-lng="{{ $station['longitude'] }}"
            data-name="{{ $station['name'] }}"
            aria-label="Peta lokasi {{ $station['name'] }}"
        >
            <div class="map-fallback-canvas" aria-hidden="true">
                <svg viewBox="0 0 400 280" preserveAspectRatio="xMidYMid slice" class="map-fallback-svg">
                    <rect width="400" height="280" fill="#e5e7eb"/>
                    <path d="M0 40 Q150 55 260 110 T400 140" fill="none" stroke="#93c5fd" stroke-width="18"/>
                    <polygon points="130,85 205,70 230,150 150,165" fill="#a3e635" opacity="0.55"/>
                    <polygon points="155,175 235,160 265,250 175,260" fill="#a3e635" opacity="0.55"/>
                    <path d="M-10 90 L410 35 M40 -10 L130 290 M130 -10 L265 290 M-10 190 L410 140 M230 -10 L350 290" stroke="#ffffff" stroke-width="7"/>
                </svg>
                <div class="map-pin-marker map-pin-secondary" style="top: 34%; left: 18%;">
                    <svg viewBox="0 0 32 42" fill="none"><path d="M16 0C7.16 0 0 7.16 0 16C0 27.2 16 42 16 42C16 42 32 27.2 32 16C32 7.16 24.84 0 16 0Z" fill="#EF4444"/><circle cx="16" cy="15" r="6" fill="#FFFFFF"/></svg>
                </div>
                <div class="map-pin-marker map-pin-secondary" style="top: 42%; left: 68%;">
                    <svg viewBox="0 0 32 42" fill="none"><path d="M16 0C7.16 0 0 7.16 0 16C0 27.2 16 42 16 42C16 42 32 27.2 32 16C32 7.16 24.84 0 16 0Z" fill="#EF4444"/><circle cx="16" cy="15" r="6" fill="#FFFFFF"/></svg>
                </div>
                <div class="map-pin-marker map-pin-main" style="top: 56%; left: 48%;">
                    <svg viewBox="0 0 44 56" fill="none"><path d="M22 0C9.85 0 0 9.85 0 22C0 37.4 22 56 22 56C22 56 44 37.4 44 22C44 9.85 34.15 0 22 0Z" fill="#EF4444"/><circle cx="22" cy="21" r="8.5" fill="#FFFFFF"/></svg>
                </div>
            </div>

            <a href="{{ route('stations.index') }}" class="map-back-btn" aria-label="Kembali ke daftar stasiun">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 18L9 12L15 6"/>
                </svg>
                <span>Kembali</span>
            </a>
        </div>

        <main class="detail-sheet">
            @if (session('status'))
                <div class="notice notice-success" role="status">{{ session('status') }}</div>
            @endif

            <div class="detail-top-meta">
                <div class="station-badges">
                    <span class="badge-operator">{{ $station['operator_badge'] }}</span>
                    <span class="badge-status badge-status-{{ $station['current_status'] }}">
                        <span class="badge-status-dot" aria-hidden="true"></span>
                        {{ $station['status_label'] }}
                    </span>
                </div>

                <div class="detail-stars" aria-label="Rating {{ $station['rating'] }} dari 5 bintang">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $station['rating'])
                            <svg class="star-icon is-filled" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.368-2.448a1 1 0 00-1.176 0l-3.368 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.067 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.282-3.957z"/>
                            </svg>
                        @else
                            <svg class="star-icon is-empty" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.368-2.448a1 1 0 00-1.176 0l-3.368 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.067 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.282-3.957z"/>
                            </svg>
                        @endif
                    @endfor
                </div>
            </div>

            <h1 class="detail-station-title">{{ $station['name'] }}</h1>
            <p class="detail-station-address">{{ $station['address'] }}</p>

            <p class="detail-station-distance">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 21C16 16.8 19 13.4 19 9.5C19 5.63401 15.866 2.5 12 2.5C8.13401 2.5 5 5.63401 5 9.5C5 13.4 8 16.8 12 21Z"></path>
                    <circle cx="12" cy="9.5" r="2.5"></circle>
                </svg>
                <span data-detail-distance data-lat="{{ $station['latitude'] }}" data-lng="{{ $station['longitude'] }}">{{ number_format($station['distance_km'], 1, '.', '') }} km</span>
            </p>

            <div class="detail-actions-row">
                <div class="detail-actions-left">
                    <a
                        href="https://www.google.com/maps/dir/?api=1&amp;destination={{ $station['latitude'] }},{{ $station['longitude'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn-gradient-pill btn-navigate"
                    >
                        Navigate
                    </a>

                    <button
                        type="button"
                        class="btn-circle-gradient"
                        aria-label="Simpan ke stasiun favorit"
                        aria-pressed="false"
                        data-favorite-toggle="{{ $station['id'] }}"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M12 20.5C12 20.5 3.5 15 3.5 8.8C3.5 5.9 5.8 3.5 8.7 3.5C10.4 3.5 11.9 4.3 12 5.5C12.1 4.3 13.6 3.5 15.3 3.5C18.2 3.5 20.5 5.9 20.5 8.8C20.5 15 12 20.5 12 20.5Z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <button
                        type="button"
                        class="btn-circle-gradient"
                        aria-label="Bagikan lokasi stasiun"
                        data-share-station="{{ $station['name'] }}"
                    >
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="18" cy="5" r="3"></circle>
                            <circle cx="6" cy="12" r="3"></circle>
                            <circle cx="18" cy="19" r="3"></circle>
                            <line x1="8.6" y1="13.5" x2="15.4" y2="17.5"></line>
                            <line x1="15.4" y1="6.5" x2="8.6" y2="10.5"></line>
                        </svg>
                    </button>
                </div>

                <button type="button" class="btn-gradient-pill btn-lapor" data-report-modal-open>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <line x1="12" y1="8" x2="12" y2="12.5" stroke-linecap="round"></line>
                        <circle cx="12" cy="16" r="1" fill="currentColor" stroke="none"></circle>
                    </svg>
                    <span>Lapor</span>
                </button>
            </div>

            <p class="share-feedback" hidden data-share-feedback role="status"></p>

            <div class="detail-tabs" role="tablist" aria-label="Informasi detail stasiun">
                <button type="button" class="detail-tab is-active" role="tab" aria-selected="true" data-tab-target="tab-kotak">
                    Kotak Pengisian
                </button>
                <button type="button" class="detail-tab" role="tab" aria-selected="false" data-tab-target="tab-ulasan">
                    Ulasan
                </button>
                <button type="button" class="detail-tab" role="tab" aria-selected="false" data-tab-target="tab-fasilitas">
                    Fasilitas Umum
                </button>
            </div>

            <section id="tab-kotak" class="detail-tab-panel" role="tabpanel" data-tab-panel>
                <div class="connector-summary-pill">
                    {{ $station['available_chargers'] }}/{{ $station['total_chargers'] }} Konektor Tersedia
                </div>

                <div class="charging-boxes-list">
                    @foreach ($station['charging_boxes'] as $box)
                        <article class="charging-box-card">
                            <div class="charging-box-top">
                                <div class="charging-box-identity">
                                    <div class="charging-box-icon" aria-hidden="true">
                                        <svg viewBox="0 0 36 36" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect x="6" y="6" width="16" height="24" rx="2"></rect>
                                            <rect x="9" y="9" width="10" height="6" rx="1"></rect>
                                            <path d="M14.5 18L12 22H14.5L13.5 26L16 22H13.5L14.5 18Z" fill="currentColor" stroke="none"></path>
                                            <path d="M22 13H25C26.1 13 27 13.9 27 15V23.5C27 24.9 28.1 26 29.5 26C30.9 26 32 24.9 32 23.5V14L29.5 11.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <line x1="4" y1="30" x2="24" y2="30" stroke-linecap="round"></line>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="charging-box-name">{{ $box['name'] }}</h2>
                                        <p class="charging-box-sub">
                                            <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                                <path d="M5 2V5H3V7C3 9.21 4.79 11 7 11V14H9V11C11.21 11 13 9.21 13 7V5H11V2H9V5H7V2H5Z"/>
                                            </svg>
                                            <span>{{ $box['connector_count'] }}</span>
                                        </p>
                                    </div>
                                </div>

                                <span class="box-status-pill box-status-{{ $box['status_tone'] }}">
                                    {{ $box['status_badge'] }}
                                </span>
                            </div>

                            <hr class="charging-box-divider">

                            <div class="charging-box-bottom">
                                <div>
                                    <p class="charging-box-spec">{{ $box['connector_spec'] }}</p>
                                    <p class="charging-box-price">{{ $box['price_label'] }}</p>
                                </div>
                                <span class="charging-box-est">{{ $box['estimation_label'] }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="tab-ulasan" class="detail-tab-panel" role="tabpanel" hidden data-tab-panel>
                <div class="reviews-list">
                    @foreach ($station['reviews'] as $review)
                        <article class="review-card">
                            <div class="review-card-top">
                                <strong>{{ $review['user'] }}</strong>
                                <span>{{ $review['time'] }} &middot; {{ $review['rating'] }}/5</span>
                            </div>
                            <p>{{ $review['comment'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section id="tab-fasilitas" class="detail-tab-panel" role="tabpanel" hidden data-tab-panel>
                <div class="facilities-meta-box">
                    <div>
                        <span class="meta-label">Jam Operasional</span>
                        <strong>{{ $station['operating_hours'] }}</strong>
                    </div>
                    <div>
                        <span class="meta-label">Aplikasi Operator</span>
                        <strong>{{ $station['require_app_name'] }}</strong>
                    </div>
                </div>
                <div class="facilities-list">
                    @foreach ($station['facilities'] as $facility)
                        <div class="facility-item">
                            <strong>{{ $facility['name'] }}</strong>
                            <p>{{ $facility['detail'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </main>
    </div>

    <div class="modal-backdrop" id="report-modal" hidden data-report-modal role="dialog" aria-modal="true" aria-labelledby="report-modal-title">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h2 id="report-modal-title" class="modal-title">Lapor Kondisi Stasiun</h2>
                    <p class="modal-subtitle">Bantu pengendara lain dengan memperbarui kondisi nyata {{ $station['name'] }}.</p>
                </div>
                <button type="button" class="modal-close-btn" aria-label="Tutup dialog laporan" data-report-modal-close>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M18 6L6 18M6 6L18 18" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('stations.report', $station['id']) }}" class="report-form">
                @csrf
                <div class="report-status-options">
                    <label class="report-radio-card">
                        <input type="radio" name="status" value="normal" checked>
                        <span>
                            <strong>Berfungsi Normal (Tersedia)</strong>
                            <small>Unit pengisian daya aktif dan siap digunakan.</small>
                        </span>
                    </label>
                    <label class="report-radio-card">
                        <input type="radio" name="status" value="busy">
                        <span>
                            <strong>Antrean Padat</strong>
                            <small>Seluruh konektor sedang dipakai dan terdapat antrean kendaraan.</small>
                        </span>
                    </label>
                    <label class="report-radio-card">
                        <input type="radio" name="status" value="technical_issue">
                        <span>
                            <strong>Kendala Teknis / Rusak</strong>
                            <small>Mesin mati, konektor rusak, atau gagal terhubung ke aplikasi.</small>
                        </span>
                    </label>
                </div>

                <div class="field-group">
                    <label for="report-notes">Catatan Tambahan (Opsional)</label>
                    <textarea id="report-notes" name="notes" rows="3" placeholder="Tuliskan detail kondisi konektor atau antrean di lokasi..."></textarea>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="primary-button">Kirim Laporan Kondisi</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="" defer></script>
</body>
</html>
