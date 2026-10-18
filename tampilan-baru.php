<?php
/**
 * Halaman pratinjau tampilan baru JIBAS (Bootstrap 5.3.8 + tema 4 fase + shell).
 * Bersifat aditif: tidak mengubah satu pun file legacy. Data yang tampil adalah CONTOH (kerja DB jbsyayasan ditunda).
 *
 * Parameter bantu:
 *   ?jbphase=terang|senja|gelap|fajar  paksa satu fase (simulasi jam)
 *   ?jbchannel=local|email             ubah kanal login untuk uji visibilitas menu Webmail
 *   ?jbmodul=referensi                 set item aktif
 *   ?jbfragment=1                      render potongan saja (untuk iframe tab)
 */

session_start();
require_once __DIR__ . '/include/shell.php';

$isFragment = isset($_GET['jbfragment']);
$channel = isset($_GET['jbchannel']) && $_GET['jbchannel'] === 'email' ? 'email' : 'local';
$simulate = isset($_GET['jbphase']) ? $_GET['jbphase'] : null;
$active = isset($_GET['jbmodul']) ? $_GET['jbmodul'] : 'beranda';

$sampleLembaga = array(
    array('RA Manba\'ul Ulum', 'RA', 34, 40),
    array('RA Miftahul Ulum', 'RA', 28, 36),
    array('TK Tunas Bangsa', 'TK', 45, 48),
    array('SD Islam Al-Hikmah', 'SD', 112, 120),
    array('SMP Darul Falah', 'SMP', 96, 108),
    array('SMA Hasan Kafrawi', 'SMA', 154, 180),
);

$sampleReferensi = array(
    array('bi-calendar3', 'Tahun Ajaran', 'tahun_ajaran', 4, 'hanya-baca'),
    array('bi-brightness-high', 'Semester', 'semester', 4, 'hanya-baca'),
    array('bi-diagram-3', 'Rombel', 'rombel', 6, 'hanya-baca'),
    array('bi-mortarboard', 'Angkatan', 'angkatan', 4, 'hanya-baca'),
    array('bi-cash-coin', 'Kategori Keuangan', 'kategori_keuangan', 4, 'hanya-baca'),
    array('bi-envelope-paper', 'Jenis Surat', 'jenis_surat', 10, 'hanya-baca'),
    array('bi-geo-alt', 'Kabupaten', 'kabupaten', 514, 'global'),
    array('bi-people', 'Suku', 'suku', 225, 'global'),
);

if ($isFragment) {
    header('Content-Type: text/html; charset=utf-8');
    ?>
<!DOCTYPE html>
<html lang="id" <?= jibas_theme_attrs($channel === 'email' ? 'auto' : 'auto', $simulate) ?>>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="style/bootstrap/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style/jibas-theme.css">
    <link rel="stylesheet" href="style/jibas-shell.css">
</head>
<body class="p-3">
    <h2 class="h6 mb-1"><i class="bi bi-plugin me-1"></i>Isi modul (contoh tab)</h2>
    <p class="small-note mb-3">Tab memuat halaman lama di dalam iframe satu-DOM — bukan frameset. Ganti
        <code>src</code> dengan <code>akademik/referensi/…</code> setelah modul bermigrasi.</p>
    <table class="table table-striped table-hover align-middle mb-0">
        <thead><tr><th>Kode</th><th>Nama</th><th class="text-end">Baris</th></tr></thead>
        <tbody>
        <?php foreach ($sampleReferensi as $row) { ?>
            <tr><td><i class="bi <?= $row[0] ?> me-1"></i><?= $row[2] ?></td><td><?= $row[1] ?></td>
                <td class="text-end"><?= number_format($row[3]) ?></td></tr>
        <?php } ?>
        </tbody>
    </table>
    <script src="script/bootstrap.bundle.min.js"></script>
    <script src="script/theme.js"></script>
</body>
</html>
    <?php
    exit;
}

jb_shell_open(array(
    'title' => 'Pratinjau Tampilan Baru — JIBAS',
    'channel' => $channel,
    'phase_simulate' => $simulate,
    'active_header' => $active === 'beranda' ? '' : $active,
    'brand_name' => 'Yayasan Islam Hasan Kafrawi',
    'brand_sub' => 'JIBAS 33.0 — pratinjau tampilan',
));
jb_shell_sidebar($active, $channel);
jb_shell_main_open();
?>

