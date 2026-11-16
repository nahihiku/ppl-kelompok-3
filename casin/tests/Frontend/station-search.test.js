import { test } from 'node:test';
import assert from 'node:assert/strict';
import { createStationSearch } from '../../resources/js/station-search.js';

const station = {
    id: 12, name: 'SPKLU Bandung', address: 'Bandung', operator_badge: 'PLN',
    current_status: 'normal', status_label: 'Tersedia', latitude: -6.9, longitude: 107.6,
    distance_km: 1.25, connector_type: 'CCS2', connector_summary: 'CCS2, AC Type 2',
    connector_search: 'ccs2 | ac type 2', power_kw: 50, current_type: 'DC',
    price_per_kwh: 2466, available_chargers: 0, total_chargers: 2,
    is_valid_coordinate: true, detail_url: '/stasiun/12', direct_nav_url: 'https://www.google.com/maps/dir/?api=1',
};
const response = (stations) => ({ ok: true, json: async () => ({ success: true, data: { stations } }) });

function harness(fetcher, options = {}) {
    const results = [];
    const errors = [];
    let loading = 0;
    const client = createStationSearch({
        endpoint: 'https://casin.test/api/stations', fetcher,
        onLoading: () => { loading += 1; },
        onSuccess: (stations) => results.push(stations), onError: (error) => errors.push(error),
        ...options,
    });
    return { client, results, errors, get loading() { return loading; } };
}

test('sends location and combined search/vehicle filters; maps API fields for cards and pins', async () => {
    let request;
    const h = harness(async (url, options) => {
        request = { url: new URL(url), options };
        return response([station]);
    });
    await h.client.search({ q: 'Bandung & kota', lat: -6.9, lng: 107.6,
        connector: 'AC Type 2', vehicle_connector: 'CCS2', operator: 'PLN', status: 'normal' });
    assert.equal(request.url.searchParams.get('q'), 'Bandung & kota');
    assert.equal(request.url.searchParams.get('lat'), '-6.9');
    assert.equal(request.url.searchParams.get('lng'), '107.6');
    assert.equal(request.url.searchParams.get('connector'), 'AC Type 2');
    assert.equal(request.url.searchParams.get('vehicle_connector'), 'CCS2');
    assert.equal(request.url.searchParams.get('operator'), 'PLN');
    assert.equal(request.url.searchParams.get('status'), 'normal');
    assert.equal(request.options.headers.Accept, 'application/json');
    const card = h.results[0][0];
    assert.equal(card.id, '12');
    assert.equal(card.n, station.name);
    assert.equal(card.cs, station.connector_search);
    assert.equal(card.av, 0);
    assert.equal(card.d, 1.25);
    assert.equal(card.url, '/stasiun/12');
    assert.equal(card.vc, 1);
    assert.equal(h.loading, 1);
});

test('an older response cannot replace a newer search, even if fetch ignores abort', async () => {
    const pending = [];
    const h = harness((url, options) => new Promise((resolve) => pending.push({ resolve, options })));
    const older = h.client.search({ q: 'lama' });
    const latest = h.client.search({ q: 'baru' });
    assert.equal(pending[0].options.signal.aborted, true);
    pending[1].resolve(response([{ ...station, name: 'Baru' }]));
    await latest;
    pending[0].resolve(response([{ ...station, name: 'Lama' }]));
    await older;
    assert.deepEqual(h.results.map((items) => items[0].n), ['Baru']);
    assert.equal(h.errors.length, 0);
});

test('clearing a search obtains all results instead of filtering the old subset', async () => {
    const urls = [];
    const h = harness(async (url) => { urls.push(new URL(url)); return response([]); });
    await h.client.search({ q: 'tidak ditemukan', operator: 'PLN' });
    await h.client.search({ q: '', operator: '' });
    assert.equal(urls[1].searchParams.has('q'), false);
    assert.equal(urls[1].searchParams.has('operator'), false);
    assert.deepEqual(h.results, [[], []]);
});

test('network, HTTP and malformed responses preserve the last success and allow retry', async () => {
    const answers = [response([station]), new Error('offline'), { ok: false },
        { ok: true, json: async () => ({ data: [] }) },
        { ok: true, json: async () => { throw new SyntaxError('HTML'); } }, response([])];
    const h = harness(async () => {
        const answer = answers.shift();
        if (answer instanceof Error) throw answer;
        return answer;
    });
    for (let i = 0; i < 6; i++) await h.client.search({});
    assert.equal(h.errors.length, 4);
    assert.equal(h.results.length, 2);
    assert.equal(h.results[0][0].n, station.name);
    assert.deepEqual(h.results[1], []);
});

test('timeout reports a retryable failure', async () => {
    const h = harness((url, { signal }) => new Promise((resolve, reject) => {
        signal.addEventListener('abort', () => reject(new Error('timeout')));
    }), { timeoutMs: 10 });
    await h.client.search({});
    assert.equal(h.errors.length, 1);
    assert.equal(h.results.length, 0);
});

test('invalid coordinates remain cards but cannot become map pins or a fake zero distance', async () => {
    const h = harness(async () => response([{ ...station, latitude: 0, longitude: 0, distance_km: null }]));
    await h.client.search({});
    assert.equal(h.results[0][0].vc, 0);
    assert.ok(Number.isNaN(h.results[0][0].d));
});
