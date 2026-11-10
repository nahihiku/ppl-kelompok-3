@php
    $initialLimit = 30;
    $initialStations = $stations->take($initialLimit);
    $compactCatalog = $stations->map(fn (array $s): array => [
        'id' => (string) $s['id'],
        'n' => $s['name'],
        'op' => $s['operator_badge'],
        'st' => $s['current_status'],
        'stl' => $s['status_label'],
        'a' => $s['address'],
        'lat' => (float) $s['latitude'],
        'lng' => (float) $s['longitude'],
        'd' => round((float) $s['distance_km'], 2),
        'c' => $s['connector_type'],
        'csum' => $s['connector_summary'] ?? $s['connector_type'],
        'cs' => $s['connector_search'] ?? strtolower((string) $s['connector_type']),
        'p' => (float) $s['power_kw'],
        'cu' => $s['current_type'],
        'pr' => (int) $s['price_per_kwh'],
        'av' => (int) $s['available_chargers'],
        'tot' => (int) $s['total_chargers'],
        'url' => $s['detail_url'] ?? route('stations.show', $s['id']),
        'nav' => $s['direct_nav_url'] ?? '',
        'vc' => ($s['is_valid_coordinate'] ?? true) ? 1 : 0,
    ])->values();
@endphp

<div class="catalog-container" id="cari-stasiun">
    @if ($showIntro ?? false)
        <div class="catalog-intro">
            <h2 class="catalog-intro-title">Charging jadi lebih pasti.</h2>
            <p class="catalog-intro-subtitle">Cari berdasarkan lokasi, operator, dan pastikan konektor sesuai sebelum berangkat.</p>
        </div>
    @endif

    <form method="GET" action="{{ $formAction ?? route('stations.index') }}" class="search-filter-bar" data-station-search-form>
        <div class="search-input-wrap">
            <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="M20 20L16.5 16.5" stroke-linecap="round"></path>
            </svg>
            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Cari nama stasiun, kota, atau provinsi..."
                aria-label="Cari nama stasiun atau lokasi"
                data-station-search-input
            >
        </div>
        <button type="button" class="filter-toggle-btn" aria-label="Filter stasiun" aria-expanded="false" data-filter-toggle>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <line x1="4" y1="6" x2="20" y2="6"></line>
                <circle cx="9" cy="6" r="2" fill="#ffffff"></circle>
                <line x1="4" y1="12" x2="20" y2="12"></line>
                <circle cx="15" cy="12" r="2" fill="#ffffff"></circle>
                <line x1="4" y1="18" x2="20" y2="18"></line>
                <circle cx="10" cy="18" r="2" fill="#ffffff"></circle>
            </svg>
        </button>

        <div class="filter-panel" hidden data-filter-panel>
            <div class="filter-grid">
                <div class="filter-field">
                    <label for="filter-connector">Jenis Konektor</label>
                    <select id="filter-connector" name="connector" data-filter-connector>
                        <option value="">Semua Konektor</option>
                        <option value="CCS2" @selected(request('connector') === 'CCS2')>CCS2 (DC Fast)</option>
                        <option value="AC Type 2" @selected(request('connector') === 'AC Type 2')>AC Type 2</option>
                        <option value="DC Connector" @selected(request('connector') === 'DC Connector')>DC Connector</option>
                        <option value="AC Connector" @selected(request('connector') === 'AC Connector')>AC Connector</option>
                        <option value="CHAdeMO" @selected(request('connector') === 'CHAdeMO')>CHAdeMO</option>
                    </select>
                </div>
                <div class="filter-field">
                    <label for="filter-operator">Operator Penyedia</label>
                    <select id="filter-operator" name="operator" data-filter-operator>
                        <option value="">Semua Operator</option>
                        <option value="PLN" @selected(request('operator') === 'PLN')>PLN (SPKLU)</option>
                        <option value="SWASTA" @selected(request('operator') === 'SWASTA')>Swasta</option>
                        <option value="KOMERSIAL" @selected(request('operator') === 'KOMERSIAL')>Komersial</option>
                    </select>
                </div>
                <div class="filter-field">
                    <label for="filter-status">Status Operasional</label>
                    <select id="filter-status" name="status" data-filter-status>
                        <option value="">Semua Status</option>
                        <option value="normal" @selected(request('status') === 'normal')>Tersedia</option>
                        <option value="busy" @selected(request('status') === 'busy')>Antrean Padat</option>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <a href="{{ $formAction ?? route('stations.index') }}" class="filter-reset-link">Reset Filter</a>
                <button type="submit" class="btn-gradient-pill">Terapkan Filter</button>
            </div>
        </div>
    </form>

    <section
        class="stations-map-card"
        id="stations-overview-map"
        data-stations-map
        data-default-lat="-6.2088"
        data-default-lng="106.8456"
        aria-label="Peta Sebaran Stasiun Pengisian Daya"
    >
        <div class="stations-map-header">
            <div>
                <h2 class="stations-map-title">Peta Sebaran Stasiun Pengisian Daya</h2>
                <p class="stations-map-subtitle" data-map-subtitle>
                    Visualisasi interaktif titik SPKLU berdasarkan lokasi GPS perangkat dan kompatibilitas soket kendaraan Anda.
                </p>
            </div>
            <div class="stations-map-actions">
                <button type="button" class="btn-map-action" data-map-locate-btn>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M12 2v3m0 14v3M2 12h3m14 0h3" stroke-linecap="round"></path>
                        <circle cx="12" cy="12" r="7"></circle>
                    </svg>
                    <span>Lokasi Saya</span>
                </button>
                <button type="button" class="btn-map-action btn-map-secondary" data-map-fit-btn>
                    <span>Tampilkan Semua Pin</span>
                </button>
            </div>
        </div>

        <div class="stations-map-warning" role="alert" hidden data-map-warning></div>

        <div class="stations-map-legend" aria-label="Keterangan warna pin peta">
            <span class="map-legend-item">
                <span class="map-legend-dot is-normal" aria-hidden="true"></span>
                <span>Tersedia (Normal)</span>
            </span>
            <span class="map-legend-item">
                <span class="map-legend-dot is-busy" aria-hidden="true"></span>
                <span>Antrean Padat</span>
            </span>
            <span class="map-legend-item">
                <span class="map-legend-dot is-issue" aria-hidden="true"></span>
                <span>Kendala Teknis</span>
            </span>
            <span class="map-legend-item">
                <span class="map-legend-dot is-user" aria-hidden="true"></span>
                <span>Posisi Anda / Pusat Peta</span>
            </span>
        </div>

        <div class="stations-map-canvas-wrap" id="stations-leaflet-canvas" data-stations-map-canvas>
            <div class="map-fallback-canvas" aria-hidden="true">
                <svg viewBox="0 0 800 360" preserveAspectRatio="xMidYMid slice" class="map-fallback-svg">
                    <rect width="800" height="360" fill="#e2e8f0"/>
                    <path d="M0 70 Q260 95 480 180 T800 220" fill="none" stroke="#93c5fd" stroke-width="24"/>
                    <polygon points="180,95 330,75 370,190 210,210" fill="#bbf7d0" opacity="0.65"/>
                    <polygon points="460,165 620,145 660,275 495,290" fill="#bbf7d0" opacity="0.65"/>
                    <path d="M-20 120 L820 45 M90 -20 L240 380 M310 -20 L490 380 M-20 260 L820 190 M540 -20 L710 380" stroke="#ffffff" stroke-width="9"/>
                </svg>
            </div>
        </div>
    </section>

    <div class="nearest-header">
        <div>
            <h2 class="nearest-title">Stasiun Terdekat</h2>
            <p class="nearest-subtitle">Diurutkan berdasarkan jarak terdekat</p>
        </div>
        <span class="found-badge" data-stations-count>{{ $stations->count() }} Ditemukan</span>
    </div>

    <div class="station-cards-list" data-station-list>
        @forelse ($initialStations as $station)
            <article
                class="station-card"
                data-station-card
                data-station-id="{{ $station['id'] }}"
                data-title="{{ $station['name'] }}"
                data-address="{{ $station['address'] }}"
                data-name="{{ strtolower($station['name'].' '.$station['address'].' '.$station['operator_badge']) }}"
                data-connector="{{ $station['connector_type'] }}"
                data-connector-summary="{{ $station['connector_summary'] ?? $station['connector_type'] }}"
                data-connectors="{{ $station['connector_search'] ?? strtolower($station['connector_type']) }}"
                data-operator="{{ $station['operator_badge'] }}"
                data-status="{{ $station['current_status'] }}"
                data-status-label="{{ $station['status_label'] }}"
                data-power="{{ $station['power_kw'] }} kW ({{ $station['current_type'] }})"
                data-price="{{ $station['price_per_kwh'] > 0 ? 'Rp '.number_format($station['price_per_kwh'], 0, '', '').'/kWh' : 'Tarif belum diketahui' }}"
                data-chargers="{{ $station['available_chargers'] }}/{{ $station['total_chargers'] }} Konektor"
                data-detail-url="{{ route('stations.show', $station['id']) }}"
                data-nav-url="{{ $station['direct_nav_url'] ?? '' }}"
                data-valid-coords="{{ ($station['is_valid_coordinate'] ?? true) ? '1' : '0' }}"
                data-lat="{{ $station['latitude'] }}"
                data-lng="{{ $station['longitude'] }}"
            >
                <div class="station-card-top">
                    <div class="station-badges">
                        <span class="badge-operator">{{ $station['operator_badge'] }}</span>
                        <span class="badge-status badge-status-{{ $station['current_status'] }}">
                            <span class="badge-status-dot" aria-hidden="true"></span>
                            {{ $station['status_label'] }}
                        </span>
                    </div>
                    <div class="station-distance-box">
                        <strong data-station-distance>{{ number_format($station['distance_km'], 1, '.', '') }} km</strong>
                        <span>dari lokasimu</span>
                    </div>
                </div>

                <h3 class="station-card-title">{{ $station['name'] }}</h3>

                <p class="station-card-address">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M12 21C16 16.8 19 13.4 19 9.5C19 5.63401 15.866 2.5 12 2.5C8.13401 2.5 5 5.63401 5 9.5C5 13.4 8 16.8 12 21Z"></path>
                        <circle cx="12" cy="9.5" r="2.5"></circle>
                    </svg>
                    <span>{{ $station['address'] }}</span>
                </p>

                <hr class="station-card-divider">

                <div class="station-specs-row">
                    <div class="spec-col">
                        <span class="spec-label">Konektor</span>
                        <div class="spec-connector-val">
                            <span class="spec-connector-name">{{ $station['connector_summary'] ?? $station['connector_type'] }}</span>
                            <span class="spec-connector-dot" aria-hidden="true"></span>
                            <span class="spec-connector-power">
                                {{ $station['power_kw'] }} kW<br>{{ $station['current_type'] }}
                            </span>
                        </div>
                    </div>
                    <div class="spec-col spec-col-right">
                        <span class="spec-label">Tarif</span>
                        <div class="spec-tariff-val">
                            @if ($station['price_per_kwh'] > 0)
                                <strong>Rp {{ number_format($station['price_per_kwh'], 0, '', '') }}</strong>
                                <span>/kWh</span>
                            @else
                                <strong>Belum diketahui</strong>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="station-card-footer">
                    <div class="charger-avail-info">
                        <span class="charger-avail-label">Charger Tersedia</span>
                        <span class="charger-avail-count">{{ $station['available_chargers'] }} dari {{ $station['total_chargers'] }}</span>
                    </div>
                    <div class="station-card-cta-group">
                        <button type="button" class="btn-outline-pill" data-focus-station-map="{{ $station['id'] }}">
                            Lihat di Peta
                        </button>
                        <a href="{{ route('stations.show', $station['id']) }}" class="btn-gradient-pill">Lihat Detail</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="empty-station-state">
                <p>Tidak ada stasiun pengisian daya yang sesuai dengan kriteria pencarian Anda.</p>
                <a href="{{ route('stations.index') }}" class="btn-gradient-pill">Tampilkan Semua Stasiun</a>
            </div>
        @endforelse
    </div>

    <div class="catalog-load-more-wrap" data-load-more-wrap @if($stations->count() <= $initialLimit) hidden @endif>
        <button type="button" class="btn-outline-pill btn-load-more" data-load-more-stations>
            Muat Lebih Banyak Stasiun
        </button>
    </div>

    <p class="catalog-attribution">
        Sumber data sebaran SPKLU: Katalog terkurasi CasIn.id, OpenStreetMap (<a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">ODbL</a>), serta dataset publik petaspklu.id / PT PLN (Persero) melalui Mendeley Data v2.0 (<a href="https://doi.org/10.17632/z2bvd5r574.2" target="_blank" rel="noopener noreferrer">CC BY-NC 3.0</a>) dan spesifikasi soket PLN Mobile / Kementerian ESDM.
    </p>

    <script type="application/json" id="casin-stations-catalog">@json($compactCatalog)</script>
</div>
