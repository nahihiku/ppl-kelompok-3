import {
    DEFAULT_POSITION,
    isValidPosition,
    readStoredPosition,
    requestDevicePosition,
} from './geolocation';

function whenLeafletReady(callback) {
    if (typeof window === 'undefined') {
        return;
    }

    if (window.L) {
        callback(window.L);
        return;
    }

    let attempts = 0;
    const maxAttempts = 50;
    const timer = window.setInterval(() => {
        attempts += 1;
        if (window.L) {
            window.clearInterval(timer);
            callback(window.L);
        } else if (attempts >= maxAttempts) {
            window.clearInterval(timer);
        }
    }, 100);
}

export function calculateHaversineKm(lat1, lon1, lat2, lon2) {
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

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function getPinColorByStatus(status) {
    switch (status) {
        case 'busy':
            return '#d97706';
        case 'technical_issue':
            return '#dc2626';
        default:
            return '#0095a8';
    }
}

function createStationPinIcon(L, status, isPrimary = false) {
    const color = getPinColorByStatus(status);
    const width = isPrimary ? 42 : 34;
    const height = isPrimary ? 54 : 44;

    return L.divIcon({
        className: `casin-leaflet-pin ${isPrimary ? 'is-primary' : ''}`,
        html: `
            <svg viewBox="0 0 44 56" width="${width}" height="${height}" fill="none" aria-hidden="true">
                <path d="M22 0C9.85 0 0 9.85 0 22C0 37.4 22 56 22 56C22 56 44 37.4 44 22C44 9.85 34.15 0 22 0Z" fill="${color}"/>
                <circle cx="22" cy="21" r="11" fill="#FFFFFF"/>
                <path d="M23.5 13L17.5 21.5H22L20.5 29L26.5 20.5H22L23.5 13Z" fill="${color}"/>
            </svg>
        `,
        iconSize: [width, height],
        iconAnchor: [width / 2, height],
        popupAnchor: [0, -(height - 6)],
    });
}

function createUserLocationIcon(L, isDeviceGps = true) {
    const fill = isDeviceGps ? '#2563eb' : '#21327c';
    const ring = isDeviceGps ? 'rgba(37, 99, 235, 0.25)' : 'rgba(33, 50, 124, 0.2)';

    return L.divIcon({
        className: 'casin-user-location-pin',
        html: `
            <div style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:9999px;background:${ring};">
                <div style="width:14px;height:14px;border-radius:9999px;background:${fill};border:2.5px solid #ffffff;box-shadow:0 2px 6px rgba(15,23,42,0.35);"></div>
            </div>
        `,
        iconSize: [28, 28],
        iconAnchor: [14, 14],
        popupAnchor: [0, -14],
    });
}

function buildStationPopupHtml(data) {
    const validCoords = data.validCoords && isValidPosition({ latitude: data.lat, longitude: data.lng });
    const navActionHtml = validCoords && data.navUrl
        ? `<a href="${escapeHtml(data.navUrl)}" target="_blank" rel="noopener noreferrer" class="map-popup-btn map-popup-btn-nav">Rute Navigasi</a>`
        : `<span class="map-popup-btn map-popup-btn-disabled" aria-disabled="true">Navigasi Nonaktif</span>`;

    return `
        <div class="casin-map-popup">
            <div class="map-popup-badges">
                <span class="map-popup-operator">${escapeHtml(data.operator)}</span>
                <span class="map-popup-status is-${escapeHtml(data.status)}">${escapeHtml(data.statusLabel)}</span>
            </div>
            <h4 class="map-popup-title">${escapeHtml(data.title)}</h4>
            <p class="map-popup-address">${escapeHtml(data.address)}</p>
            <div class="map-popup-meta">
                <span><strong>${escapeHtml(data.connector)}</strong> &middot; ${escapeHtml(data.power)}</span>
                <span>${escapeHtml(data.chargers)} &middot; <strong>${escapeHtml(data.price)}</strong></span>
                ${data.distanceText ? `<span>Jarak: <strong>${escapeHtml(data.distanceText)}</strong></span>` : ''}
            </div>
            <div class="map-popup-actions">
                <a href="${escapeHtml(data.detailUrl)}" class="map-popup-btn map-popup-btn-detail">Lihat Detail</a>
                ${navActionHtml}
            </div>
        </div>
    `;
}

function normalizeCatalogItem(item) {
    const lat = Number(item.lat);
    const lng = Number(item.lng);
    const validCoords = item.vc !== 0
        && isValidPosition({ latitude: lat, longitude: lng })
        && !(lat === 0 && lng === 0);

    return {
        id: String(item.id || ''),
        title: item.n || 'SPKLU',
        address: item.a || '',
        operator: item.op || 'PLN',
        status: item.st || 'normal',
        statusLabel: item.stl || 'Tersedia',
        connector: item.c || 'Belum diketahui',
        power: `${item.p ?? 0} kW (${item.cu || '-'})`,
        price: Number(item.pr) > 0 ? `Rp ${Number(item.pr)}/kWh` : 'Tarif belum diketahui',
        chargers: `${item.av ?? 0}/${item.tot ?? 0} Konektor`,
        detailUrl: item.url || '#',
        navUrl: item.nav || '',
        validCoords,
        lat,
        lng,
        distanceText: Number.isFinite(Number(item.d)) ? `${Number(item.d).toFixed(1)} km` : '',
    };
}

export function initStationsOverviewMap(onPositionUpdated) {
    const mapSection = document.getElementById('stations-overview-map');
    const canvasWrap = document.getElementById('stations-leaflet-canvas');
    if (!mapSection || !canvasWrap) {
        return null;
    }

    const warningEl = mapSection.querySelector('[data-map-warning]');
    const subtitleEl = mapSection.querySelector('[data-map-subtitle]');
    const locateBtn = mapSection.querySelector('[data-map-locate-btn]');
    const fitBtn = mapSection.querySelector('[data-map-fit-btn]');

    const state = {
        map: null,
        L: null,
        userMarker: null,
        accuracyCircle: null,
        markerLayer: null,
        canvasRenderer: null,
        activeStations: [],
        markersById: new Map(),
        focusedStationId: null,
    };

    const showWarning = (message) => {
        if (!warningEl) return;
        if (!message) {
            warningEl.hidden = true;
            warningEl.textContent = '';
            return;
        }
        warningEl.textContent = message;
        warningEl.hidden = false;
    };

    const extractCardData = (card) => {
        const lat = Number(card.dataset.lat);
        const lng = Number(card.dataset.lng);
        const distEl = card.querySelector('[data-station-distance]');

        return {
            id: card.dataset.stationId || '',
            title: card.dataset.title || card.querySelector('.station-card-title')?.textContent?.trim() || 'SPKLU',
            address: card.dataset.address || '',
            operator: card.dataset.operator || 'PLN',
            status: card.dataset.status || 'normal',
            statusLabel: card.dataset.statusLabel || 'Tersedia',
            connector: card.dataset.connector || 'CCS2',
            power: card.dataset.power || '',
            price: card.dataset.price || '',
            chargers: card.dataset.chargers || '',
            detailUrl: card.dataset.detailUrl || '#',
            navUrl: card.dataset.navUrl || '',
            validCoords: card.dataset.validCoords !== '0' && isValidPosition({ latitude: lat, longitude: lng }) && !(lat === 0 && lng === 0),
            lat,
            lng,
            distanceText: distEl ? distEl.textContent.trim() : '',
        };
    };

    const readStationsFromCatalogOrDom = () => {
        const catalogScript = document.getElementById('casin-stations-catalog');
        if (catalogScript) {
            try {
                const parsed = JSON.parse(catalogScript.textContent || '[]');
                if (Array.isArray(parsed) && parsed.length > 0) {
                    return parsed.map(normalizeCatalogItem).filter((item) => item.id && item.validCoords);
                }
            } catch {
                // Fallback ke kartu DOM jika JSON katalog gagal dibaca
            }
        }

        return Array.from(document.querySelectorAll('[data-station-card]'))
            .filter((card) => card.style.display !== 'none')
            .map(extractCardData)
            .filter((item) => item.id && item.validCoords);
    };

    const renderUserMarker = (position) => {
        if (!state.map || !state.L) return;

        const isDevice = position?.source === 'device' && isValidPosition(position);
        const coords = isDevice ? [position.latitude, position.longitude] : [DEFAULT_POSITION.latitude, DEFAULT_POSITION.longitude];
        const label = isDevice
            ? 'Posisi GPS Perangkat Anda Saat Ini'
            : 'Titik Pusat Bawaan (Monas, Jakarta)';

        if (state.userMarker) {
            state.map.removeLayer(state.userMarker);
        }
        if (state.accuracyCircle) {
            state.map.removeLayer(state.accuracyCircle);
            state.accuracyCircle = null;
        }

        state.userMarker = state.L.marker(coords, {
            icon: createUserLocationIcon(state.L, isDevice),
            zIndexOffset: 900,
        })
            .addTo(state.map)
            .bindPopup(`<strong>${escapeHtml(label)}</strong>`);

        if (isDevice && Number.isFinite(position.accuracy) && position.accuracy > 0) {
            state.accuracyCircle = state.L.circle(coords, {
                radius: Math.min(position.accuracy, 1500),
                color: '#2563eb',
                fillColor: '#3b82f6',
                fillOpacity: 0.1,
                weight: 1,
            }).addTo(state.map);
        }

        if (subtitleEl) {
            subtitleEl.textContent = isDevice
                ? 'Menampilkan sebaran stasiun pengisian daya terdekat dari koordinat GPS perangkat Anda.'
                : 'Menampilkan peta pada titik pusat bawaan (Monas, Jakarta). Aktifkan lokasi untuk pengurutan GPS real-time.';
        }
    };

    const fitVisibleMarkers = (includeUser = true) => {
        if (!state.map || !state.L) return;

        const points = state.activeStations.map((s) => [s.lat, s.lng]);
        if (includeUser && state.userMarker && points.length <= 150) {
            const userLatLng = state.userMarker.getLatLng();
            if (userLatLng) {
                points.push([userLatLng.lat, userLatLng.lng]);
            }
        }

        if (points.length > 1) {
            state.map.fitBounds(points, { padding: [36, 36], maxZoom: 14 });
        } else if (points.length === 1) {
            state.map.setView(points[0], 14);
        } else {
            state.map.setView([DEFAULT_POSITION.latitude, DEFAULT_POSITION.longitude], 12);
        }
    };

    const renderMarkersInViewport = () => {
        if (!state.map || !state.L || !state.markerLayer) return;

        const L = state.L;
        const bounds = state.map.getBounds().pad(0.25);
        const visibleInBounds = state.activeStations.filter((s) => bounds.contains([s.lat, s.lng]));
        const useDetailedPins = visibleInBounds.length <= 75;

        state.markerLayer.clearLayers();
        state.markersById.clear();

        visibleInBounds.forEach((data) => {
            const isFocused = state.focusedStationId && state.focusedStationId === data.id;
            let marker;

            if (useDetailedPins || isFocused) {
                marker = L.marker([data.lat, data.lng], {
                    icon: createStationPinIcon(L, data.status, isFocused),
                    zIndexOffset: isFocused ? 750 : 0,
                });
            } else {
                const color = getPinColorByStatus(data.status);
                marker = L.circleMarker([data.lat, data.lng], {
                    renderer: state.canvasRenderer,
                    radius: 6.5,
                    fillColor: color,
                    color: '#ffffff',
                    weight: 1.5,
                    opacity: 1,
                    fillOpacity: 0.9,
                });
            }

            marker.bindPopup(buildStationPopupHtml(data));
            state.markerLayer.addLayer(marker);
            state.markersById.set(data.id, { marker, data });
        });
    };

    const setStations = (stations, shouldFitBounds = false) => {
        state.activeStations = stations
            .filter((item) => item && item.id && item.validCoords);

        if (!state.map || !state.L) {
            return;
        }

        if (shouldFitBounds && state.activeStations.length > 0 && state.activeStations.length <= 150) {
            fitVisibleMarkers(false);
        } else if (shouldFitBounds && state.activeStations.length > 150) {
            const center = readStoredPosition() || DEFAULT_POSITION;
            state.map.setView([center.latitude, center.longitude], 12);
        }

        renderMarkersInViewport();
    };

    const syncMarkersFromCards = (shouldFitBounds = false) => {
        const list = readStationsFromCatalogOrDom();
        setStations(list, shouldFitBounds);
    };

    const focusStation = (stationId) => {
        const targetId = String(stationId);
        const station = state.activeStations.find((item) => item.id === targetId);
        mapSection.scrollIntoView({ behavior: 'smooth', block: 'center' });

        if (!state.map) {
            return;
        }

        if (!station) {
            const existing = state.markersById.get(targetId);
            if (existing) {
                state.map.setView([existing.data.lat, existing.data.lng], 15, { animate: true });
                existing.marker.openPopup();
            }
            return;
        }

        state.focusedStationId = targetId;
        state.map.setView([station.lat, station.lng], 15, { animate: false });
        renderMarkersInViewport();
        const entry = state.markersById.get(targetId);
        if (entry) {
            entry.marker.openPopup();
        }
    };

    document.addEventListener('click', (event) => {
        const focusBtn = event.target.closest('[data-focus-station-map]');
        if (focusBtn) {
            focusStation(focusBtn.dataset.focusStationMap);
        }
    });

    fitBtn?.addEventListener('click', () => {
        fitVisibleMarkers(true);
    });

    locateBtn?.addEventListener('click', () => {
        const originalText = locateBtn.querySelector('span')?.textContent || 'Lokasi Saya';
        locateBtn.disabled = true;
        const span = locateBtn.querySelector('span');
        if (span) span.textContent = 'Mencari GPS...';

        requestDevicePosition()
            .then((position) => {
                showWarning('');
                if (typeof onPositionUpdated === 'function') {
                    onPositionUpdated(position);
                }
                renderUserMarker(position);
                if (state.map) {
                    state.map.setView([position.latitude, position.longitude], 13, { animate: true });
                }
                renderMarkersInViewport();
            })
            .catch((error) => {
                showWarning(
                    `${error.message} Peta ditampilkan pada titik pusat bawaan (Monas, Jakarta).`
                );
                renderUserMarker(DEFAULT_POSITION);
                if (state.map) {
                    state.map.setView([DEFAULT_POSITION.latitude, DEFAULT_POSITION.longitude], 12, { animate: true });
                }
            })
            .finally(() => {
                locateBtn.disabled = false;
                if (span) span.textContent = originalText;
            });
    });

    whenLeafletReady((L) => {
        state.L = L;
        const fallback = canvasWrap.querySelector('.map-fallback-canvas');
        const mapDiv = document.createElement('div');
        mapDiv.className = 'stations-leaflet-mount';
        mapDiv.style.width = '100%';
        mapDiv.style.height = '100%';
        canvasWrap.insertBefore(mapDiv, fallback);
        if (fallback) {
            fallback.style.display = 'none';
        }

        const initialPos = readStoredPosition() || DEFAULT_POSITION;
        state.canvasRenderer = L.canvas({ padding: 0.5 });
        state.map = L.map(mapDiv, {
            zoomControl: true,
            scrollWheelZoom: false,
            preferCanvas: true,
        }).setView([initialPos.latitude, initialPos.longitude], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        }).addTo(state.map);

        state.markerLayer = L.layerGroup().addTo(state.map);

        state.map.on('moveend zoomend', () => {
            renderMarkersInViewport();
        });

        renderUserMarker(initialPos);
        if (state.activeStations.length === 0) {
            state.activeStations = readStationsFromCatalogOrDom();
        }
        setStations(state.activeStations, state.activeStations.length <= 150);
    });

    return {
        syncMarkersFromCards,
        setStations,
        renderUserMarker,
        showWarning,
        focusStation,
        normalizeCatalogItem,
    };
}

