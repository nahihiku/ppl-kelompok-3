import './bootstrap';
import { DEFAULT_POSITION, initLocationWidget, readStoredPosition } from './geolocation';
import {
    calculateHaversineKm,
    initStationDetailMap,
    initStationsOverviewMap,
} from './map';

const ACTIVE_VEHICLE_KEY = 'casin.active_vehicle';
const FAVORITES_KEY = 'casin.favorite_stations';
const STATION_PAGE_SIZE = 30;
let overviewMapInstance = null;

const catalogState = {
    initialized: false,
    all: [],
    filtered: [],
    visibleLimit: STATION_PAGE_SIZE,
    resetUrl: '/stasiun',
};

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function readActiveVehicle() {
    try {
        const parsed = JSON.parse(localStorage.getItem(ACTIVE_VEHICLE_KEY) || 'null');
        if (parsed && parsed.brand && parsed.connector) {
            return parsed;
        }
    } catch {
        // Abaikan jika penyimpanan lokal tidak tersedia
    }
    return null;
}

function initStationsCatalogState() {
    if (catalogState.initialized) {
        return catalogState.all.length > 0;
    }

    const searchForm = document.querySelector('[data-station-search-form]');
    if (searchForm?.getAttribute('action')) {
        catalogState.resetUrl = searchForm.getAttribute('action');
    }

    const catalogEl = document.getElementById('casin-stations-catalog');
    if (catalogEl) {
        try {
            const parsed = JSON.parse(catalogEl.textContent || '[]');
            if (Array.isArray(parsed) && parsed.length > 0) {
                catalogState.all = parsed;
                catalogState.filtered = [...parsed];
                catalogState.initialized = true;
                return true;
            }
        } catch {
            // Fallback ke pembacaan kartu DOM jika JSON katalog gagal diparsing
        }
    }

    const domCards = Array.from(document.querySelectorAll('[data-station-card]'));
    if (domCards.length > 0) {
        catalogState.all = domCards.map((card) => {
            const distText = card.querySelector('[data-station-distance]')?.textContent || '0';
            const powerText = card.dataset.power || '0 kW (-)';
            const powerMatch = powerText.match(/^([\d.]+)\s*kW\s*\(([^)]+)\)/i);
            const chargerText = card.dataset.chargers || '0/0 Konektor';
            const chargerMatch = chargerText.match(/(\d+)\s*\/\s*(\d+)/);
            const priceText = card.dataset.price || '';
            const priceMatch = priceText.match(/Rp\s*([\d.]+)/i);

            return {
                id: String(card.dataset.stationId || ''),
                n: card.dataset.title || '',
                op: card.dataset.operator || 'PLN',
                st: card.dataset.status || 'normal',
                stl: card.dataset.statusLabel || 'Tersedia',
                a: card.dataset.address || '',
                lat: Number(card.dataset.lat),
                lng: Number(card.dataset.lng),
                d: parseFloat(distText) || 0,
                c: card.dataset.connector || 'Belum diketahui',
                csum: card.dataset.connectorSummary || card.dataset.connector || 'Belum diketahui',
                cs: card.dataset.connectors || (card.dataset.connector || '').toLowerCase(),
                p: powerMatch ? Number(powerMatch[1]) : 0,
                cu: powerMatch ? powerMatch[2] : '-',
                pr: priceMatch ? Number(priceMatch[1].replace(/\./g, '')) : 0,
                av: chargerMatch ? Number(chargerMatch[1]) : 0,
                tot: chargerMatch ? Number(chargerMatch[2]) : 0,
                url: card.dataset.detailUrl || '#',
                nav: card.dataset.navUrl || '',
                vc: card.dataset.validCoords === '0' ? 0 : 1,
            };
        });
        catalogState.filtered = [...catalogState.all];
        catalogState.initialized = true;
        return true;
    }

    catalogState.initialized = true;
    return false;
}

