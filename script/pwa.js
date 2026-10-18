/* PWA JIBAS: registrasi service worker + tombol pasang (install prompt). */
(function () {
    'use strict';

    if (!('serviceWorker' in navigator)) return;

    window.addEventListener('load', function () {
        /* Halaman anjungan memasang <base href="/anjungan/">, jadi path SW diambil dari nilai
           absolut yang diberikan halaman (window.JIBAS_SW) dan scope-nya selalu root. */
        var src = window.JIBAS_SW || 'sw.js';
        var opts = src.charAt(0) === '/' ? { scope: src.replace(/[^/]*$/, '') } : {};
        navigator.serviceWorker
            .register(src, opts)
            .then(function (reg) {
                reg.addEventListener('updatefound', function () {
                    var b = reg.installing;
                    if (!b) return;
                    b.addEventListener('statechange', function () {
                        if (b.state === 'installed' && navigator.serviceWorker.controller) {
                            var note = document.querySelector('[data-jb-pwa-update]');
                            if (note) note.hidden = false;
                        }
                    });
                });
            })
            .catch(function () { /* sw.js belum tersedia via path ini — abaikan, aplikasi tetap jalan */ });
    });

    var tombol = document.querySelector('[data-jb-install]');
    if (tombol) {
        var prompt = null;
        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            prompt = e;
            tombol.hidden = false;
        });
        tombol.addEventListener('click', function () {
            if (!prompt) return;
            prompt.prompt();
            prompt.userChoice.then(function () {
                prompt = null;
                tombol.hidden = true;
            });
        });
        window.addEventListener('appinstalled', function () { tombol.hidden = true; });
    }
})();
