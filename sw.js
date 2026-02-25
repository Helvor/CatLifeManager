const CACHE = 'catlife-v1';

// Resources to pre-cache on install (app shell)
const PRECACHE = [
    '/',
    '/style.css',
    '/manifest.json',
    '/icons/icon-192.png',
];

self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE).then(cache => cache.addAll(PRECACHE))
    );
});

self.addEventListener('activate', event => {
    // Remove old caches from previous versions
    event.waitUntil(
        caches.keys()
            .then(keys => Promise.all(
                keys.filter(k => k !== CACHE).map(k => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Only handle same-origin GET requests
    if (url.origin !== location.origin || request.method !== 'GET') return;

    // Static assets (CSS, JS, images, fonts): cache-first
    if (/\.(css|js|png|jpg|jpeg|gif|svg|webp|woff2?)$/.test(url.pathname)) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;
                return fetch(request).then(response => {
                    const clone = response.clone();
                    caches.open(CACHE).then(c => c.put(request, clone));
                    return response;
                });
            })
        );
        return;
    }

    // HTML pages: network-first, fall back to cache for offline
    event.respondWith(
        fetch(request)
            .then(response => {
                // Only cache successful responses
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE).then(c => c.put(request, clone));
                }
                return response;
            })
            .catch(() => caches.match(request))
    );
});