export function initStationDetailMap() {
    const mapContainer = document.getElementById('station-detail-map');
    if (!mapContainer) {
        return;
    }

    whenLeafletReady((L) => {
        const lat = Number(mapContainer.dataset.lat);
        const lng = Number(mapContainer.dataset.lng);
        const stationId = String(mapContainer.dataset.stationId || '');
        const name = mapContainer.dataset.name || 'SPKLU';
        const address = mapContainer.dataset.address || '';
        const status = mapContainer.dataset.status || 'normal';
        const statusLabel = mapContainer.dataset.statusLabel || 'Tersedia';
        const isValid = mapContainer.dataset.validCoords !== '0'
            && isValidPosition({ latitude: lat, longitude: lng })
            && !(lat === 0 && lng === 0);

        const fallback = mapContainer.querySelector('.map-fallback-canvas');
        const mapDiv = document.createElement('div');
        mapDiv.style.width = '100%';
        mapDiv.style.height = '100%';
        mapContainer.insertBefore(mapDiv, fallback);
        if (fallback) {
            fallback.style.display = 'none';
        }

        const centerCoords = isValid
            ? [lat, lng]
            : [DEFAULT_POSITION.latitude, DEFAULT_POSITION.longitude];

        const map = L.map(mapDiv, { zoomControl: false }).setView(centerCoords, isValid ? 15 : 12);
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 19,
        }).addTo(map);

        let allStations = [];
        try {
            allStations = JSON.parse(mapContainer.dataset.stations || '[]');
        } catch {
            allStations = [];
        }

        allStations.forEach((item) => {
            if (String(item.id) === stationId) {
                return;
            }
            const itemLat = Number(item.latitude);
            const itemLng = Number(item.longitude);
            if (!isValidPosition({ latitude: itemLat, longitude: itemLng }) || (itemLat === 0 && itemLng === 0)) {
                return;
            }

            const secondaryIcon = createStationPinIcon(L, item.current_status || 'normal', false);
            L.marker([itemLat, itemLng], { icon: secondaryIcon })
                .addTo(map)
                .bindPopup(`
                    <div class="casin-map-popup">
                        <strong>${escapeHtml(item.name)}</strong>
                        <p class="map-popup-address">${escapeHtml(item.address || '')}</p>
                        <div class="map-popup-actions">
                            <a href="/stasiun/${encodeURIComponent(item.id)}" class="map-popup-btn map-popup-btn-detail">Lihat Stasiun</a>
                        </div>
                    </div>
                `);
        });

        if (isValid) {
            const primaryIcon = createStationPinIcon(L, status, true);
            L.marker([lat, lng], { icon: primaryIcon, zIndexOffset: 800 })
                .addTo(map)
                .bindPopup(`
                    <div class="casin-map-popup">
                        <span class="map-popup-status is-${escapeHtml(status)}">${escapeHtml(statusLabel)}</span>
                        <h4 class="map-popup-title">${escapeHtml(name)}</h4>
                        <p class="map-popup-address">${escapeHtml(address)}</p>
                    </div>
                `)
                .openPopup();
        } else {
            const navLink = document.querySelector('[data-navigate-link]');
            if (navLink) {
                navLink.removeAttribute('href');
                navLink.setAttribute('aria-disabled', 'true');
                navLink.classList.add('is-disabled');
            }
        }

        const storedUserPos = readStoredPosition();
        if (storedUserPos && isValidPosition(storedUserPos)) {
            L.marker([storedUserPos.latitude, storedUserPos.longitude], {
                icon: createUserLocationIcon(L, true),
                zIndexOffset: 900,
            })
                .addTo(map)
                .bindPopup('<strong>Posisi GPS Perangkat Anda</strong>');
        }
    });
}
