export const CUSTOM_VEHICLES_KEY = 'casin.custom_vehicles';

const connectorNames = ['CCS2', 'AC Type 2', 'CHAdeMO', 'DC Connector', 'AC Connector'];

function invalid(message, field) {
    const error = new Error(message);
    error.field = field;
    return error;
}

function text(value) {
    return typeof value === 'string' ? value.trim().replace(/\s+/g, ' ') : '';
}

function normalizeEntry(input) {
    if (!input || !['vehicle', 'connector'].includes(input.kind)) {
        throw invalid('Pilih jenis tambahan yang valid.', 'kind');
    }

    const connector = text(input.connector);
    if (!connector || connector.length > 100) {
        throw invalid('Isi tipe konektor, maksimal 100 karakter.', 'connector');
    }

    const entry = {
        kind: input.kind,
        connector: connectorNames.find((name) => name.toLowerCase() === connector.toLowerCase()) || connector,
    };
    if (entry.kind === 'vehicle') {
        entry.brand = text(input.brand);
        entry.model = text(input.model);
        if (!entry.brand || entry.brand.length > 100) {
            throw invalid('Isi merek kendaraan, maksimal 100 karakter.', 'brand');
        }
        if (!entry.model || entry.model.length > 100) {
            throw invalid('Isi model kendaraan, maksimal 100 karakter.', 'model');
        }
    }

    return entry;
}

export function readCustomVehicles(storage) {
    const raw = storage.getItem(CUSTOM_VEHICLES_KEY);
    if (raw === null) return [];

    try {
        const entries = JSON.parse(raw);
        if (!Array.isArray(entries)) throw new Error();
        return entries.map((entry) => {
            if (typeof entry?.id !== 'string' || !entry.id.startsWith('custom-')) throw new Error();
            return { ...normalizeEntry(entry), id: entry.id };
        });
    } catch {
        throw new Error('Daftar tambahan di browser tidak dapat dibaca. Pilihan bawaan tetap tersedia.');
    }
}

export function customVehicleName(entry) {
    return entry.kind === 'connector' ? 'Semua kendaraan' : `${entry.brand} ${entry.model}`;
}

export function customVehicleLabel(entry) {
    return entry.kind === 'connector'
        ? `${entry.connector} · Konektor saja`
        : `${customVehicleName(entry)} · ${entry.connector}`;
}

export function addCustomVehicle(input, storage, baseVehicles = []) {
    const entry = normalizeEntry(input);
    const entries = readCustomVehicles(storage);
    const name = customVehicleName(entry).toLowerCase();
    const connector = entry.connector.toLowerCase();
    const duplicate = entries.some((saved) => saved.kind === entry.kind
        && customVehicleName(saved).toLowerCase() === name
        && saved.connector.toLowerCase() === connector)
        || (entry.kind === 'vehicle' && baseVehicles.some((saved) =>
            text(saved.name).toLowerCase() === name && text(saved.connector).toLowerCase() === connector));

    if (duplicate) {
        throw invalid('Pilihan ini sudah ada. Gunakan pilihan yang tersedia pada daftar.', entry.kind === 'vehicle' ? 'model' : 'connector');
    }

    // Local selection identifier only; this is not an authentication token.
    const id = `custom-${globalThis.crypto?.randomUUID?.() || `${Date.now().toString(36)}-${Math.random().toString(36).slice(2)}`}`;
    const saved = { ...entry, id };
    storage.setItem(CUSTOM_VEHICLES_KEY, JSON.stringify([...entries, saved]));
    return saved;
}