function buildStationCardHtml(station) {
    const id = escapeHtml(station.id);
    const name = escapeHtml(station.n);
    const address = escapeHtml(station.a);
    const operator = escapeHtml(station.op);
    const status = escapeHtml(station.st);
    const statusLabel = escapeHtml(station.stl);
    const connector = escapeHtml(station.c);
    const connectorSummary = escapeHtml(station.csum || station.c);
    const connectorTokens = escapeHtml(station.cs || String(station.c || '').toLowerCase());
    const powerKw = Number(station.p ?? 0);
    const currentType = escapeHtml(station.cu || '-');
    const price = Number(station.pr ?? 0);
    const available = Number(station.av ?? 0);
    const total = Number(station.tot ?? 0);
    const detailUrl = escapeHtml(station.url || `/stasiun/${station.id}`);
    const navUrl = escapeHtml(station.nav || '');
    const validCoords = station.vc ? '1' : '0';
    const distanceKm = Number.isFinite(Number(station.d)) ? Number(station.d).toFixed(1) : '0.0';
    const searchName = escapeHtml(`${station.n || ''} ${station.a || ''} ${station.op || ''}`.toLowerCase());
    const priceAttr = price > 0 ? `Rp ${Math.round(price)}/kWh` : 'Tarif belum diketahui';

    const tariffHtml = price > 0
        ? `<strong>Rp ${Math.round(price)}</strong><span>/kWh</span>`
        : '<strong>Belum diketahui</strong>';

    return `
        <article
            class="station-card"
            data-station-card
            data-station-id="${id}"
            data-title="${name}"
            data-address="${address}"
            data-name="${searchName}"
            data-connector="${connector}"
            data-connector-summary="${connectorSummary}"
            data-connectors="${connectorTokens}"
            data-operator="${operator}"
            data-status="${status}"
            data-status-label="${statusLabel}"
            data-power="${powerKw} kW (${currentType})"
            data-price="${escapeHtml(priceAttr)}"
            data-chargers="${available}/${total} Konektor"
            data-detail-url="${detailUrl}"
            data-nav-url="${navUrl}"
            data-valid-coords="${validCoords}"
            data-lat="${escapeHtml(station.lat)}"
            data-lng="${escapeHtml(station.lng)}"
        >
            <div class="station-card-top">
                <div class="station-badges">
                    <span class="badge-operator">${operator}</span>
                    <span class="badge-status badge-status-${status}">
                        <span class="badge-status-dot" aria-hidden="true"></span>
                        ${statusLabel}
                    </span>
                </div>
                <div class="station-distance-box">
                    <strong data-station-distance>${distanceKm} km</strong>
                    <span>dari lokasimu</span>
                </div>
            </div>

            <h3 class="station-card-title">${name}</h3>

            <p class="station-card-address">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path d="M12 21C16 16.8 19 13.4 19 9.5C19 5.63401 15.866 2.5 12 2.5C8.13401 2.5 5 5.63401 5 9.5C5 13.4 8 16.8 12 21Z"></path>
                    <circle cx="12" cy="9.5" r="2.5"></circle>
                </svg>
                <span>${address}</span>
            </p>

            <hr class="station-card-divider">

            <div class="station-specs-row">
                <div class="spec-col">
                    <span class="spec-label">Konektor</span>
                    <div class="spec-connector-val">
                        <span class="spec-connector-name">${connectorSummary}</span>
                        <span class="spec-connector-dot" aria-hidden="true"></span>
                        <span class="spec-connector-power">
                            ${powerKw} kW<br>${currentType}
                        </span>
                    </div>
                </div>
                <div class="spec-col spec-col-right">
                    <span class="spec-label">Tarif</span>
                    <div class="spec-tariff-val">${tariffHtml}</div>
                </div>
            </div>

            <div class="station-card-footer">
                <div class="charger-avail-info">
                    <span class="charger-avail-label">Charger Tersedia</span>
                    <span class="charger-avail-count">${available} dari ${total}</span>
                </div>
                <div class="station-card-cta-group">
                    <button type="button" class="btn-outline-pill" data-focus-station-map="${id}">
                        Lihat di Peta
                    </button>
                    <a href="${detailUrl}" class="btn-gradient-pill">Lihat Detail</a>
                </div>
            </div>
        </article>
    `;
}

