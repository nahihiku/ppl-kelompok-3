import { test } from 'node:test';
import assert from 'node:assert/strict';
import {
    CUSTOM_VEHICLES_KEY, addCustomVehicle, readCustomVehicles, customVehicleName, customVehicleLabel,
} from '../../resources/js/custom-vehicles.js';

function memoryStorage() {
    const values = new Map();
    return {
        getItem: (key) => values.get(key) ?? null,
        setItem: (key, value) => values.set(key, value),
    };
}

test('an additional vehicle survives a reload with its identity and connector intact', () => {
    const storage = memoryStorage();
    const saved = addCustomVehicle({ kind: 'vehicle', brand: '  Merek Uji ', model: 'Model  Baru', connector: ' chademo ' }, storage);
    assert.deepEqual(readCustomVehicles(storage), [{
        kind: 'vehicle', brand: 'Merek Uji', model: 'Model Baru', connector: 'CHAdeMO', id: saved.id,
    }]);
    assert.equal(customVehicleName(saved), 'Merek Uji Model Baru');
    assert.equal(customVehicleLabel(saved), 'Merek Uji Model Baru · CHAdeMO');
    assert.ok(saved.id.startsWith('custom-'));
});

test('a connector-only option does not require a vehicle and supports a type outside the defaults', () => {
    const storage = memoryStorage();
    const saved = addCustomVehicle({ kind: 'connector', connector: 'Konektor Uji' }, storage);
    assert.equal(readCustomVehicles(storage)[0].connector, 'Konektor Uji');
    assert.equal(customVehicleName(saved), 'Semua kendaraan');
    assert.equal(customVehicleLabel(saved), 'Konektor Uji · Konektor saja');
    assert.equal(saved.brand, undefined);
});

test('duplicate additions are rejected without changing existing data', () => {
    const storage = memoryStorage();
    addCustomVehicle({ kind: 'vehicle', brand: 'Merek', model: 'Model', connector: 'CCS2' }, storage);
    const before = storage.getItem(CUSTOM_VEHICLES_KEY);
    assert.throws(() => addCustomVehicle({ kind: 'vehicle', brand: ' merek ', model: 'model', connector: 'ccs2' }, storage), /sudah ada/);
    assert.equal(storage.getItem(CUSTOM_VEHICLES_KEY), before);
    assert.throws(() => addCustomVehicle({ kind: 'vehicle', brand: 'Hyundai', model: 'Ioniq 5', connector: 'CCS2' }, storage,
        [{ name: 'Hyundai Ioniq 5', connector: 'CCS2' }]), /sudah ada/);
});

test('the same model with another connector and a standalone connector remain distinct choices', () => {
    const storage = memoryStorage();
    const first = addCustomVehicle({ kind: 'vehicle', brand: 'Merek', model: 'Model', connector: 'CCS2' }, storage);
    const second = addCustomVehicle({ kind: 'vehicle', brand: 'Merek', model: 'Model', connector: 'CHAdeMO' }, storage);
    addCustomVehicle({ kind: 'connector', connector: 'CCS2' }, storage);
    assert.notEqual(first.id, second.id);
    assert.equal(readCustomVehicles(storage).length, 3);
});

test('missing, whitespace-only and oversized fields cannot be persisted', () => {
    const storage = memoryStorage();
    for (const invalid of [
        { kind: 'vehicle', brand: ' ', model: 'Model', connector: 'CCS2' },
        { kind: 'vehicle', brand: 'Merek', model: '', connector: 'CCS2' },
        { kind: 'connector', connector: ' ' },
        { kind: 'connector', connector: 'x'.repeat(101) },
        { kind: 'other', connector: 'CCS2' },
    ]) assert.throws(() => addCustomVehicle(invalid, storage));
    assert.equal(storage.getItem(CUSTOM_VEHICLES_KEY), null);
});

test('corrupt saved data is not silently overwritten by a new addition', () => {
    const storage = memoryStorage();
    for (const invalid of ['broken JSON', '{}', '[{"id":"all","kind":"connector","connector":"CCS2"}]']) {
        storage.setItem(CUSTOM_VEHICLES_KEY, invalid);
        assert.throws(() => readCustomVehicles(storage), /tidak dapat dibaca/);
        assert.throws(() => addCustomVehicle({ kind: 'connector', connector: 'CHAdeMO' }, storage), /tidak dapat dibaca/);
        assert.equal(storage.getItem(CUSTOM_VEHICLES_KEY), invalid);
    }
});

test('storage failure preserves the previous saved list', () => {
    const storage = memoryStorage();
    addCustomVehicle({ kind: 'connector', connector: 'CCS2' }, storage);
    const before = storage.getItem(CUSTOM_VEHICLES_KEY);
    const blocked = { getItem: storage.getItem, setItem: () => { throw new Error('Quota exceeded'); } };
    assert.throws(() => addCustomVehicle({ kind: 'connector', connector: 'CHAdeMO' }, blocked), /Quota exceeded/);
    assert.equal(storage.getItem(CUSTOM_VEHICLES_KEY), before);
});
