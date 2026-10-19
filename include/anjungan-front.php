<?php
/**
 * Tampilan modern Anjungan Informasi (vhost data.yaishk.sch.id).
 *
 * Dijadikan halaman di URL root oleh beranda.php. Nama folder anjungan/ tidak pernah muncul
 * di address bar: <base href="/anjungan/"> hanya dipakai agar aset dan titik masuk AJAX
 * warisan tetap resolve (muncul di network tab, bukan di address bar).
 *
 * Pane konten sengaja memakai id lama (content-pane-a..h) supaya script tiap seksi anjungan
 * tetap bekerja apa adanya. Beberapa file laporan warisan hanya ditambal agar tidak fatal di
 * PHP 8; salinan semula tersimpan di C:\YIM\JIBAS\legacy-backup\2026-10-19-infosiswa\.
 */

require_once __DIR__ . '/../script/theme.php';

/* Kesalahan PHP tidak ditampilkan ke pengunjung anjungan; log PHP tetap mencatatnya. */
ini_set('display_errors', '0');

if (!function_exists('jb_anj_e')) {
    function jb_anj_e($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('jb_anj_sections')) {
    /**
     * Peta 8 seksi anjungan.
     *   pane  : id lama penampung konten (content-pane-<pane>) — dipakai script warisan
     *   src   : URL relatif terhadap <base> yang mengembalikan potongan HTML seksi
     *   menu  : include warisan yang tetap dipakai (punya pilihan departemen dari database)
     *   tools : tombol modern pengganti menu bergambar untuk seksi yang menunya murni statis
     *   init  : fungsi warisan yang dipanggil setelah potongan + script-nya selesai dimuat
     */
    function jb_anj_sections()
    {
        $sections = array(
            'beranda' => array(
                'label' => 'Beranda', 'icon' => 'bi-house-door', 'pane' => 'a',
                'src' => 'beranda/beranda.php', 'judul' => 'Beranda Informasi',
            ),
            'infosekolah' => array(
                'label' => 'Info Sekolah', 'label_pendek' => 'Sekolah', 'icon' => 'bi-buildings', 'pane' => 'b',
                'src' => 'infosekolah/infosekolah.php', 'judul' => 'Informasi Sekolah',
                'menu' => 'infosekolah/infosekolah.menu.php', 'init' => 'ifse_InitInfoSekolah', 'iframe' => true,
                'tools' => array(
                    array('label' => 'Notes', 'icon' => 'bi-card-text', 'onclick' => 'ifse_MenuNotesClicked()'),
                    array('label' => 'Galeri', 'icon' => 'bi-images', 'onclick' => 'ifse_MenuGaleriClicked()'),
                    array('label' => 'Video', 'icon' => 'bi-play-btn', 'onclick' => 'ifse_MenuVideoClicked()'),
                ),
            ),
            'mading' => array(
                'label' => 'Mading', 'icon' => 'bi-megaphone', 'pane' => 'j',
                'src' => 'mading/mading.php', 'judul' => 'Mading Siswa',
                'menu' => 'mading/mading.menu.php', 'init' => 'mad_InitMading', 'iframe' => true,
                'tools' => array(
                    array('label' => 'Notes', 'icon' => 'bi-card-text', 'onclick' => 'mad_MenuNotesClicked()'),
                    array('label' => 'Galeri', 'icon' => 'bi-images', 'onclick' => 'mad_MenuGaleriClicked()'),
                    array('label' => 'Video', 'icon' => 'bi-play-btn', 'onclick' => 'mad_MenuVideoClicked()'),
                ),
            ),
            'infosiswa' => array(
                'label' => 'Info Siswa', 'label_pendek' => 'Siswa', 'icon' => 'bi-person-video3', 'pane' => 'c',
                'src' => 'infosiswa/infosiswa.php', 'judul' => 'Informasi Siswa', 'wadah' => 'is_main', 'nav_hidden' => true,
                'dasbor' => 'siswa',
            ),
            'jadkal' => array(
                'label' => 'Jadwal & Kalender', 'label_pendek' => 'Jadwal', 'icon' => 'bi-calendar3', 'pane' => 'i',
                'src' => 'jadkal/jadkal.php', 'judul' => 'Jadwal & Kalender Akademik',
                'tools' => array(
                    array('label' => 'Kalender Akademik', 'icon' => 'bi-calendar-event', 'onclick' => 'jadkal_ShowKalender()'),
                    array('label' => 'Jadwal Kelas', 'icon' => 'bi-grid-3x3-gap', 'onclick' => 'jadkal_ShowJadwalKelas()'),
                    array('label' => 'Jadwal Guru', 'icon' => 'bi-person-video2', 'onclick' => 'jadkal_ShowJadwalGuru()'),
                ),
            ),
            'pustaka' => array(
                'label' => 'Perpustakaan', 'label_pendek' => 'Perpust.', 'icon' => 'bi-book', 'pane' => 'k',
                'src' => 'pustaka/pustaka.php', 'judul' => 'Perpustakaan',
                'tools' => array(
                    array('label' => 'Daftar Pustaka', 'icon' => 'bi-list-ul', 'onclick' => 'pustaka_browse()'),
                    array('label' => 'Cari Pustaka', 'icon' => 'bi-search', 'onclick' => 'pustaka_search()'),
                    array('label' => 'Statistik', 'icon' => 'bi-bar-chart', 'onclick' => 'pustaka_stat()'),
                ),
            ),
            'pegawai' => array(
                'label' => 'Kepegawaian', 'label_pendek' => 'Pegawai', 'icon' => 'bi-diagram-3', 'pane' => 'g',
                'src' => 'pegawai/struktur.php', 'judul' => 'Struktur Kepegawaian',
                'init' => 'jb_anj_pegawai',
            ),
            'psb' => array(
                'label' => 'PSB', 'icon' => 'bi-flag', 'pane' => 'h',
                'src' => 'psb/psb.php', 'judul' => 'Penerimaan Siswa Baru',
                'tools' => array(
                    array('label' => 'Pendataan Calon Siswa', 'icon' => 'bi-person-plus', 'onclick' => 'psb_InputPsbClicked()'),
                    array('label' => 'Daftar Calon Siswa', 'icon' => 'bi-clipboard-list', 'onclick' => 'psb_DaftarPsbClicked()'),
                    array('label' => 'Status Penerimaan', 'icon' => 'bi-patch-check', 'onclick' => 'psb_StatusPsbClicked()'),
                ),
            ),
        );

        /* Pustaka tidak perlu: script-nya ikut datang bersama potongan halamannya. */
        $libs = array(
            'beranda' => array('beranda/beranda.js'),
            'infosekolah' => array('infosekolah/infosekolah.js'),
            'mading' => array('mading/mading.js'),
            'infosiswa' => array('infosiswa/infosiswa.js', 'infosiswa/infosiswa.cbe.js',
                'script/SpryAssets/SpryTabbedPanels.js'),
            'jadkal' => array('jadkal/jadkal.js'),
            'pegawai' => array('pegawai/struktur.js'),
            'psb' => array('psb/psb.js'),
        );
        /* Laporan Nilai & Rapor warisan merangkai tab dengan Spry: CSS-nya ikut dimuat per seksi. */
        $styles = array(
            'infosiswa' => array('script/SpryAssets/SpryTabbedPanels.css'),
        );
        foreach ($libs as $key => $urls) {
            $sections[$key]['lib'] = $urls;
        }
        foreach ($styles as $key => $urls) {
            $sections[$key]['css'] = $urls;
        }
        return $sections;
    }
}

if (!function_exists('jb_anj_key_from_path')) {
    /** /, /index.php, /beranda.php -> beranda; /mading, /mading/ -> mading; selain itu null. */
    function jb_anj_key_from_path($path)
    {
        $path = trim((string)$path, '/');
        $path = preg_replace('#/(index|beranda)\.php$#', '', $path);
        if ($path === '' || preg_match('/^(index|beranda)\.php$/', $path) || $path === 'index') return 'beranda';
        $sections = jb_anj_sections();
        return isset($sections[$path]) ? $path : null;
    }
}

/**
 * Bagian berikut ini merender halaman. Berkas ini boleh di-require lebih awal hanya untuk
 * fungsi bantu di atas — selama $anj belum diisi, tidak ada keluaran.
 *
 * $anj: seksi (key), judul1, judul2, logo, versi, build_date.
 * Diisi beranda.php setelah chdir ke folder anjungan dan mainconfig dimuat, supaya include
 * warisan (mading.menu.php, infosekolah.menu.php) tetap berada di scope global seperti dulu.
 */
if (!isset($anj)) return;

$sections = jb_anj_sections();
$active = isset($anj['seksi']) && isset($sections[$anj['seksi']]) ? $anj['seksi'] : 'beranda';
$info = jibas_phase_info(jibas_phase());
$judul1 = isset($anj['judul1']) ? trim($anj['judul1']) : 'ANJUNGAN INFORMASI';
$judul2 = isset($anj['judul2']) ? trim($anj['judul2']) : '';
$logo = isset($anj['logo']) && $anj['logo'] !== '' ? $anj['logo'] : 'jibas2015.ico';
$versi = isset($anj['versi']) ? $anj['versi'] : '';
$build = isset($anj['build_date']) ? $anj['build_date'] : '';
$hari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
$bulan = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
$jam = (int)jibas_now_hour();
$sapa = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 18 ? 'Selamat sore' : 'Selamat malam'));
$tanggal = $hari[(int)date('w')] . ', ' . (int)date('j') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y');
?>
<!DOCTYPE html>
<html lang="id" <?= jibas_theme_attrs('auto') ?>>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<base href="/anjungan/">
<title>JIBAS &mdash; Anjungan Informasi <?= jb_anj_e($judul2 !== '' ? $judul2 : $judul1) ?></title>
<meta name="description" content="Anjungan informasi <?= jb_anj_e($judul1) ?>: berita sekolah, mading, informasi siswa, jadwal dan kalender akademik, perpustakaan, kepegawaian, dan PSB.">
<meta name="theme-color" content="<?= jb_anj_e($info['theme_color']) ?>">
<link rel="manifest" href="/manifest.webmanifest">
<link rel="icon" href="/images/<?= jb_anj_e($logo) ?>">
<link rel="apple-touch-icon" href="/images/apple-touch-icon.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Anjungan <?= jb_anj_e($judul1) ?>">
<script>window.JIBAS_SW = '/sw.js';</script>
<script>/* koreksi fase sebelum paint bila user memilih mode manual */
    (function () {
        try {
            var m = localStorage.getItem('jibasThemeMode');
            if (m !== 'terang' && m !== 'gelap') return;
            var b = { terang: 'light', senja: 'dark', gelap: 'dark', fajar: 'light' };
            var d = document.documentElement;
            d.setAttribute('data-jibas-phase', m);
            d.setAttribute('data-bs-theme', b[m]);
        } catch (e) { /* tanpa localStorage: pakai fase dari server */ }
    })();</script>