function renderFilteredStationCards() {
    const listEl = document.querySelector('[data-station-list]');
    const countBadge = document.querySelector('[data-stations-count]');
    const loadMoreWrap = document.querySelector('[data-load-more-wrap]');
    if (!listEl) {
        return;
    }

    const totalMatches = catalogState.filtered.length;
    if (countBadge) {
        countBadge.textContent = `${totalMatches} Ditemukan`;
    }

    if (totalMatches === 0) {
        listEl.innerHTML = `
            <div class="empty-station-state">
                <p>Tidak ada stasiun pengisian daya yang sesuai dengan kriteria pencarian Anda.</p>
                <a href="${escapeHtml(catalogState.resetUrl)}" class="btn-gradient-pill">Tampilkan Semua Stasiun</a>
            </div>
        `;
        if (loadMoreWrap) {
            loadMoreWrap.hidden = true;
        }
        return;
    }

    const visibleSlice = catalogState.filtered.slice(0, catalogState.visibleLimit);
    listEl.innerHTML = visibleSlice.map(buildStationCardHtml).join('');

    if (loadMoreWrap) {
        loadMoreWrap.hidden = totalMatches <= catalogState.visibleLimit;
    }
}

function matchesConnectorToken(station, connectorLower) {
    if (!connectorLower) {
        return true;
    }
    const primary = String(station.c || '').toLowerCase();
    const tokens = String(station.cs || '').toLowerCase();
    return primary === connectorLower || tokens.includes(connectorLower);
}

function applyClientStationFilters(shouldFitMap = false, resetPagination = true) {
    if (!initStationsCatalogState() || catalogState.all.length === 0) {
        return;
    }

    if (resetPagination) {
        catalogState.visibleLimit = STATION_PAGE_SIZE;
    }

    const searchInput = document.querySelector('[data-station-search-input]');
    const connectorSelect = document.querySelector('[data-filter-connector]');
    const operatorSelect = document.querySelector('[data-filter-operator]');
    const statusSelect = document.querySelector('[data-filter-status]');

    const query = (searchInput?.value || '').trim().toLowerCase();
    const selectConnector = (connectorSelect?.value || '').trim().toLowerCase();
    const selectOperator = (operatorSelect?.value || '').trim().toLowerCase();
    const selectStatus = (statusSelect?.value || '').trim().toLowerCase();
    const activeVehicle = readActiveVehicle();
    const vehicleConnector = (activeVehicle?.connector || '').trim().toLowerCase();

    catalogState.filtered = catalogState.all.filter((station) => {
        if (query !== '') {
            const haystack = `${station.n || ''} ${station.a || ''} ${station.op || ''}`.toLowerCase();
            if (!haystack.includes(query)) {
                return false;
            }
        }

        if (vehicleConnector !== '' && !matchesConnectorToken(station, vehicleConnector)) {
            return false;
        }

        if (selectConnector !== '' && !matchesConnectorToken(station, selectConnector)) {
            return false;
        }

        if (selectOperator !== '' && String(station.op || '').toLowerCase() !== selectOperator) {
            return false;
        }

        if (selectStatus !== '' && String(station.st || '').toLowerCase() !== selectStatus) {
            return false;
        }

        return true;
    });

    renderFilteredStationCards();

    if (overviewMapInstance && typeof overviewMapInstance.normalizeCatalogItem === 'function') {
        const normalized = catalogState.filtered
            .map((item) => overviewMapInstance.normalizeCatalogItem(item))
            .filter((item) => item.id && item.validCoords);
        overviewMapInstance.setStations(normalized, shouldFitMap);
    }
}

