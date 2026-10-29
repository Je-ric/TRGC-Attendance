const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function worker(fetch = async () => ({ status: 200 })) {
    const handlers = {};
    const added = [];
    const deleted = [];
    const fallback = { offline: true };
    const context = vm.createContext({
        URL, Response, fetch,
        self: {
            registration: { scope: 'https://example.test/church/' },
            location: { origin: 'https://example.test' },
            addEventListener: (name, handler) => { handlers[name] = handler; }
        },
        caches: {
            open: async () => ({ add: async url => added.push(url), match: async () => fallback }),
            keys: async () => ['trgc-offline-v0', 'trgc-offline-v2', 'another-app'],
            delete: async key => deleted.push(key)
        }
    });
    vm.runInContext(fs.readFileSync('sw.js', 'utf8'), context);
    return { handlers, added, deleted, fallback };
}

test('worker caches only offline help and cleans only its old caches', async () => {
    const state = worker();
    let pending;
    state.handlers.install({ waitUntil: promise => { pending = promise; } });
    await pending;
    assert.deepEqual(state.added, ['https://example.test/church/pages/offline.html']);
    state.handlers.activate({ waitUntil: promise => { pending = promise; } });
    await pending;
    assert.deepEqual(state.deleted, ['trgc-offline-v0']);
});

test('failed navigation returns offline help, while HTTP errors remain server responses', async () => {
    const request = { method: 'GET', mode: 'navigate', url: 'https://example.test/church/pages/attendance.html?session=123' };
    const offline = worker(async () => { throw new TypeError('Network unavailable'); });
    let response;
    offline.handlers.fetch({ request, respondWith: promise => { response = promise; } });
    assert.equal(await response, offline.fallback);
    const unavailable = { status: 503 };
    const online = worker(async () => unavailable);
    online.handlers.fetch({ request, respondWith: promise => { response = promise; } });
    assert.equal(await response, unavailable);
});

test('worker never intercepts API reads, writes, external requests, or other apps', () => {
    const { handlers } = worker();
    for (const request of [
        { method: 'POST', mode: 'navigate', url: 'https://example.test/church/pages/attendance.html' },
        { method: 'GET', mode: 'cors', url: 'https://example.test/church/api/people' },
        { method: 'GET', mode: 'navigate', url: 'https://database.test/people' },
        { method: 'GET', mode: 'navigate', url: 'https://example.test/other-app/' }
    ]) {
        handlers.fetch({ request, respondWith() { assert.fail('Request should bypass the worker'); } });
    }
});

test('manifest URLs resolve to real app resources under root and subdirectory hosting', () => {
    const path = 'manifest.webmanifest';
    const manifest = JSON.parse(fs.readFileSync(path, 'utf8'));
    for (const root of ['https://example.test/', 'https://example.test/church/']) {
        const url = new URL('manifest.webmanifest', root);
        assert.equal(new URL(manifest.scope, url).href, root);
        assert.equal(new URL(manifest.start_url, url).href, root + 'index.html');
        for (const icon of manifest.icons) {
            assert.ok(fs.existsSync(new URL(icon.src, url).pathname.slice(new URL(root).pathname.length)));
        }
    }
});

test('all app entry points load shared PWA setup and manifest', () => {
    for (const page of ['index', 'attendance', 'events', 'people', 'families', 'reports']) {
        const html = fs.readFileSync(page === 'index' ? 'index.html' : `pages/${page}.html`, 'utf8');
        assert.match(html, /<script src="(?:\.\.\/)?assets\/js\/pwa.js" defer><\/script>/);
        assert.match(html, /rel="manifest" href="(?:\.\.\/)?manifest.webmanifest"/);
    }
});
