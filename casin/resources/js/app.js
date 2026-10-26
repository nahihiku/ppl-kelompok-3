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

const AUTH_TOKEN_KEY = 'casin.auth_token';
const AUTH_USER_KEY = 'casin.auth_user';

function showAuthErrors(form, errors, fallbackMessage = 'Periksa kembali data yang kamu masukkan.') {
    form.querySelectorAll('[data-client-error]').forEach((element) => element.remove());
    const messages = errors && typeof errors === 'object' ? errors : {};
    const fieldGroups = form.querySelectorAll('.field-group');

    Object.entries(messages).forEach(([field, fieldMessages]) => {
        const input = form.querySelector(`[name="${field}"]`);
        const group = input?.closest('.field-group');
        if (!group) return;
        const message = Array.isArray(fieldMessages) ? fieldMessages[0] : fieldMessages;
        const error = document.createElement('p');
        error.className = 'field-error';
        error.dataset.clientError = 'true';
        error.textContent = message;
        group.appendChild(error);
        input.setAttribute('aria-invalid', 'true');
    });

    const general = form.closest('.auth-card')?.querySelector('[data-auth-api-error]');
    if (general) {
        const firstFieldMessage = Object.values(messages).flat()[0];
        general.textContent = firstFieldMessage || fallbackMessage;
        general.hidden = false;
    }
}

function clearAuthErrors(form) {
    form.querySelectorAll('[data-client-error]').forEach((element) => element.remove());
    form.querySelectorAll('[aria-invalid="true"]').forEach((input) => input.removeAttribute('aria-invalid'));
    const general = form.closest('.auth-card')?.querySelector('[data-auth-api-error]');
    if (general) general.hidden = true;
}

async function postAuthForm(form) {
    clearAuthErrors(form);
    const button = form.querySelector('button[type="submit"]');
    const originalLabel = button?.textContent;
    if (button) {
        button.disabled = true;
        button.textContent = 'Memproses…';
    }

    try {
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.device_name = 'casin_web';
        const response = await fetch(form.dataset.apiEndpoint, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const result = await response.json();
        if (!response.ok) {
            showAuthErrors(form, result.errors, result.message);
            return;
        }

        localStorage.setItem(AUTH_TOKEN_KEY, result.data.token);
        localStorage.setItem(AUTH_USER_KEY, JSON.stringify(result.data.user));
        window.location.assign(form.dataset.authForm === 'register' ? '/' : '/');
    } catch {
        showAuthErrors(form, null, 'Koneksi gagal. Periksa internetmu lalu coba lagi.');
    } finally {
        if (button) {
            button.disabled = false;
            button.textContent = originalLabel;
        }
    }
}

function initApiAuthForms() {
    document.querySelectorAll('[data-auth-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            postAuthForm(form);
        });
    });
}

function initGoogleAuth() {
    document.querySelectorAll('[data-google-auth]').forEach((button) => {
        button.addEventListener('click', () => {
            const clientId = button.dataset.googleClientId;
            const authCard = button.closest('.auth-card');
            const showError = (message) => {
                const error = authCard?.querySelector('[data-auth-api-error]');
                if (error) { error.textContent = message; error.hidden = false; }
            };

            if (!clientId) {
                showError('Login Google belum dikonfigurasi. Silakan gunakan email dan kata sandi.');
                return;
            }

            const start = () => {
                if (!window.google?.accounts?.oauth2) {
                    showError('Layanan Google belum siap. Coba lagi beberapa saat.');
                    return;
                }

                button.disabled = true;
                const tokenClient = window.google.accounts.oauth2.initTokenClient({
                    client_id: clientId,
                    scope: 'openid email profile',
                    callback: async (tokenResponse) => {
                        try {
                            if (!tokenResponse.access_token) throw new Error('Token Google tidak diterima.');
                            const response = await fetch(button.dataset.apiEndpoint, {
                                method: 'POST',
                                headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                                body: JSON.stringify({ access_token: tokenResponse.access_token, device_name: 'casin_web_google' }),
                            });
                            const result = await response.json();
                            if (!response.ok) throw new Error(result.message || 'Autentikasi Google gagal.');
                            localStorage.setItem(AUTH_TOKEN_KEY, result.data.token);
                            localStorage.setItem(AUTH_USER_KEY, JSON.stringify(result.data.user));
                            window.location.assign('/');
                        } catch (error) {
                            showError(error.message);
                        } finally {
                            button.disabled = false;
                        }
                    },
                    error_callback: () => {
                        button.disabled = false;
                        showError('Login Google dibatalkan atau tidak berhasil.');
                    },
                });
                tokenClient.requestAccessToken({ prompt: 'select_account' });
            };

            if (window.google?.accounts?.oauth2) {
                start();
                return;
            }

            window.setTimeout(start, 500);
        });
    });
}

function initClientAuthState() {
    const token = localStorage.getItem(AUTH_TOKEN_KEY);
    let user = null;
    try {
        user = JSON.parse(localStorage.getItem(AUTH_USER_KEY) || 'null');
    } catch {
        localStorage.removeItem(AUTH_USER_KEY);
    }
    const actions = document.querySelector('.landing-page .nav-actions');
    if (!token || !user || !actions || !actions.querySelector('.nav-join')) return;

    actions.innerHTML = `
        <span class="welcome-user">Hai, ${user.name || user.email}</span>
        <a class="nav-stations-link" href="/stasiun">Daftar Stasiun</a>
        <button class="nav-login-pill" type="button" data-client-logout>Keluar</button>
    `;
    actions.querySelector('[data-client-logout]')?.addEventListener('click', async () => {
        try {
            await fetch('/api/logout', {
                method: 'POST',
                headers: { Accept: 'application/json', Authorization: `Bearer ${token}` },
            });
        } finally {
            localStorage.removeItem(AUTH_TOKEN_KEY);
            localStorage.removeItem(AUTH_USER_KEY);
            window.location.reload();
        }
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
initApiAuthForms();
initGoogleAuth();
initClientAuthState();
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