<div class="jb-page-head">
    <div class="me-auto">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb jb-breadcrumb">
                <li class="breadcrumb-item">Beranda</li>
                <li class="breadcrumb-item active" aria-current="page">KPI</li>
            </ol>
        </nav>
        <h1>Ikhtisar Yayasan</h1>
    </div>
    <div class="d-flex flex-wrap gap-2 no-print">
        <button class="btn btn-sm btn-outline-primary" type="button"><i class="bi bi-calendar3 me-1"></i>2026/2027</button>
        <button class="btn btn-sm btn-outline-primary disabled" type="button" title="Kerja DB jbsyayasan ditunda">
            <i class="bi bi-buildings me-1"></i>Semua Lembaga</button>
        <button class="btn btn-sm btn-primary" type="button"><i class="bi bi-download me-1"></i>Ekspor</button>
    </div>
</div>

<div class="alert alert-warning py-2 no-print" role="alert">
    <i class="bi bi-flask me-1"></i>Angka di halaman ini <strong>data contoh</strong>. Sumber riil (schema
    <code>jbsyayasan</code>) baru dipakai setelah fase database dimulai.
</div>

<div class="row g-3 mb-3">
    <?php
    $kpi = array(
        array('Peserta Didik Aktif', '1.397', 'bi-people', '+62 dari bulan lalu', array(35, 48, 40, 62, 55, 78, 90)),
        array('Guru & Tendik', '58', 'bi-mortarboard', 'rasio 1 : 24', array(60, 58, 62, 55, 70, 66, 72)),
        array('Rombel Terisi', '46 / 52', 'bi-diagram-3', 'kuota 88%', array(70, 72, 68, 80, 84, 86, 88)),
        array('Realisasi Tagihan', '72,4%', 'bi-cash-coin', 'Rp 412 jt tertagih', array(40, 45, 52, 58, 61, 68, 72)),
        array('Surat Belum Didisposisi', '9', 'bi-envelope-exclamation', 'SLA 2 hari', array(20, 18, 25, 16, 12, 10, 9)),
        array('Kepatuhan Data Siswa', '94%', 'bi-shield-check', 'NISN/NIK lengkap', array(80, 82, 85, 88, 90, 92, 94)),
    );
    foreach ($kpi as $card) { ?>
    <div class="col-6 col-lg-4 col-xxl-2">
        <div class="jb-kpi-card h-100">
            <span class="jb-kpi-icon"><i class="bi <?= $card[2] ?>"></i></span>
            <p class="jb-kpi-label"><?= $card[0] ?></p>
            <p class="jb-kpi-value"><?= $card[1] ?></p>
            <p class="jb-kpi-meta"><?= $card[3] ?></p>
            <div class="jb-kpi-spark" aria-hidden="true">
                <?php foreach ($card[4] as $h) { echo '<span style="height:' . $h . '%"></span>'; } ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<div class="row g-3 mb-3">
    <div class="col-12 col-xl-5">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-buildings me-1"></i>Siswa per Lembaga</span>
                <span class="badge text-bg-light">6 dari 11 lembaga</span>
            </div>
            <div class="card-body jb-bar-list">
                <?php foreach ($sampleLembaga as $l) {
                    $pct = $l[3] > 0 ? round($l[2] / $l[3] * 100) : 0; ?>
                    <div class="jb-bar-row">
                        <span class="text-truncate" title="<?= $l[0] ?>"><?= $l[0] ?></span>
                        <span class="jb-bar-track"><span class="jb-bar-fill" style="width:<?= $pct ?>%"></span></span>
                        <span class="text-end"><?= $l[2] ?>/<?= $l[3] ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="bi bi-journal-text me-1"></i>Referensi — hanya-baca</span>
                <span class="badge text-bg-light">33 entri</span>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr><th scope="col">Master Data</th><th scope="col">Tabel</th>
                            <th scope="col" class="text-end">Baris</th><th scope="col">Mode</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($sampleReferensi as $row) { ?>
                        <tr>
                            <th scope="row" class="fw-normal"><i class="bi <?= $row[0] ?> me-1"></i><?= $row[1] ?></th>
                            <td><code><?= $row[2] ?></code></td>
                            <td class="text-end"><?= number_format($row[3]) ?></td>
                            <td><span class="badge text-bg-light"><i class="bi bi-eye me-1"></i><?= $row[4] ?></span></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">Tombol tambah/ubah/hapus tidak dirender pada mode hanya-baca.</small>
                <nav aria-label="Navigasi tabel">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">Sebelum</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">Sesudah</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-palette me-1"></i>Simulator fase tema</div>
            <div class="card-body">
                <p class="small-note mb-2">Mode <strong>Auto</strong> memakai jam server (Asia/Jakarta).
                    Empat tombol di bawah memaksa satu fase untuk pemeriksaan visual.</p>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a class="btn btn-sm btn-outline-primary" href="?jbphase=terang&jbchannel=<?= $channel ?>">Terang 06:00–16:59</a>
                    <a class="btn btn-sm btn-outline-primary" href="?jbphase=senja&jbchannel=<?= $channel ?>">Senja 17:00–17:59</a>
                    <a class="btn btn-sm btn-outline-primary" href="?jbphase=gelap&jbchannel=<?= $channel ?>">Gelap 18:00–03:59</a>
                    <a class="btn btn-sm btn-outline-primary" href="?jbphase=fajar&jbchannel=<?= $channel ?>">Fajar 04:00–05:59</a>
                    <a class="btn btn-sm btn-accent" href="?jbchannel=<?= $channel ?>">Kembali ke Auto</a>
                </div>
                <div class="row g-2 align-items-center">
                    <div class="col-auto"><span class="jb-phase-chip">Fase aktif:
                        <strong class="ms-1" data-jb-phase="label">—</strong>
                        <span data-jb-phase="range"></span></span></div>
                    <div class="col-auto"><span class="text-muted small">kanal login aktif:
                            <strong><?= $channel === 'email' ? 'email / SSO' : 'lokal' ?></strong></span></div>
                </div>
                <hr>
                <p class="mb-1 small-note">Aturan Webmail (engine RoundCube):</p>
                <div class="btn-group btn-group-sm" role="group">
                    <a class="btn <?= $channel === 'local' ? 'btn-primary' : 'btn-outline-primary' ?>"
                       href="?jbchannel=local&jbphase=<?= $simulate ?>">Login lokal — Webmail disembunyikan</a>
                    <a class="btn <?= $channel === 'email' ? 'btn-primary' : 'btn-outline-primary' ?>"
                       href="?jbchannel=email&jbphase=<?= $simulate ?>">Login email/SSO — Webmail tampil</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-ui-checks me-1"></i>Kecocokan komponen Bootstrap 5.3.8</div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button class="btn btn-primary btn-sm">Utama</button>
                    <button class="btn btn-accent btn-sm">Aksen</button>
                    <button class="btn btn-outline-primary btn-sm">Garis</button>
                    <button class="btn btn-secondary btn-sm">Sekunder</button>
                    <button class="btn btn-danger btn-sm">Bahaya</button>
                    <button class="btn btn-success btn-sm">Sukses</button>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6"><label class="form-label small">Cari</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input class="form-control" placeholder="NISN / nama"></div></div>
                    <div class="col-6"><label class="form-label small">Lembaga</label>
                        <select class="form-select form-select-sm"><option>Semua lembaga</option><option>SMA Hasan Kafrawi</option></select></div>
                </div>
                <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="sw1" checked>
                        <label class="form-check-label small" for="sw1">Baris bergantian</label></div>
                    <div class="form-check"><input class="form-check-input" type="radio" name="r" id="r1" checked>
                        <label class="form-check-label small" for="r1">Ringkas</label></div>
                    <span class="badge text-bg-light">readonly</span>
                    <span class="badge bg-primary">baru</span>
                </div>
                <div class="progress mb-2" style="height:.5rem" role="progressbar" aria-label="Realisasi"
                     aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width:72%"></div></div>
                <div class="alert alert-info py-2 mb-0 small"><i class="bi bi-info-circle me-1"></i>
                    Tautan, tabel, tombol, form, dan kartu mengikuti token fase aktif.</div>
            </div>
        </div>
    </div>
</div>

<div class="card no-print">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-window-stack me-1"></i>Tab modul (menggantikan frameset)</span>
        <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-primary" type="button" onclick="jibasShell.tabs.open('referensi','Referensi','tampilan-baru.php?jbfragment=1&jbphase=<?= $simulate ?>&jbchannel=<?= $channel ?>')">
                <i class="bi bi-journal-text me-1"></i>Muat Referensi</button>
            <button class="btn btn-outline-primary" type="button" onclick="jibasShell.tabs.open('keuangan','Keuangan','tampilan-baru.php?jbfragment=1&jbphase=<?= $simulate ?>&jbchannel=<?= $channel ?>')">
                <i class="bi bi-cash-coin me-1"></i>Muat Keuangan</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="jb-tabs d-none" id="jbTabStrip"></div>
        <div id="jbTabHost" class="p-3 small-note">Tab akan muncul di sini — satu DOM, tanpa <code>&lt;frameset&gt;</code>.</div>
    </div>
</div>

<?php jb_shell_close(); ?>
