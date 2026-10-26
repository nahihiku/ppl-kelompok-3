import './bootstrap';
import { initLocationWidget, readStoredPosition } from './geolocation';

const ACTIVE_VEHICLE_KEY = 'casin.active_vehicle';
const FAVORITES_KEY = 'casin.favorite_stations';

function calculateHaversineKm(lat1, lon1, lat2, lon2) {
    const toRad = (deg) => (deg * Math.PI) / 180;
    const R = 6371;
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
}

function updateStationDistances(position) {
    if (!position || !Number.isFinite(position.latitude) || !Number.isFinite(position.longitude)) {
        return;
    }

    const listEl = document.querySelector('[data-station-list]');
    const cards = Array.from(document.querySelectorAll('[data-station-card]'));

    cards.forEach((card) => {
        const lat = Number(card.dataset.lat);
        const lng = Number(card.dataset.lng);
        if (Number.isFinite(lat) && Number.isFinite(lng)) {
            const km = calculateHaversineKm(position.latitude, position.longitude, lat, lng);
            card.dataset.distanceKm = km.toFixed(2);
            const distLabel = card.querySelector('[data-station-distance]');
            if (distLabel) {
                distLabel.textContent = `${km.toFixed(1)} km`;
            }
        }
    });

    if (listEl && cards.length > 1) {
        cards
            .sort((a, b) => Number(a.dataset.distanceKm ?? 9999) - Number(b.dataset.distanceKm ?? 9999))
            .forEach((card) => listEl.appendChild(card));
    }

    const detailDist = document.querySelector('[data-detail-distance]');
    if (detailDist) {
        const lat = Number(detailDist.dataset.lat);
        const lng = Number(detailDist.dataset.lng);
        if (Number.isFinite(lat) && Number.isFinite(lng)) {
            const km = calculateHaversineKm(position.latitude, position.longitude, lat, lng);
            detailDist.textContent = `${km.toFixed(1)} km`;
        }
    }
}

function initPasswordToggles() {
    document.querySelectorAll('[data-password-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const wrapper = btn.closest('.input-with-icon');
            const input = wrapper?.querySelector('[data-password-input]');
            if (!input) {
                return;
            }
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });
}

function initFilterAndSearch() {
    const toggleBtn = document.querySelector('[data-filter-toggle]');
    const panel = document.querySelector('[data-filter-panel]');

    if (toggleBtn && panel) {
        toggleBtn.addEventListener('click', () => {
            const isHidden = panel.hasAttribute('hidden');
            if (isHidden) {
                panel.removeAttribute('hidden');
                toggleBtn.setAttribute('aria-expanded', 'true');
            } else {
                panel.setAttribute('hidden', '');
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    const searchInput = document.querySelector('[data-station-search-input]');
    const cards = Array.from(document.querySelectorAll('[data-station-card]'));
    const countBadge = document.querySelector('[data-stations-count]');

    if (searchInput && cards.length > 0) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            cards.forEach((card) => {
                const name = (card.dataset.name || '').toLowerCase();
                const matches = query === '' || name.includes(query);
                card.style.display = matches ? '' : 'none';
                if (matches) {
                    visibleCount += 1;
                }
            });

            if (countBadge) {
                countBadge.textContent = `${visibleCount} Ditemukan`;
            }
        });
    }
}

function initVehicleSelector() {
    const modal = document.querySelector('[data-vehicle-modal]');
    const openBtns = document.querySelectorAll('[data-vehicle-modal-open]');
    const closeBtn = document.querySelector('[data-vehicle-modal-close]');
    const resetBtn = document.querySelector('[data-vehicle-reset]');
    const options = document.querySelectorAll('[data-vehicle-option]');
    const brandEls = document.querySelectorAll('[data-active-vehicle-brand]');
    const connectorEls = document.querySelectorAll('[data-active-vehicle-connector]');

    const applyVehicle = (vehicle) => {
        brandEls.forEach((el) => {
            el.textContent = vehicle ? vehicle.brand : 'Merek Kendaraan';
        });
        connectorEls.forEach((el) => {
            el.textContent = vehicle ? `Konektor ${vehicle.connector}` : 'Jenis Konektor';
        });

        options.forEach((opt) => {
            opt.classList.toggle('is-selected', Boolean(vehicle && opt.dataset.vehicleBrand === vehicle.brand));
        });
    };

    try {
        const saved = JSON.parse(localStorage.getItem(ACTIVE_VEHICLE_KEY) || 'null');
        if (saved && saved.brand) {
            applyVehicle(saved);
        }
    } catch {
        // Abaikan jika penyimpanan lokal tidak tersedia
    }

    if (!modal) {
        return;
    }

    openBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            modal.removeAttribute('hidden');
        });
    });

    closeBtn?.addEventListener('click', () => {
        modal.setAttribute('hidden', '');
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.setAttribute('hidden', '');
        }
    });

    options.forEach((opt) => {
        opt.addEventListener('click', () => {
            const selected = {
                brand: opt.dataset.vehicleBrand,
                connector: opt.dataset.vehicleConnector,
            };
            try {
                localStorage.setItem(ACTIVE_VEHICLE_KEY, JSON.stringify(selected));
            } catch {
                // Abaikan jika gagal menyimpan
            }
            applyVehicle(selected);
            modal.setAttribute('hidden', '');
        });
    });

    resetBtn?.addEventListener('click', () => {
        try {
            localStorage.removeItem(ACTIVE_VEHICLE_KEY);
        } catch {
            // Abaikan
        }
        applyVehicle(null);
        modal.setAttribute('hidden', '');
    });
}

