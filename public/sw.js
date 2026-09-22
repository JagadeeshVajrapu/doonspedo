const CACHE_NAME = 'taxiapp-static-v3';
const STATIC_ASSETS = [
    '/css/frontend.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css'
];

self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS))
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames =>
            Promise.all(
                cacheNames
                    .filter(cacheName => cacheName !== CACHE_NAME)
                    .map(cacheName => caches.delete(cacheName))
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);

    // Never intercept non-GET requests (login/logout/forms/api mutations).
    if (request.method !== 'GET') {
        return;
    }

    // Do not cache/intercept dynamic app routes to avoid auth/session stale responses.
    if (url.origin === self.location.origin) {
        const blockedPrefixes = ['/app', '/login', '/logout', '/register', '/admin', '/driver', '/rider', '/profile'];
        if (blockedPrefixes.some(prefix => url.pathname === prefix || url.pathname.startsWith(prefix + '/'))) {
            return;
        }
    }

    event.respondWith(
        caches.match(request).then(cached => {
            if (cached) {
                return cached;
            }

            return fetch(request).then(networkResponse => {
                if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                    return networkResponse;
                }

                if (url.origin === self.location.origin && url.pathname.startsWith('/css/')) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, responseClone));
                }

                return networkResponse;
            });
        })
    );
});
