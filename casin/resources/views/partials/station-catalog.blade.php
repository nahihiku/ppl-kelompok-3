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
                placeholder="Cari nama stasiun atau lokasi..."
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
                    </select>
                </div>
                <div class="filter-field">
                    <label for="filter-operator">Operator Penyedia</label>
                    <select id="filter-operator" name="operator" data-filter-operator>
                        <option value="">Semua Operator</option>
                        <option value="PLN" @selected(request('operator') === 'PLN')>PLN (SPKLU)</option>
                        <option value="SWASTA" @selected(request('operator') === 'SWASTA')>Swasta</option>
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

    <div class="nearest-header">
        <div>
            <h2 class="nearest-title">Stasiun Terdekat</h2>
            <p class="nearest-subtitle">Diurutkan berdasarkan jarak terdekat</p>
        </div>
        <span class="found-badge" data-stations-count>{{ $stations->count() }} Ditemukan</span>
    </div>

    <div class="station-cards-list" data-station-list>
        @forelse ($stations as $station)
            <article
                class="station-card"
                data-station-card
                data-name="{{ strtolower($station['name'].' '.$station['address'].' '.$station['operator_badge']) }}"
                data-connector="{{ $station['connector_type'] }}"
                data-operator="{{ $station['operator_badge'] }}"
                data-status="{{ $station['current_status'] }}"
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
                            <span class="spec-connector-name">{{ $station['connector_type'] }}</span>
                            <span class="spec-connector-dot" aria-hidden="true"></span>
                            <span class="spec-connector-power">
                                {{ $station['power_kw'] }} kW<br>{{ $station['current_type'] }}
                            </span>
                        </div>
                    </div>
                    <div class="spec-col spec-col-right">
                        <span class="spec-label">Tarif</span>
                        <div class="spec-tariff-val">
                            <strong>Rp {{ number_format($station['price_per_kwh'], 0, '', '') }}</strong>
                            <span>/kWh</span>
                        </div>
                    </div>
                </div>

                <div class="station-card-footer">
                    <div class="charger-avail-info">
                        <span class="charger-avail-label">Charger Tersedia</span>
                        <span class="charger-avail-count">{{ $station['available_chargers'] }} dari {{ $station['total_chargers'] }}</span>
                    </div>
                    <a href="{{ route('stations.show', $station['id']) }}" class="btn-gradient-pill">Lihat Detail</a>
                </div>
            </article>
        @empty
            <div class="empty-station-state">
                <p>Tidak ada stasiun pengisian daya yang sesuai dengan kriteria pencarian Anda.</p>
                <a href="{{ route('stations.index') }}" class="btn-gradient-pill">Tampilkan Semua Stasiun</a>
            </div>
        @endforelse
    </div>
</div>