function initMobileMenu() {
    const toggle = document.querySelector('[data-menu-toggle]');
    const drawer = document.querySelector('[data-mobile-drawer]');
    if (!toggle || !drawer) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isHidden = drawer.hasAttribute('hidden');
        if (isHidden) {
            drawer.removeAttribute('hidden');
            toggle.setAttribute('aria-expanded', 'true');
        } else {
            drawer.setAttribute('hidden', '');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
}

function initDetailInteractions() {
    const tabs = document.querySelectorAll('[data-tab-target]');
    const panels = document.querySelectorAll('[data-tab-panel]');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const targetId = tab.dataset.tabTarget;
            tabs.forEach((t) => {
                const active = t === tab;
                t.classList.toggle('is-active', active);
                t.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            panels.forEach((panel) => {
                if (panel.id === targetId) {
                    panel.removeAttribute('hidden');
                } else {
                    panel.setAttribute('hidden', '');
                }
            });
        });
    });

    const favBtn = document.querySelector('[data-favorite-toggle]');
    if (favBtn) {
        const stationId = favBtn.dataset.favoriteToggle;
        const readFavs = () => {
            try {
                return JSON.parse(localStorage.getItem(FAVORITES_KEY) || '[]');
            } catch {
                return [];
            }
        };
        const renderFav = (isFav) => {
            favBtn.classList.toggle('is-favorited', isFav);
            favBtn.setAttribute('aria-pressed', isFav ? 'true' : 'false');
        };

        renderFav(readFavs().includes(stationId));

        favBtn.addEventListener('click', () => {
            const current = readFavs();
            const next = current.includes(stationId)
                ? current.filter((id) => id !== stationId)
                : [...current, stationId];
            try {
                localStorage.setItem(FAVORITES_KEY, JSON.stringify(next));
            } catch {
                // Abaikan
            }
            renderFav(next.includes(stationId));
        });
    }

    const shareBtn = document.querySelector('[data-share-station]');
    const shareFeedback = document.querySelector('[data-share-feedback]');
    if (shareBtn) {
        shareBtn.addEventListener('click', async () => {
            const shareUrl = window.location.href;
            const stationName = shareBtn.dataset.shareStation || 'Stasiun CasIn.id';
            if (navigator.share) {
                try {
                    await navigator.share({ title: stationName, url: shareUrl });
                    return;
                } catch {
                    // Lanjut ke salin tautan
                }
            }
            try {
                await navigator.clipboard.writeText(shareUrl);
                if (shareFeedback) {
                    shareFeedback.textContent = 'Tautan lokasi stasiun berhasil disalin.';
                    shareFeedback.removeAttribute('hidden');
                }
            } catch {
                // Abaikan
            }
        });
    }

    const reportModal = document.querySelector('[data-report-modal]');
    const reportOpen = document.querySelector('[data-report-modal-open]');
    const reportClose = document.querySelector('[data-report-modal-close]');

    if (reportModal && reportOpen) {
        reportOpen.addEventListener('click', () => reportModal.removeAttribute('hidden'));
        reportClose?.addEventListener('click', () => reportModal.setAttribute('hidden', ''));
        reportModal.addEventListener('click', (event) => {
            if (event.target === reportModal) {
                reportModal.setAttribute('hidden', '');
            }
        });
    }

    const mapContainer = document.getElementById('station-detail-map');
    if (mapContainer && window.L) {
        const lat = Number(mapContainer.dataset.lat);
        const lng = Number(mapContainer.dataset.lng);
        const name = mapContainer.dataset.name || 'SPKLU';
        if (Number.isFinite(lat) && Number.isFinite(lng)) {
            const fallback = mapContainer.querySelector('.map-fallback-canvas');
            const mapDiv = document.createElement('div');
            mapDiv.style.width = '100%';
            mapDiv.style.height = '100%';
            mapContainer.insertBefore(mapDiv, fallback);
            if (fallback) {
                fallback.style.display = 'none';
            }

            const map = window.L.map(mapDiv, { zoomControl: false }).setView([lat, lng], 15);
            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap',
            }).addTo(map);

            const pinIcon = window.L.divIcon({
                className: 'custom-leaflet-pin',
                html: '<svg viewBox="0 0 44 56" width="40" height="52" fill="none"><path d="M22 0C9.85 0 0 9.85 0 22C0 37.4 22 56 22 56C22 56 44 37.4 44 22C44 9.85 34.15 0 22 0Z" fill="#EF4444"/><circle cx="22" cy="21" r="8.5" fill="#FFFFFF"/></svg>',
                iconSize: [40, 52],
                iconAnchor: [20, 52],
            });

            window.L.marker([lat, lng], { icon: pinIcon }).addTo(map).bindPopup(name);
        }
    }
}

initLocationWidget();
initPasswordToggles();
initFilterAndSearch();
initVehicleSelector();
initMobileMenu();
initDetailInteractions();

const storedPos = readStoredPosition();
if (storedPos) {
    updateStationDistances(storedPos);
}

const locBtn = document.querySelector('[data-location-button]');
if (locBtn) {
    locBtn.addEventListener('click', () => {
        setTimeout(() => {
            const latest = readStoredPosition();
            if (latest) {
                updateStationDistances(latest);
            }
        }, 1200);
    });
}
