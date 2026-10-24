/**
 * [FE-01] Perekap koordinat perangkat berbasis HTML5 Geolocation API.
 *
 * Izin diperoleh hanya lewat tombol yang ditekan pengguna (KNF-06 — tidak ada
 * pelacakan senyap), hasil terakhir disimpan di sessionStorage agar halaman peta
 * lain bisa memakainya tanpa meminta izin ulang, dan DEFAULT_POSITION menjadi
 * titik pusat bawaan peta saat izin ditolak atau posisi gagal didapat.
 */

const POSITION_STORAGE_KEY = 'casin.user_position';

/** Presisi tinggi, tanpa memakai posisi lama, tunggu maksimal 10 detik. */
const REQUEST_OPTIONS = {
    enableHighAccuracy: true,
    timeout: 10000,
    maximumAge: 0,
};

/** Titik pusat bawaan peta: Monas, Jakarta (dipakai kalau GPS gagal/ditolak). */
export const DEFAULT_POSITION = Object.freeze({
    latitude: -6.2088,
    longitude: 106.8456,
    source: 'default',
});

/** Kode galat GeolocationPosition menurut spesifikasi HTML5. */
const ERROR_CODES = Object.freeze({
    PERMISSION_DENIED: 1,
    POSITION_UNAVAILABLE: 2,
    TIMEOUT: 3,
});

export class GeolocationError extends Error {
    constructor(status, message, cause) {
        super(message, { cause });
        this.name = 'GeolocationError';
        this.status = status;
    }
}

function isSupported() {
    return typeof navigator !== 'undefined'
        && 'geolocation' in navigator
        && window.isSecureContext === true;
}

function toPlainPosition(position) {
    return {
        latitude: Number(position.coords.latitude),
        longitude: Number(position.coords.longitude),
        accuracy: Math.round(Number(position.coords.accuracy ?? 0)),
        capturedAt: new Date().toISOString(),
        source: 'device',
    };
}

/** Validasi koordinat sesuai use case: posisi tidak valid dimatikan navigasinya. */
export function isValidPosition(position) {
    const latitude = Number(position?.latitude);
    const longitude = Number(position?.longitude);

    return Number.isFinite(latitude) && Number.isFinite(longitude)
        && Math.abs(latitude) <= 90
        && Math.abs(longitude) <= 180;
}

function describeError(error) {
    switch (error?.code) {
        case ERROR_CODES.PERMISSION_DENIED:
            return {
                status: 'permission_denied',
                message: 'Izin lokasi ditolak. Pegang kembali tombolnya, lalu pilih “Izinkan” pada peramban.',
            };
        case ERROR_CODES.POSITION_UNAVAILABLE:
            return {
                status: 'position_unavailable',
                message: 'Perangkat gagal menemukan lokasi. Cari tempat terbuka atau aktifkan layanan lokasi, lalu coba lagi.',
            };
        case ERROR_CODES.TIMEOUT:
            return {
                status: 'timeout',
                message: 'Waktu tunggu GPS habis. Periksa sinyal perangkat lalu coba lagi.',
            };
        default:
            return {
                status: 'unknown',
                message: 'Akses lokasi gagal. Coba beberapa saat lagi.',
            };
    }
}

function storePosition(position) {
    try {
        sessionStorage.setItem(POSITION_STORAGE_KEY, JSON.stringify(position));
    } catch {
        // Penyimpanan tidak tersedia (mis. mode pribadi) — koordinat tetap dipakai di memori.
    }
}

export function readStoredPosition() {
    try {
        const raw = sessionStorage.getItem(POSITION_STORAGE_KEY);
        if (! raw) {
            return null;
        }

        const position = JSON.parse(raw);
        return isValidPosition(position) ? position : null;
    } catch {
        return null;
    }
}

export function clearStoredPosition() {
    try {
        sessionStorage.removeItem(POSITION_STORAGE_KEY);
    } catch {
        // Dibiarkan sunyi: tidak ada apa pun untuk dibersihkan.
    }
}

/** Minta koordinat segar dari perangkat. Ditolak gejala galat berupa GeolocationError. */
export function requestDevicePosition() {
    return new Promise((resolve, reject) => {
        if (! isSupported()) {
            reject(new GeolocationError(
                'unsupported',
                'Peramban ini tidak mendukung Geolocation (halaman harus dibuka lewat HTTPS atau localhost).',
            ));
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const devicePosition = toPlainPosition(position);
                if (! isValidPosition(devicePosition)) {
                    reject(new GeolocationError(
                        'position_unavailable',
                        'Perangkat mengirim koordinat yang tidak valid, peta akan memakai titik pusat bawaan.',
                    ));
                    return;
                }

                storePosition(devicePosition);
                resolve(devicePosition);
            },
            (error) => {
                const { status, message } = describeError(error);
                reject(new GeolocationError(status, message, error));
            },
            REQUEST_OPTIONS,
        );
    });
}

function formatCoordinate(value) {
    const degrees = Math.abs(value).toFixed(6).replace('.', ',');
    return `${value < 0 ? '−' : ''}${degrees}°`;
}

/** Sambungkan modul ini ke panel lokasi pada halaman. Aman dipanggil di halaman mana pun. */
export function initLocationWidget() {
    const widget = document.getElementById('location-widget');
    if (! widget) {
        return null;
    }

    const statusElement = widget.querySelector('[data-location-status]');
    const buttonElement = widget.querySelector('[data-location-button]');
    if (! statusElement || ! buttonElement) {
        return null;
    }

    const setStatus = (state, message) => {
        widget.classList.toggle('is-active', state === 'active');
        widget.classList.toggle('is-error', state === 'error');
        statusElement.textContent = message;
    };

    const renderPosition = (position, timeLabel) => {
        const time = new Date(position.capturedAt).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
        });
        const accuracy = position.accuracy ? ` akurasi ± ${position.accuracy} m ·` : '';
        setStatus('active', `${formatCoordinate(position.latitude)} ${formatCoordinate(position.longitude)} ·${accuracy} ${timeLabel} pukul ${time}`);
        buttonElement.textContent = 'Perbarui lokasi';
    };

    const cachedPosition = readStoredPosition();
    if (cachedPosition) {
        renderPosition(cachedPosition, 'tersimpan');
    }

    buttonElement.addEventListener('click', () => {
        buttonElement.disabled = true;
        setStatus('loading', 'Meminta izin lokasi pada peramban…');
        buttonElement.textContent = 'Meminta izin…';

        requestDevicePosition()
            .then((position) => {
                buttonElement.disabled = false;
                renderPosition(position, 'diperbarui');
            })
            .catch((error) => {
                setStatus('error', error.message);
                buttonElement.disabled = error.status === 'unsupported';
                buttonElement.textContent = error.status === 'unsupported' ? 'Lokasi tak tersedia' : 'Coba lagi';
            });
    });

    return {
        widget,
        setStatus,
        request: requestDevicePosition,
        cachedPosition,
    };
}

/** Nyala API ke lingkup global untuk dipakai blade/fitur lain (pola seperti window.axios). */
window.CasIn = window.CasIn ?? {};
window.CasIn.location = {
    requestDevicePosition,
    readStoredPosition,
    clearStoredPosition,
    isValidPosition,
    DEFAULT_POSITION,
    GeolocationError,
};
