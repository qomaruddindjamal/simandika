/* Service worker JIBAS: cache aset statis, selalu network untuk PHP (data sesi), fallback luring. */
'use strict';

const CACHE_ASET = 'jibas-aset-v3';
const CACHE_HALAMAN = 'jibas-halaman-v1';

const ASET_INTI = [
    './offline.html',
    './manifest.webmanifest',
    './style/bootstrap/bootstrap.min.css',
    './style/bootstrap/bootstrap-icons.min.css',
    './style/bootstrap/fonts/bootstrap-icons.woff2',
    './style/bootstrap/fonts/bootstrap-icons.woff',
    './style/jibas-theme.css',
    './style/jibas-shell.css',
    './style/jibas-anjungan.css',
    './script/bootstrap.bundle.min.js',
    './script/theme.js',
    './script/shell.js',
    './script/anjungan.js',
    './script/pwa.js',
    './images/pwa-192.png',
    './images/pwa-512.png',
    './images/pwa-maskable-512.png',
    './images/apple-touch-icon.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_ASET)
            .then((cache) => Promise.all(ASET_INTI.map((rel) => cache.add(new URL(rel, self.location).href))))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((k) => k !== CACHE_ASET && k !== CACHE_HALAMAN).map((k) => caches.delete(k))
            ))
            .then(() => self.clients.claim())
    );
});

function isStatis(url) {
    if (url.pathname.indexOf('/style/') !== -1) return true;
    if (url.pathname.indexOf('/script/') !== -1) return true;
    if (url.pathname.indexOf('/images/') !== -1) return true;
    return /\.(css|js|woff2?|ttf|png|jpe?g|gif|svg|ico|webp)$/i.test(url.pathname);
}

self.addEventListener('fetch', (event) => {
    const request = event.request;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) return;

    // Halaman berbasis PHP berisi data sesi: tidak pernah disimpan, hanya dicoba ulang saat online.
    if (/\.php(?:$|\/)/i.test(url.pathname) || url.pathname === '/') {
        if (request.mode === 'navigate') {
            event.respondWith(
                fetch(request).catch(() => caches.match(new URL('./offline.html', self.location).href))
            );
        }
        return;
    }

    if (!isStatis(url)) return;

    // Cache-first dengan penyegaran diam-diam (stale-while-revalidate).
    event.respondWith(
        caches.match(request).then((cached) => {
            const refresh = fetch(request)
                .then((response) => {
                    if (response && response.ok && response.type === 'basic') {
                        const copy = response.clone();
                        caches.open(CACHE_ASET).then((cache) => cache.put(request, copy));
                    }
                    return response;
                })
                .catch(() => cached);
            return cached || refresh;
        })
    );
});

self.addEventListener('message', (event) => {
    if (event.data === 'SKIP_TUNGGU') self.skipWaiting();
});
