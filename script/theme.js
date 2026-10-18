/* JIBAS — tema 4 fase di klien: mode manual (terang/gelap) + auto per jam, tanpa reload. */
(function () {
    'use strict';

    var KEY = 'jibasThemeMode';
    var BASE = { terang: 'light', senja: 'dark', gelap: 'dark', fajar: 'light' };
    var LABEL = { terang: 'Terang', senja: 'Senja', gelap: 'Gelap', fajar: 'Fajar' };
    var RANGE = { terang: '06:00-16:59', senja: '17:00-17:59', gelap: '18:00-03:59', fajar: '04:00-05:59' };
    var ICON = { terang: 'bi-sun', senja: 'bi-sunset', gelap: 'bi-moon-stars', fajar: 'bi-sunrise' };
    var THEME_COLOR = { terang: '#0f6fb8', senja: '#ff8a5b', gelap: '#0e1319', fajar: '#7c5cad' };
    var BOUNDARIES = [4, 6, 17, 18];

    var root = document.documentElement;

    function phaseFromHour(h) {
        if (h >= 6 && h <= 16) return 'terang';
        if (h === 17) return 'senja';
        if (h >= 18 || h <= 3) return 'gelap';
        return 'fajar';
    }

    function readMode() {
        try {
            var m = localStorage.getItem(KEY);
            return (m === 'terang' || m === 'gelap') ? m : 'auto';
        } catch (e) {
            return 'auto';
        }
    }

    function writeMode(m) {
        try {
            if (m === 'auto') localStorage.removeItem(KEY);
            else localStorage.setItem(KEY, m);
        } catch (e) { /* mode privat: tema tetap berlaku untuk sesi ini */ }
    }

    function resolve(mode) {
        if (mode === 'terang' || mode === 'gelap') return mode;
        return phaseFromHour(new Date().getHours());
    }

    function paint(phase) {
        root.setAttribute('data-jibas-phase', phase);
        root.setAttribute('data-bs-theme', BASE[phase]);
        sync(phase);
    }

    function sync(phase) {
        var chips = document.querySelectorAll('[data-jb-phase]');
        for (var i = 0; i < chips.length; i++) {
            var chip = chips[i];
            var field = chip.getAttribute('data-jb-phase');
            if (field === 'label') chip.textContent = LABEL[phase];
            else if (field === 'range') chip.textContent = RANGE[phase];
            else if (field === 'icon') chip.className = 'bi ' + ICON[phase];
        }
        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta && THEME_COLOR[phase]) meta.setAttribute('content', THEME_COLOR[phase]);
        var buttons = document.querySelectorAll('[data-jb-theme-mode]');
        for (var b = 0; b < buttons.length; b++) {
            var on = buttons[b].getAttribute('data-jb-theme-mode') === currentMode;
            buttons[b].classList.toggle('active', on);
            buttons[b].setAttribute('aria-pressed', on ? 'true' : 'false');
        }
    }

    function msUntilNextBoundary() {
        var now = new Date();
        var h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
        var nowMin = h * 60 + m;
        var target = null;
        for (var i = 0; i < BOUNDARIES.length; i++) {
            if (BOUNDARIES[i] * 60 > nowMin) { target = BOUNDARIES[i] * 60; break; }
        }
        if (target === null) target = 24 * 60;
        return (target - nowMin) * 60000 - s * 1000;
    }

    var currentMode = 'auto';

    function apply(mode) {
        currentMode = mode;
        if (root.getAttribute('data-jb-lock-phase') !== '1') paint(resolve(mode));
        else sync(root.getAttribute('data-jibas-phase') || resolve(mode));
    }

    function setMode(mode) {
        writeMode(mode);
        apply(mode);
    }

    function boot() {
        apply(readMode());
        var timer = setTimeout(function tick() {
            if (currentMode === 'auto' && root.getAttribute('data-jb-lock-phase') !== '1') paint(resolve('auto'));
            else sync(root.getAttribute('data-jibas-phase'));
            timer = setTimeout(tick, msUntilNextBoundary());
        }, msUntilNextBoundary());

        document.addEventListener('click', function (ev) {
            var btn = ev.target.closest ? ev.target.closest('[data-jb-theme-mode]') : null;
            if (!btn) return;
            ev.preventDefault();
            setMode(btn.getAttribute('data-jb-theme-mode'));
        });
    }

    window.jibasTheme = {
        apply: apply,
        setMode: setMode,
        mode: function () { return currentMode; },
        phase: function () { return root.getAttribute('data-jibas-phase'); },
        phaseFromHour: phaseFromHour
    };

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
    else boot();
})();