<link rel="stylesheet" href="/style/bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="/style/bootstrap/bootstrap-icons.min.css">
<link rel="stylesheet" href="/style/jibas-theme.css">
<link rel="stylesheet" href="/style/jibas-anjungan.css">
<script src="script/jquery-latest.js"></script>
<script src="script/jquery-ui-1.10.3.custom.min.js"></script>
<script src="script/tools.js"></script>
<script src="script/string.js"></script>
<script src="script/ajax.js"></script>
<script src="script/rupiah2.js"></script>
<script src="script/validator.js"></script>
<script src="script/lytebox.js"></script>
<script src="script/mktree.js"></script>
<script src="beranda/beranda.listbg.js"></script>
<link rel="stylesheet" href="script/mktree.css">
<link rel="stylesheet" href="script/lytebox.css">
<script src="/script/bootstrap.bundle.min.js" defer></script>
<script src="/script/theme.js" defer></script>
<script src="/script/pwa.js" defer></script>
<script src="/script/anjungan.js" defer></script>
</head>
<body class="anj-body" data-anj-active="<?= jb_anj_e($active) ?>">

<header class="anj-topbar" id="anjTopbar">
    <div class="anj-topbar-row anj-row-id">
        <div class="anj-shell">
            <a class="anj-brand" href="/">
                <img src="/images/<?= jb_anj_e($logo) ?>" alt="Logo">
                <span class="anj-brand-text">
                    <strong><?= jb_anj_e($judul1) ?></strong>
                    <?php if ($judul2 !== '') { ?><small><?= jb_anj_e($judul2) ?></small><?php } ?>
                </span>
            </a>
            <div class="anj-row-tools">
                <span class="anj-clock d-none d-md-inline">
                    <i class="bi bi-calendar3 me-1"></i><?= jb_anj_e($tanggal) ?>
                </span>
                <div class="dropdown anj-theme-menu">
                    <button class="btn btn-sm anj-theme-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false" aria-haspopup="true" aria-label="Pilih tema"
                            data-jb-phase="title" title="Tema <?= jb_anj_e($info['label']) ?> &middot; <?= jb_anj_e($info['range']) ?>">
                        <i class="bi <?= jb_anj_e($info['icon']) ?>" data-jb-phase="icon"></i>
                        <i class="bi bi-chevron-down anj-caret"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end anj-theme-list">
                        <li><h6 class="dropdown-header">Tema <span data-jb-phase="label"><?= jb_anj_e($info['label']) ?></span>
                            &middot; <span data-jb-phase="range"><?= jb_anj_e($info['range']) ?></span></h6></li>
                        <li><button type="button" class="dropdown-item anj-mode" data-jb-theme-mode="auto" aria-pressed="true">
                            <i class="bi bi-magic anj-mode-icon"></i><span>Auto (ikuti jam)</span><i class="bi bi-check2 anj-mode-cek"></i></button></li>
                        <li><button type="button" class="dropdown-item anj-mode" data-jb-theme-mode="terang" aria-pressed="false">
                            <i class="bi bi-sun anj-mode-icon"></i><span>Terang</span><i class="bi bi-check2 anj-mode-cek"></i></button></li>
                        <li><button type="button" class="dropdown-item anj-mode" data-jb-theme-mode="gelap" aria-pressed="false">
                            <i class="bi bi-moon-stars anj-mode-icon"></i><span>Gelap</span><i class="bi bi-check2 anj-mode-cek"></i></button></li>
                    </ul>
                </div>
                <!-- Pintu dasbor Info Siswa: paling kanan, berlabel nama siswa + foto/inisial
                     setelah login (script/anjungan.js yang mengisi avatar dan labelnya). -->
                <button type="button" class="btn btn-sm anj-masuk" data-bs-toggle="modal" data-bs-target="#anjMasukModal">
                    <i class="bi bi-box-arrow-in-right me-1"></i><span class="anj-masuk-nama">Masuk</span>
                </button>
            </div>
        </div>
    </div>
    <nav class="anj-topbar-row anj-row-nav" aria-label="Menu anjungan">
        <div class="anj-shell">
            <ul class="anj-nav" id="anjNav">
                <?php foreach ($sections as $key => $sek) {
                    if (!empty($sek['nav_hidden'])) continue;
                    $url = $key === 'beranda' ? '/' : '/' . $key . '/';
                    $pendek = !empty($sek['label_pendek']) ? $sek['label_pendek'] : $sek['label']; ?>
                <li class="anj-nav-item"><a class="anj-nav-link<?= $key === $active ? ' active' : '' ?>" href="<?= jb_anj_e($url) ?>"
                       title="<?= jb_anj_e($sek['label']) ?>"
                       data-seksi="<?= jb_anj_e($key) ?>"><i class="bi <?= jb_anj_e($sek['icon']) ?>"></i><span class="anj-nav-label"><?= jb_anj_e($sek['label']) ?></span><span class="anj-nav-label-sm"><?= jb_anj_e($pendek) ?></span></a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>
</header>

<header class="anj-hero" id="anjHero">
    <div class="anj-hero-layer" id="anjBg" aria-hidden="true"></div>
    <div class="anj-hero-inner">
        <p class="anj-hero-eyebrow"><?= jb_anj_e($sapa) ?> &middot; <?= jb_anj_e($tanggal) ?></p>
        <h1 class="anj-hero-title" id="anjHeroTitle"><?= jb_anj_e($sections[$active]['judul']) ?></h1>
        <p class="anj-hero-sub" id="anjHeroSub"><?= jb_anj_e($judul2 !== '' ? $judul2 : $judul1) ?></p>
    </div>
</header>

<main class="anj-main">
    <!-- Rel sidebar laporan siswa: di luar kartu, ditempel script/anjungan.js dan
         ditahan posisinya saat halaman digulir. -->
    <aside class="anj-rail" aria-label="Laporan siswa"></aside>

    <?php foreach ($sections as $key => $sek) { ?>
    <section class="anj-card<?= $key === $active ? '' : ' d-none' ?>" id="anj-kartu-<?= jb_anj_e($key) ?>"
             data-seksi="<?= jb_anj_e($key) ?>" data-src="<?= jb_anj_e($sek['src']) ?>"
             data-lib="<?= jb_anj_e(isset($sek['lib']) ? json_encode($sek['lib']) : '[]') ?>"
             data-css="<?= jb_anj_e(isset($sek['css']) ? json_encode($sek['css']) : '[]') ?>"
             <?php if (isset($sek['init'])) { ?>data-init="<?= jb_anj_e($sek['init']) ?>"<?php } ?>
             <?php if (!empty($sek['iframe'])) { ?>data-iframe="1"<?php } ?>
             <?php if (!empty($sek['wadah'])) { ?>data-wadah="<?= jb_anj_e($sek['wadah']) ?>"<?php } ?>
             <?php if (!empty($sek['dasbor'])) { ?>data-dasbor="<?= jb_anj_e($sek['dasbor']) ?>"<?php } ?>>
        <div class="anj-card-head">
            <h2 class="anj-card-title"><i class="bi <?= jb_anj_e($sek['icon']) ?> me-2"></i><?= jb_anj_e($sek['judul']) ?></h2>
            <?php if (isset($sek['tools'])) { ?>
            <div class="anj-tools" role="group" aria-label="Menu <?= jb_anj_e($sek['judul']) ?>">
                <?php $no = 0; foreach ($sek['tools'] as $tool) { $no++; ?>
                <button type="button" class="btn btn-sm anj-tool<?= $no === 1 ? ' active' : '' ?>" onclick="<?= jb_anj_e($tool['onclick']) ?>">
                    <i class="bi <?= jb_anj_e($tool['icon']) ?> me-1"></i><?= jb_anj_e($tool['label']) ?>
                </button>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if (isset($sek['menu'])) { ?>
            <div class="anj-legacy-menu">
                <?php include $sek['menu']; ?>
            </div>
            <?php } ?>
        </div>
        <div class="anj-card-body<?= !empty($sek['iframe']) ? ' anj-body-frame' : '' ?>">
            <div id="content-pane-<?= jb_anj_e($sek['pane']) ?>" class="anj-pane">&nbsp;</div>
        </div>
    </section>
    <?php } ?>
</main>

<div class="modal fade" id="anjMasukModal" tabindex="-1" role="dialog" aria-labelledby="anjMasukJudul" aria-hidden="true"
     data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content anj-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="anjMasukJudul">
                    <i class="bi bi-person-lock me-2"></i>Masuk Informasi Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="anjMasukForm" autocomplete="off" novalidate>
                <div class="modal-body">
                    <p class="anj-modal-note">
                        Melalui halaman Informasi Siswa anda dapat melihat Data Pribadi, Akademik, Presensi dan Keuangan siswa.
                        Isikan NISN dan PIN siswa. Tanyakan PIN kepada staf sekolah apabila anda tidak mengetahuinya.
                    </p>
                    <div class="mb-3">
                        <label class="form-label" for="anjIsNisn">N I S N</label>
                        <input type="text" class="form-control" id="anjIsNisn" name="nisn" maxlength="16"
                               inputmode="numeric" autocomplete="off" data-lpignore="true" data-form-type="other" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="anjIsPin">P I N</label>
                        <input type="password" class="form-control" id="anjIsPin" name="pin" maxlength="25"
                               autocomplete="new-password" data-lpignore="true" data-form-type="other" required>
                    </div>
                    <div class="anj-modal-error" id="anjMasukError" role="alert" hidden></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm anj-masuk-kirim" id="anjMasukKirim">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="anj-foot">
    <div class="anj-foot-inner">
        <span><i class="bi bi-info-circle me-1"></i>JIBAS <?= jb_anj_e($versi) ?><?= $build !== '' ? ' &middot; ' . jb_anj_e($build) : '' ?></span>
        <span class="anj-foot-links">
            <a href="/">Anjungan</a>
            <button type="button" data-bs-toggle="modal" data-bs-target="#anjMasukModal">Masuk</button>
        </span>
    </div>
</footer>

<script>/* id init yang dipanggil script/anjungan.js untuk seksi kepegawaian (pohon mktree) */
    function jb_anj_pegawai() {
        if (typeof convertTrees === 'function') { convertTrees(); setTimeout(function () { try { expandTree('tree1'); } catch (e) { /* pohon belum ada */ } }, 200); }
    }</script>
</body>
</html>
