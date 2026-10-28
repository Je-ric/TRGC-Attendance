// Increment this version when changing the offline page.
const CACHE_NAME = 'trgc-offline-v1';
const OFFLINE_URL = new URL('offline.html', self.registration.scope).href;

self.addEventListener('install', event => {
    event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.add(OFFLINE_URL)));
});

self.addEventListener('activate', event => {
    event.waitUntil(caches.keys().then(keys => Promise.all(
        keys.filter(key => key.startsWith('trgc-offline-') && key !== CACHE_NAME)
            .map(key => caches.delete(key))
    )));
});

// Only intercept page navigation. Database requests, writes, and personal data
// never enter the service worker cache. Updates activate after open tabs close.
self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET' || event.request.mode !== 'navigate') return;
    const url = new URL(event.request.url);
    if (url.origin !== self.location.origin || !url.href.startsWith(self.registration.scope)) return;

    event.respondWith(fetch(event.request).catch(async () => {
        const cache = await caches.open(CACHE_NAME);
        return await cache.match(OFFLINE_URL) || Response.error();
    }));
});
