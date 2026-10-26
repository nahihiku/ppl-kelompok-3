<div class="modal-backdrop" id="vehicle-modal" hidden data-vehicle-modal role="dialog" aria-modal="true" aria-labelledby="vehicle-modal-title">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h2 id="vehicle-modal-title" class="modal-title">Pilih Kendaraan Aktif</h2>
                <p class="modal-subtitle">Stasiun pengisian akan disesuaikan secara otomatis dengan jenis konektor kendaraanmu.</p>
            </div>
            <button type="button" class="modal-close-btn" aria-label="Tutup dialog" data-vehicle-modal-close>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M18 6L6 18M6 6L18 18" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div class="vehicle-options-list">
            @foreach ($vehicles as $vehicle)
                <button
                    type="button"
                    class="vehicle-option-item"
                    data-vehicle-option
                    data-vehicle-brand="{{ $vehicle['brand'] }} {{ $vehicle['model'] }}"
                    data-vehicle-connector="{{ $vehicle['default_connector_type'] }}"
                    data-vehicle-category="{{ $vehicle['category'] }}"
                >
                    <span class="vehicle-option-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M5 11L6.5 6.5C6.8 5.6 7.6 5 8.6 5H15.4C16.4 5 17.2 5.6 17.5 6.5L19 11M3 16V12.5C3 11.7 3.7 11 4.5 11H19.5C20.3 11 21 11.7 21 12.5V16C21 16.6 20.6 17 20 17H19V18.5C19 19.1 18.6 19.5 18 19.5H17C16.4 19.5 16 19.1 16 18.5V17H8V18.5C8 19.1 7.6 19.5 7 19.5H6C5.4 19.5 5 19.1 5 18.5V17H4C3.4 17 3 16.6 3 16Z" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="7" cy="14" r="1" fill="currentColor"/>
                            <circle cx="17" cy="14" r="1" fill="currentColor"/>
                        </svg>
                    </span>
                    <span class="vehicle-option-body">
                        <strong>{{ $vehicle['brand'] }} {{ $vehicle['model'] }}</strong>
                        <span>{{ $vehicle['category'] }} &middot; Konektor {{ $vehicle['default_connector_type'] }} &middot; {{ $vehicle['battery_capacity_kwh'] }} kWh</span>
                    </span>
                    <span class="vehicle-option-badge">{{ $vehicle['default_connector_type'] }}</span>
                </button>
            @endforeach
        </div>

        <div class="modal-footer">
            <button type="button" class="vehicle-reset-btn" data-vehicle-reset>Tampilkan Semua Konektor</button>
        </div>
    </div>
</div>
