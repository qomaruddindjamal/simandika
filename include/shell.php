<?php
/**
 * Shell tampilan baru JIBAS: navbar header (13 menu lama) + sidebar + area konten.
 * Dipakai halaman pratinjau dan modul yang sudah bermigrasi. Belum menyentuh file legacy mana pun.
 */

require_once __DIR__ . '/../script/theme.php';

if (!function_exists('jb_e')) {
    function jb_e($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('jb_login_channel')) {
    /**
     * Kanal login: 'local' (user lokal JIBAS) atau 'email' (login lewat akun email/SSO Google).
     * Engine RoundCube/Halaman webmail HANYA ditampilkan untuk kanal 'email'.
     */
    function jb_login_channel()
    {
        $channel = isset($_SESSION['jibas_login_channel']) ? $_SESSION['jibas_login_channel'] : '';
        return $channel === 'email' ? 'email' : 'local';
    }
}

if (!function_exists('jb_header_menu')) {
    /** Padanan 13 ikon header lama (akademik/frametop.php:136-195) versi Bootstrap Icons. */
    function jb_header_menu()
    {
        return array(
            array('key' => 'referensi',  'label' => 'Referensi',      'icon' => 'bi-journal-text'),
            array('key' => 'calonsiswa', 'label' => 'Calon Siswa',    'icon' => 'bi-person-plus'),
            array('key' => 'guru',       'label' => 'Guru & Pelajaran', 'icon' => 'bi-mortarboard'),
            array('key' => 'jadwal',     'label' => 'Jadwal',         'icon' => 'bi-calendar3'),
            array('key' => 'siswa',      'label' => 'Kesiswaan',      'icon' => 'bi-people'),
            array('key' => 'presensi',   'label' => 'Presensi',       'icon' => 'bi-clipboard-check'),
            array('key' => 'penilaian',  'label' => 'Penilaian',      'icon' => 'bi-list-check'),
            array('key' => 'exim',       'label' => 'Ekspor/Impor',   'icon' => 'bi-file-earmark-arrow-up'),
            array('key' => 'kelulusan',  'label' => 'Kelulusan',      'icon' => 'bi-award'),
            array('key' => 'mutasi',     'label' => 'Mutasi',         'icon' => 'bi-arrow-left-right'),
            array('key' => 'pelaporan',  'label' => 'Pelaporan',      'icon' => 'bi-bar-chart-line'),
            array('key' => 'pengaturan', 'label' => 'Pengaturan',     'icon' => 'bi-sliders'),
        );
    }
}

if (!function_exists('jb_sidebar_menu')) {
    /**
     * Grup sidebar. $channel='email' menampilkan grup Pintasan (webmail/tembusan);
     * $channel='local' menyembunyikannya — engine RoundCube tidak ditampilkan untuk user lokal.
     */
    function jb_sidebar_menu($channel = null)
    {
        if (is_null($channel)) $channel = jb_login_channel();
        $groups = array(
            array(
                'title' => 'Utama',
                'items' => array(
                    array('key' => 'beranda', 'label' => 'Beranda & KPI', 'icon' => 'bi-speedometer2'),
                    array('key' => 'laporan-eksekutif', 'label' => 'Laporan Eksekutif', 'icon' => 'bi-pie-chart'),
                ),
            ),
            array(
                'title' => 'Referensi',
                'badge' => 'readonly',
                'items' => array(
                    array('key' => 'ref-yayasan', 'label' => 'Profil Yayasan', 'icon' => 'bi-bank',
                          'sub' => array('yayasan', 'lembaga', 'departemen', 'kepala_sekolah', 'instansi')),
                    array('key' => 'ref-akademik', 'label' => 'Akademik', 'icon' => 'bi-book',
                          'sub' => array('tahun_ajaran', 'semester', 'tingkat', 'kelas', 'rombel', 'jurusan',
                                         'angkatan', 'kenaikan_kelas', 'alumni', 'sekolah_asal')),
                    array('key' => 'ref-wilayah', 'label' => 'Wilayah', 'icon' => 'bi-geo-alt',
                          'sub' => array('provinsi', 'kabupaten', 'kecamatan', 'kelurahan', 'kepulauan', 'suku')),
                    array('key' => 'ref-keuangan', 'label' => 'Keuangan & Pembukuan', 'icon' => 'bi-cash-coin',
                          'sub' => array('kategori_keuangan', 'keuangan', 'spp', 'penerimaan',
                                         'jenis_penerimaan', 'tahun_buku')),
                    array('key' => 'ref-surat', 'label' => 'Persuratan', 'icon' => 'bi-envelope-paper',
                          'sub' => array('kategori_surat', 'jenis_surat', 'tujuan_surat', 'no_surat')),
                    array('key' => 'ref-sdm', 'label' => 'Pegawai', 'icon' => 'bi-person-badge',
                          'sub' => array('data_guru', 'guru_lembaga')),
                ),
            ),
            array(
                'title' => 'Transaksional',
                'items' => array(
                    array('key' => 'pembayaran', 'label' => 'Pembayaran', 'icon' => 'bi-receipt'),
                    array('key' => 'transaksi', 'label' => 'Transaksi', 'icon' => 'bi-cash-stack'),
                    array('key' => 'surat-masuk', 'label' => 'Surat Masuk', 'icon' => 'bi-inboxes'),
                    array('key' => 'surat-keluar', 'label' => 'Surat Keluar', 'icon' => 'bi-send'),
                ),
            ),
            array(
                'title' => 'Pintasan',
                'only_channel' => 'email',
                'items' => array(
                    array('key' => 'webmail', 'label' => 'Webmail', 'icon' => 'bi-envelope'),
                    array('key' => 'tembusan', 'label' => 'Tembusan & Arsip', 'icon' => 'bi-archive'),
                ),
            ),
        );

        $visible = array();
        foreach ($groups as $group) {
            if (isset($group['only_channel']) && $group['only_channel'] !== $channel) continue;
            $visible[] = $group;
        }
        return $visible;
    }
}

if (!function_exists('jb_shell_open')) {
    /**
     * Cetak <head> sampai pembuka area konten.
     * $opts: title, active_header, active_sidebar, phase_simulate, brand_name, brand_sub, logo
     */
    function jb_shell_open($opts = array())
    {
        $title = isset($opts['title']) ? $opts['title'] : 'JIBAS';
        $channel = isset($opts['channel']) ? ($opts['channel'] === 'email' ? 'email' : 'local') : jb_login_channel();
        $mode = isset($opts['theme_mode']) ? jibas_theme_mode($opts['theme_mode']) : 'auto';
        $simulate = isset($opts['phase_simulate']) ? $opts['phase_simulate'] : null;
        $locked = in_array($simulate, array('terang', 'senja', 'gelap', 'fajar'), true);
        $phase = $locked ? $simulate : jibas_resolve_phase($mode);
        $info = jibas_phase_info($phase);
        $brand = isset($opts['brand_name']) ? $opts['brand_name'] : 'Yayasan Pendidikan';
        $brand_sub = isset($opts['brand_sub']) ? $opts['brand_sub'] : 'JIBAS ' . (isset($opts['version']) ? $opts['version'] : '33.0');
        $logo = isset($opts['logo']) ? $opts['logo'] : 'images/jibas2015.ico';
        $active_header = isset($opts['active_header']) ? $opts['active_header'] : '';
        $collapsed = !empty($opts['sidebar_collapsed']) ? ' is-collapsed' : '';

        $lock = $locked ? ' data-jb-lock-phase="1"' : '';
        echo '<!DOCTYPE html>' . "\n";
        echo '<html lang="id" ' . jibas_theme_attrs($mode, $simulate) . $lock . '>' . "\n";
        echo '<head>' . "\n";
        echo '<meta charset="utf-8">' . "\n";
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
        echo '<title>' . jb_e($title) . '</title>' . "\n";
        echo '<meta name="theme-color" content="' . jb_e($info['theme_color']) . '">' . "\n";
        echo '<meta name="description" content="Sistem informasi yayasan dan sekolah JIBAS">' . "\n";
        echo '<link rel="manifest" href="manifest.webmanifest">' . "\n";
        echo '<link rel="apple-touch-icon" href="images/apple-touch-icon.png">' . "\n";
        echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="mobile-web-app-capable" content="yes">' . "\n";
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . "\n";
        echo '<meta name="apple-mobile-web-app-title" content="' . jb_e($brand) . '">' . "\n";
        echo '<script>/* koreksi fase sebelum paint bila user memilih mode manual */'
            . '(function(){try{var m=localStorage.getItem("jibasThemeMode");'
            . 'if(m!=="terang"&&m!=="gelap")return;'
            . 'if(document.documentElement.getAttribute("data-jb-lock-phase")==="1")return;'
            . 'var b={terang:"light",senja:"dark",gelap:"dark",fajar:"light"};'
            . 'var d=document.documentElement;d.setAttribute("data-jibas-phase",m);'
            . 'd.setAttribute("data-bs-theme",b[m]);}catch(e){}})();</script>' . "\n";
        echo '<link rel="stylesheet" href="style/bootstrap/bootstrap.min.css">' . "\n";
        echo '<link rel="stylesheet" href="style/bootstrap/bootstrap-icons.min.css">' . "\n";
        echo '<link rel="stylesheet" href="style/jibas-theme.css">' . "\n";
        echo '<link rel="stylesheet" href="style/jibas-shell.css">' . "\n";
        echo '</head>' . "\n";
        echo '<body class="jb-shell' . $collapsed . '">' . "\n";
        echo '<div class="jb-frame">' . "\n";

        echo '  <div class="jb-brand">' . "\n";
        echo '    <button class="btn btn-sm btn-link text-decoration-none p-0 d-none d-lg-inline-flex" type="button"'
            . ' data-jb-collapse aria-label="Lipat sidebar"><i class="bi bi-layout-sidebar-inset"></i></button>' . "\n";
        echo '    <button class="btn btn-sm btn-link text-decoration-none p-0 d-lg-none" type="button"'
            . ' data-bs-toggle="offcanvas" data-bs-target="#jbSidebar" aria-label="Buka menu"><i class="bi bi-list"></i></button>' . "\n";
        echo '    <img src="' . jb_e($logo) . '" alt="Logo">' . "\n";
        echo '    <span class="jb-brand-text">' . jb_e($brand) . '<span class="jb-brand-sub">' . jb_e($brand_sub) . '</span></span>' . "\n";
        echo '  </div>' . "\n";

        echo '  <nav class="navbar-jb d-flex align-items-center">' . "\n";
        echo '    <ul class="jb-header-menu">' . "\n";
        foreach (jb_header_menu() as $item) {
            $active = $item['key'] === $active_header ? ' active' : '';
            echo '      <li><a class="nav-link' . $active . '" href="?jbmodul=' . jb_e($item['key']) . '"'
                . ' title="' . jb_e($item['label']) . '"><i class="bi ' . jb_e($item['icon']) . '"></i>'
                . '<span>' . jb_e($item['label']) . '</span></a></li>' . "\n";
        }
        echo '    </ul>' . "\n";

        echo '    <div class="d-flex align-items-center gap-2 ms-auto">' . "\n";
        if ($channel === 'email') {
            echo '      <a class="btn btn-sm btn-link text-decoration-none" href="?jbmodul=webmail"'
                . ' title="Webmail (engine RoundCube)" aria-label="Webmail"><i class="bi bi-envelope"></i></a>' . "\n";
        }
        echo '      <span class="jb-phase-chip"><i class="bi ' . jb_e($info['icon']) . '" data-jb-phase="icon"></i>'
            . '<span data-jb-phase="label">' . jb_e($info['label']) . '</span>'
            . '<small class="opacity-75" data-jb-phase="range">' . jb_e($info['range']) . '</small></span>' . "\n";
        echo '      <div class="btn-group btn-group-sm no-print" role="group" aria-label="Pilih tema">' . "\n";
        echo '        <button type="button" class="btn btn-outline-secondary" data-jb-theme-mode="terang" aria-pressed="false">Terang</button>' . "\n";
        echo '        <button type="button" class="btn btn-outline-secondary" data-jb-theme-mode="gelap" aria-pressed="false">Gelap</button>' . "\n";
        echo '        <button type="button" class="btn btn-outline-secondary" data-jb-theme-mode="auto" aria-pressed="true">Auto</button>' . "\n";
        echo '      </div>' . "\n";
        echo '      <div class="dropdown">' . "\n";
        echo '        <button class="btn btn-sm btn-link text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">'
            . '<i class="bi bi-person-circle"></i><span class="d-none d-md-inline ms-1">Administrator</span></button>' . "\n";
        echo '        <ul class="dropdown-menu dropdown-menu-end">' . "\n";
        echo '          <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>' . "\n";
        echo '          <li><a class="dropdown-item" href="#"><i class="bi bi-key me-2"></i>Ganti Sandi</a></li>' . "\n";
        echo '          <li><a class="dropdown-item" href="#" data-jb-install hidden><i class="bi bi-phone me-2"></i>Pasang aplikasi</a></li>' . "\n";
        echo '          <li><hr class="dropdown-divider"></li>' . "\n";
        echo '          <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>' . "\n";
        echo '        </ul>' . "\n";
        echo '      </div>' . "\n";
        echo '    </div>' . "\n";
        echo '  </nav>' . "\n";
    }
}

if (!function_exists('jb_shell_sidebar')) {
    function jb_shell_sidebar($active = '', $channel = null)
    {
        if (is_null($channel)) $channel = jb_login_channel();
        $groups = jb_sidebar_menu($channel);
        echo '  <aside class="sidebar-jb offcanvas-lg offcanvas-start" tabindex="-1" id="jbSidebar" aria-label="Menu navigasi">' . "\n";
        echo '    <div class="offcanvas-header">' . "\n";
        echo '      <h5 class="offcanvas-title">Menu</h5>' . "\n";
        echo '      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#jbSidebar" aria-label="Tutup"></button>' . "\n";
        echo '    </div>' . "\n";
        echo '    <div class="offcanvas-body jb-sidebar-scroll">' . "\n";
        foreach ($groups as $group) {
            echo '      <div class="jb-nav-group-title">' . jb_e($group['title']);
            if (isset($group['badge'])) {
                echo ' <span class="badge text-bg-light ms-1 fw-normal">' . jb_e($group['badge']) . '</span>';
            }
            echo '</div>' . "\n";
            foreach ($group['items'] as $item) {
                $isActive = $item['key'] === $active ? ' active' : '';
                echo '      <a class="jb-nav-link' . $isActive . '" href="?jbmodul=' . jb_e($item['key']) . '"'
                    . ' data-label="' . jb_e($item['label']) . '"><i class="bi ' . jb_e($item['icon']) . '"></i>'
                    . '<span>' . jb_e($item['label']) . '</span></a>' . "\n";
                if (isset($item['sub'])) {
                    echo '      <ul class="jb-subnav">' . "\n";
                    foreach ($item['sub'] as $sub) {
                        echo '        <li><a class="jb-nav-link is-readonly" href="?jbrefer=' . jb_e($sub) . '"'
                            . ' data-label="' . jb_e($sub) . '"><span class="text-capitalize">' . jb_e(str_replace('_', ' ', $sub)) . '</span>'
                            . ' <i class="bi bi-eye ms-auto" title="Hanya-baca"></i></a></li>' . "\n";
                    }
                    echo '      </ul>' . "\n";
                }
            }
        }
        echo '      <div class="jb-login-channel mt-auto pt-3 small-note">'
            . '<i class="bi ' . ($channel === 'email' ? 'bi-envelope-check' : 'bi-person-check') . ' me-1"></i>'
            . 'Login ' . ($channel === 'email' ? 'email / SSO' : 'lokal')
            . ($channel === 'email' ? ' — webmail aktif' : ' — webmail disembunyikan') . '</div>' . "\n";
        echo '    </div>' . "\n";
        echo '  </aside>' . "\n";
    }
}

if (!function_exists('jb_shell_main_open')) {
    function jb_shell_main_open()
    {
        echo '  <main class="main-jb" id="jbMain">' . "\n";
    }
}

if (!function_exists('jb_shell_close')) {
    function jb_shell_close($version = '33.0')
    {
        echo '  </main>' . "\n";
        echo '</div>' . "\n";
        echo '<footer class="visually-hidden">JIBAS ' . jb_e($version) . '</footer>' . "\n";
        echo '<script src="script/bootstrap.bundle.min.js" defer></script>' . "\n";
        echo '<script src="script/theme.js" defer></script>' . "\n";
        echo '<script src="script/shell.js" defer></script>' . "\n";
        echo '<script src="script/pwa.js" defer></script>' . "\n";
        echo '</body>' . "\n";
        echo '</html>' . "\n";
    }
}