function updateStationDistances(position) {
    if (!position || !Number.isFinite(position.latitude) || !Number.isFinite(position.longitude)) {
        return;
    }

    if (initStationsCatalogState() && catalogState.all.length > 0) {
        catalogState.all.forEach((station) => {
            const lat = Number(station.lat);
            const lng = Number(station.lng);
            if (Number.isFinite(lat) && Number.isFinite(lng) && station.vc !== 0) {
                const km = calculateHaversineKm(position.latitude, position.longitude, lat, lng);
                station.d = Number(km.toFixed(2));
            }
        });

        catalogState.all.sort((a, b) => Number(a.d ?? 99999) - Number(b.d ?? 99999));
        applyClientStationFilters(false, false);
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

function applyFreshCsrfToken(csrfToken) {
    if (!csrfToken) return;
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) {
        meta.setAttribute('content', csrfToken);
    }
    document.querySelectorAll('input[name="_token"]').forEach((input) => {
        input.value = csrfToken;
    });
}

async function syncWebSession(token) {
    if (!token) return null;
    try {
        const response = await fetch('/auth/session-sync', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
            body: JSON.stringify({ token }),
            credentials: 'same-origin',
        });
        if (response.ok) {
            const result = await response.json();
            if (result?.data?.csrf_token) {
                applyFreshCsrfToken(result.data.csrf_token);
            }
            if (result?.data?.user) {
                localStorage.setItem(AUTH_USER_KEY, JSON.stringify(result.data.user));
                return result.data.user;
            }
        }
    } catch {
        // Abaikan kesalahan jaringan sementara
    }
    return null;
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
        await syncWebSession(result.data.token);
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
        button.addEventListener('click', (event) => {
            const clientId = button.dataset.googleClientId;
            const authCard = button.closest('.auth-card');
            const showError = (message) => {
                const error = authCard?.querySelector('[data-auth-api-error]');
                if (error) { error.textContent = message; error.hidden = false; }
            };

            if (!clientId) {
                return;
            }

            if (!window.google?.accounts?.oauth2) {
                return;
            }

            event.preventDefault();

            const start = () => {
                if (!window.google?.accounts?.oauth2) {
                    window.location.assign('/auth/google/redirect');
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
                            await syncWebSession(result.data.token);
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

function updateHeaderAuth(user, token) {
    const actions = document.querySelector('.site-header .nav-actions');
    if (!actions) return;

    const isAdmin = Boolean(user.is_admin || user.role === 'admin');
    const adminButtonHtml = isAdmin
        ? '<a class="nav-admin-btn" href="/admin" data-admin-dashboard-btn>Dashboard Admin</a>'
        : '';

    const existingWelcome = actions.querySelector('.welcome-user');
    if (existingWelcome && !actions.querySelector('.nav-join')) {
        if (isAdmin && !actions.querySelector('[data-admin-dashboard-btn]')) {
            existingWelcome.insertAdjacentHTML('afterend', adminButtonHtml);
        }
        return;
    }

    actions.innerHTML = `
        <span class="welcome-user">Hai, ${user.name || user.email}</span>
        ${adminButtonHtml}
        <a class="nav-stations-link" href="/stasiun">Daftar Stasiun</a>
        <button class="nav-login-pill" type="button" data-client-logout>Keluar</button>
    `;

    actions.querySelector('[data-client-logout]')?.addEventListener('click', async () => {
        try {
            await fetch('/api/logout', {
                method: 'POST',
                headers: { Accept: 'application/json', Authorization: `Bearer ${token}` },
            });
            await fetch('/logout', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                credentials: 'same-origin',
            });
        } finally {
            localStorage.removeItem(AUTH_TOKEN_KEY);
            localStorage.removeItem(AUTH_USER_KEY);
            window.location.assign('/login');
        }
    });
}

function initClientAuthState() {
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            window.location.reload();
        }
    });

    document.querySelectorAll('form[action$="/logout"], .inline-logout-form, .mobile-drawer form').forEach((logoutForm) => {
        logoutForm.addEventListener('submit', () => {
            localStorage.removeItem(AUTH_TOKEN_KEY);
            localStorage.removeItem(AUTH_USER_KEY);
        });
    });

    const token = localStorage.getItem(AUTH_TOKEN_KEY);
    let user = null;
    try {
        user = JSON.parse(localStorage.getItem(AUTH_USER_KEY) || 'null');
    } catch {
        localStorage.removeItem(AUTH_USER_KEY);
    }

    const actions = document.querySelector('.site-header .nav-actions');
    const isServerGuest = Boolean(actions?.querySelector('.nav-join'));

    if (token && isServerGuest) {
        syncWebSession(token).then((freshUser) => {
            if (freshUser) {
                user = freshUser;
                updateHeaderAuth(user, token);
            }
        });
    }

    if (!token || !user || !actions || !isServerGuest) return;

    updateHeaderAuth(user, token);
}

function initAdminNavigation() {
    document.addEventListener('click', async (event) => {
        const adminBtn = event.target.closest('[data-admin-dashboard-btn], .nav-admin-btn, .drawer-admin-link');
        if (!adminBtn) return;

        const token = localStorage.getItem(AUTH_TOKEN_KEY);
        if (!token) return;

        event.preventDefault();
        const targetUrl = adminBtn.getAttribute('href') || '/admin';

        await syncWebSession(token);
        window.location.assign(targetUrl);
    });
}

function checkLoginAutoRedirect() {
    const loginCard = document.querySelector('[data-auth-form="login"]');
    if (!loginCard) return;

    const token = localStorage.getItem(AUTH_TOKEN_KEY);
    if (!token) return;

    let user = null;
    try {
        user = JSON.parse(localStorage.getItem(AUTH_USER_KEY) || 'null');
    } catch {
        return;
    }

    if (user && (user.is_admin || user.role === 'admin')) {
        syncWebSession(token).then(() => {
            window.location.assign('/admin');
        });
    }
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

    initStationsCatalogState();

    const searchInput = document.querySelector('[data-station-search-input]');
    const connectorSelect = document.querySelector('[data-filter-connector]');
    const operatorSelect = document.querySelector('[data-filter-operator]');
    const statusSelect = document.querySelector('[data-filter-status]');
    const loadMoreBtn = document.querySelector('[data-load-more-stations]');

    let searchTimer = null;
    if (searchInput && catalogState.all.length > 0) {
        searchInput.addEventListener('input', () => {
            window.clearTimeout(searchTimer);
            searchTimer = window.setTimeout(() => {
                applyClientStationFilters(true, true);
            }, 80);
        });
    }

    [connectorSelect, operatorSelect, statusSelect].forEach((selectEl) => {
        selectEl?.addEventListener('change', () => {
            applyClientStationFilters(true, true);
        });
    });

    loadMoreBtn?.addEventListener('click', () => {
        catalogState.visibleLimit += STATION_PAGE_SIZE;
        renderFilteredStationCards();
    });
}

function initVehicleSelector() {
    const modal = document.querySelector('[data-vehicle-modal]');
    const openBtns = document.querySelectorAll('[data-vehicle-modal-open]');
    const closeBtn = document.querySelector('[data-vehicle-modal-close]');
    const resetBtn = document.querySelector('[data-vehicle-reset]');
    const options = document.querySelectorAll('[data-vehicle-option]');
    const brandEls = document.querySelectorAll('[data-active-vehicle-brand]');
    const connectorEls = document.querySelectorAll('[data-active-vehicle-connector]');

    const applyVehicle = (vehicle, shouldFitMap = false) => {
        brandEls.forEach((el) => {
            el.textContent = vehicle ? vehicle.brand : 'Merek Kendaraan';
        });
        connectorEls.forEach((el) => {
            el.textContent = vehicle ? `Konektor ${vehicle.connector}` : 'Jenis Konektor';
        });

        options.forEach((opt) => {
            opt.classList.toggle('is-selected', Boolean(vehicle && opt.dataset.vehicleBrand === vehicle.brand));
        });

        applyClientStationFilters(shouldFitMap);
    };

    const saved = readActiveVehicle();
    if (saved) {
        applyVehicle(saved, false);
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
            applyVehicle(selected, true);
            modal.setAttribute('hidden', '');
        });
    });

    resetBtn?.addEventListener('click', () => {
        try {
            localStorage.removeItem(ACTIVE_VEHICLE_KEY);
        } catch {
            // Abaikan
        }
        applyVehicle(null, true);
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

    initStationDetailMap();
}

initStationsCatalogState();
initLocationWidget();
initPasswordToggles();
initApiAuthForms();
initGoogleAuth();
initClientAuthState();
initAdminNavigation();
checkLoginAutoRedirect();
overviewMapInstance = initStationsOverviewMap((newPos) => {
    updateStationDistances(newPos);
});
const storedPos = readStoredPosition();
if (storedPos) {
    updateStationDistances(storedPos);
}
initFilterAndSearch();
initVehicleSelector();
initMobileMenu();
initDetailInteractions();

const locBtn = document.querySelector('[data-location-button]');
if (locBtn) {
    locBtn.addEventListener('click', () => {
        setTimeout(() => {
            const latest = readStoredPosition();
            if (latest) {
                overviewMapInstance?.showWarning('');
                overviewMapInstance?.renderUserMarker(latest);
                updateStationDistances(latest);
            } else {
                overviewMapInstance?.renderUserMarker(DEFAULT_POSITION);
            }
        }, 1200);
    });
}

if (typeof window !== 'undefined') {
    window.CasIn = {
        ...(window.CasIn ?? {}),
        map: {
            calculateHaversineKm,
            initStationsOverviewMap,
            initStationDetailMap,
        },
    };
}
