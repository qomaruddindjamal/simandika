<?php
/**
 * Halaman utama vhost data.yaishk.sch.id — anjungan informasi dengan tampilan modern.
 *
 * URL yang dibersihkan (.htaccess) hanya berisi root dan nama seksi: /, /beranda/, /mading/,
 * /infosekolah/, /infosiswa/, /jadkal/, /pustaka/, /pegawai/, /psb/. Isi folder anjungan/
 * di-render lewat include/anjungan-front.php; file warisan di dalamnya tidak diubah, dan
 * nama foldernya tidak pernah muncul di address bar.
 */

require_once __DIR__ . '/include/anjungan-front.php';   // hanya fungsi bantu, $anj belum diisi

$anjunganDir = realpath(__DIR__ . '/anjungan');
if ($anjunganDir === false || !is_file($anjunganDir . '/index.php')) {
    http_response_code(503);
    exit('Anjungan tidak tersedia.');
}

$path = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '/';
$path = trim((string)rawurldecode($path), '/');
$seksi = strpos($path, '..') !== false ? null : jb_anj_key_from_path($path);
if ($seksi === null) {
    http_response_code(404);
    exit('Halaman tidak ditemukan.');
}

// Folder kerja = folder warisan, supaya include relatif di dalam anjungan tetap resolve
// seperti sebelumnya (include/config.php, ../style/style.css, dst).
chdir($anjunganDir);
$savedScript = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : null;
$savedPhpSelf = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : null;
$_SERVER['SCRIPT_NAME'] = '/anjungan/index.php';
$_SERVER['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];

require_once __DIR__ . '/include/mainconfig.php';

$anj = array('seksi' => $seksi,
    'judul1' => isset($G_JUDUL_DEPAN_1) ? $G_JUDUL_DEPAN_1 : '',
    'judul2' => isset($G_JUDUL_DEPAN_2) ? $G_JUDUL_DEPAN_2 : '',
    'logo' => isset($G_LOGO_DEPAN_KIRI) ? $G_LOGO_DEPAN_KIRI : '',
    'versi' => isset($G_VERSION) ? $G_VERSION : '',
    'build_date' => isset($G_BUILDDATE) ? $G_BUILDDATE : '');

header('Content-Type: text/html; charset=utf-8');
require __DIR__ . '/include/anjungan-front.php';

if ($savedScript !== null) $_SERVER['SCRIPT_NAME'] = $savedScript;
if ($savedPhpSelf !== null) $_SERVER['PHP_SELF'] = $savedPhpSelf;
