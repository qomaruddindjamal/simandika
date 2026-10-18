/* JIBAS — interaksi shell: lipat sidebar + tab konten modul. */
(function () {
    'use strict';

    var KEY = 'jibasSidebarCollapsed';
    var shell = document.body;

    function setCollapsed(on) {
        shell.classList.toggle('is-collapsed', on);
        try {
            if (on) localStorage.setItem(KEY, '1');
            else localStorage.removeItem(KEY);
        } catch (e) { /* storage privat */ }
    }

    document.addEventListener('click', function (ev) {
        var btn = ev.target.closest ? ev.target.closest('[data-jb-collapse]') : null;
        if (!btn) return;
        ev.preventDefault();
        setCollapsed(!shell.classList.contains('is-collapsed'));
    });

    try {
        if (window.innerWidth >= 992 && localStorage.getItem(KEY) === '1') setCollapsed(true);
    } catch (e) { /* tanpa preferensi */ }

    var tabs = {
        host: null,
        strip: null,
        mount: function () {
            tabs.host = document.getElementById('jbTabHost');
            tabs.strip = document.getElementById('jbTabStrip');
            return tabs.host !== null;
        },
        open: function (key, label, url) {
            if (!tabs.mount()) return false;
            var existing = document.getElementById('jb-tab-' + key);
            if (existing) return tabs.select(key);
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'jb-tab';
            btn.id = 'jbtabbtn-' + key;
            btn.setAttribute('data-jb-tab', key);
            btn.innerHTML = '<i class="bi bi-file-earmark"></i><span></span>' +
                '<span class="btn-close" role="button" aria-label="Tutup tab"></span>';
            btn.querySelector('span').textContent = label;
            tabs.strip.appendChild(btn);
            tabs.strip.classList.remove('d-none');
            document.querySelector('.jb-frame').classList.add('has-tabs');

            var panel = document.createElement('div');
            panel.id = 'jb-tab-' + key;
            panel.className = 'd-none h-100';
            panel.innerHTML = '<iframe class="jb-content-frame" title="' + label + '" src="' + url + '"></iframe>';
            tabs.host.appendChild(panel);
            return tabs.select(key);
        },
        select: function (key) {
            var panel = document.getElementById('jb-tab-' + key);
            if (!panel) return false;
            var panels = tabs.host.querySelectorAll('[id^="jb-tab-"]');
            for (var i = 0; i < panels.length; i++) panels[i].classList.add('d-none');
            panel.classList.remove('d-none');
            var buttons = tabs.strip.querySelectorAll('[data-jb-tab]');
            for (var b = 0; b < buttons.length; b++) {
                buttons[b].classList.toggle('active', buttons[b].getAttribute('data-jb-tab') === key);
            }
            return true;
        },
        close: function (key) {
            var panel = document.getElementById('jb-tab-' + key);
            var btn = document.getElementById('jbtabbtn-' + key);
            if (!panel || !btn) return;
            panel.remove();
            btn.remove();
            if (!tabs.strip.children.length) {
                tabs.strip.classList.add('d-none');
                document.querySelector('.jb-frame').classList.remove('has-tabs');
            }
            tabs.selectFirstAvailable();
        },
        selectFirstAvailable: function () {
            var first = tabs.strip.querySelector('[data-jb-tab]');
            if (first) tabs.select(first.getAttribute('data-jb-tab'));
        }
    };

    document.addEventListener('click', function (ev) {
        var closeBtn = ev.target.closest ? ev.target.closest('.jb-tab .btn-close') : null;
        if (closeBtn) {
            ev.preventDefault();
            tabs.close(closeBtn.closest('[data-jb-tab]').getAttribute('data-jb-tab'));
            return;
        }
        var tabBtn = ev.target.closest ? ev.target.closest('[data-jb-tab]') : null;
        if (tabBtn) {
            ev.preventDefault();
            tabs.select(tabBtn.getAttribute('data-jb-tab'));
        }
    });

    window.jibasShell = { tabs: tabs, setCollapsed: setCollapsed };
})();
