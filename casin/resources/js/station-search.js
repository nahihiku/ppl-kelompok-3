// Adapt the public API response to the compact format shared by cards and maps.
export function normalizeStation(station) {
    if (!station || station.id == null || typeof station.name !== 'string') {
        throw new Error('Data stasiun tidak valid.');
    }

    const lat = Number(station.latitude);
    const lng = Number(station.longitude);
    const valid = station.latitude != null && station.longitude != null
        && Number.isFinite(lat) && Number.isFinite(lng)
        && Math.abs(lat) <= 90 && Math.abs(lng) <= 180 && !(lat === 0 && lng === 0);

    return {
        id: String(station.id), n: station.name, a: station.address ?? '',
        op: station.operator_badge ?? '', st: station.current_status ?? 'normal',
        stl: station.status_label ?? '', lat, lng,
        d: station.distance_km == null ? NaN : Number(station.distance_km), c: station.connector_type ?? '',
        csum: station.connector_summary ?? station.connector_type ?? '',
        cs: station.connector_search ?? String(station.connector_type ?? '').toLowerCase(),
        p: Number(station.power_kw ?? 0), cu: station.current_type ?? '-',
        pr: Number(station.price_per_kwh ?? 0), av: Number(station.available_chargers ?? 0),
        tot: Number(station.total_chargers ?? 0), url: station.detail_url ?? '',
        nav: station.direct_nav_url ?? '', vc: valid && station.is_valid_coordinate !== false ? 1 : 0,
    };
}

// The sequence guard also covers responses that finish after cancellation.
export function createStationSearch({ endpoint, onLoading, onSuccess, onError, fetcher = fetch, timeoutMs = 15000 }) {
    let sequence = 0;
    let controller = null;

    const cancel = () => {
        sequence += 1;
        controller?.abort();
    };

    const search = async (params) => {
        cancel();
        const requestId = sequence;
        controller = new AbortController();
        const signal = controller.signal;
        const timeout = setTimeout(() => controller?.signal === signal && controller.abort(), timeoutMs);
        onLoading();

        try {
            const url = new URL(endpoint);
            Object.entries(params).forEach(([key, value]) => {
                if (value !== '' && value != null) url.searchParams.set(key, String(value));
            });
            const response = await fetcher(url.toString(), {
                signal, headers: { Accept: 'application/json' }, credentials: 'same-origin',
            });
            if (!response.ok) throw new Error('Gagal memuat stasiun.');
            const payload = await response.json();
            if (payload.success !== true || !Array.isArray(payload.data?.stations)) {
                throw new Error('Respons pencarian tidak valid.');
            }
            const stations = payload.data.stations.map(normalizeStation);
            if (requestId === sequence && !signal.aborted) onSuccess(stations, payload.data.center);
        } catch (error) {
            if (requestId === sequence) onError(error);
        } finally {
            clearTimeout(timeout);
        }
    };

    return { search, cancel };
}
