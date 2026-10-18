<?php
/**
 * Titik masuk AJAX untuk modal Masuk pada anjungan modern (beranda.php). Empat kegunaan:
 * POST nisn+pin untuk membuka sesi, GET ?aksi=status untuk menanyakan sesi yang berjalan,
 * POST ?aksi=keluar untuk menutupnya, dan GET ?aksi=foto untuk mengambil foto siswa yang
 * sedang login (blob jbsakad.siswa.foto; 404 bila kosong).
 *
 * Form login siswa memakai NISN + PIN. Pin dibandingkan dengan kolom pinsiswa yang di skema
 * warisan memang tersimpan apa adanya (belum di-hash), jadi perbandingan PIN masih teks polos
 * dan baru aman sepenuhnya setelah migrasi password_hash selesai. Kueri memakai prepared
 * statement dan halaman ini hanya dilayani lewat HTTPS vhost. Nilai NIS hasil lookup dipakai
 * halaman untuk memuat infosiswa/infosiswa.content.php.
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

$jawaban = array('sukses' => false, 'pesan' => 'Login gagal.');

function jb_siswa_jawab($data)
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/* $db_host warisan berbentuk "host:port", jadi tidak perlu argumen port terpisah. */
function jb_siswa_koneksi()
{
    require_once __DIR__ . '/include/database.config.php';
    $mysqli = @new mysqli($db_host, $db_user, $db_pass);
    if ($mysqli->connect_errno) {
        return null;
    }
    $mysqli->set_charset('utf8');
    return $mysqli;
}

/* Sesi siswa yang sedang berjalan, atau null bila belum login. */
function jb_siswa_sesi()
{
    session_name('_JIBAS_ANJUNGAN_IS__');
    session_start();
    if (empty($_SESSION['islogin']) || empty($_SESSION['infosiswa.nis'])) {
        return null;
    }
    return $_SESSION['infosiswa.nis'];
}

function jb_siswa_punyaFoto($nis)
{
    $mysqli = jb_siswa_koneksi();
    if (!$mysqli) {
        return false;
    }
    $stmt = $mysqli->prepare("SELECT foto IS NOT NULL AND LENGTH(foto) > 0 AS berfoto FROM jbsakad.siswa WHERE nis = ? LIMIT 1");
    $stmt->bind_param('s', $nis);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    $mysqli->close();
    return $row ? (bool)$row['berfoto'] : false;
}

/* Cek sesi tanpa mengirim kredensial: dipakai halaman saat Info Siswa dibuka ulang atau
   setelah Keluar, supaya tidak perlu menampilkan form login lama di dalam kartu. */
if (isset($_GET['aksi']) && $_GET['aksi'] === 'status') {
    $nis = jb_siswa_sesi();
    if ($nis === null) {
        jb_siswa_jawab(array('sukses' => false, 'pesan' => '', 'login' => false));
    }
    jb_siswa_jawab(array('sukses' => true, 'pesan' => '', 'login' => true,
        'nis' => $nis,
        'nama' => isset($_SESSION['infosiswa.name']) ? $_SESSION['infosiswa.name'] : '',
        'berfoto' => jb_siswa_punyaFoto($nis)));
}

/* Foto siswa yang sedang login. Nis tidak diterima dari permintaan: tanpa aturan ini siapa pun
   bisa meminta foto siswa lain lewat URL. Blob-nya disimpan di jbsakad.siswa.foto (mediumblob). */
if (isset($_GET['aksi']) && $_GET['aksi'] === 'foto') {
    $nis = jb_siswa_sesi();
    if ($nis === null) {
        http_response_code(404);
        exit();
    }
    $mysqli = jb_siswa_koneksi();
    if (!$mysqli) {
        http_response_code(503);
        exit();
    }
    $stmt = $mysqli->prepare("SELECT foto FROM jbsakad.siswa WHERE nis = ? LIMIT 1");
    $stmt->bind_param('s', $nis);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    $mysqli->close();
    if (!$row || $row['foto'] === null || $row['foto'] === '') {
        http_response_code(404);
        exit();
    }
    header('Content-Type: image/jpeg');
    header('Cache-Control: no-store');
    echo $row['foto'];
    exit();
}

/* Tutup sesi siswa. Hanya lewat POST supaya tidak bisa dipicu tautan atau prefetch. */
if (isset($_GET['aksi']) && $_GET['aksi'] === 'keluar') {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        jb_siswa_jawab(array('sukses' => false, 'pesan' => 'Pengeluaran harus dikirim sebagai permintaan POST.', 'login' => false));
    }
    session_name('_JIBAS_ANJUNGAN_IS__');
    session_start();
    $_SESSION = array();
    if (ini_get('session.use_cookies')) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    session_destroy();
    jb_siswa_jawab(array('sukses' => true, 'pesan' => '', 'login' => false));
}

$nisn = isset($_POST['nisn']) ? trim((string)$_POST['nisn']) : '';
$pin = isset($_POST['pin']) ? trim((string)$_POST['pin']) : '';
if ($nisn === '' || $pin === '') {
    $jawaban['pesan'] = 'NISN dan PIN harus diisi.';
    jb_siswa_jawab($jawaban);
}

$mysqli = jb_siswa_koneksi();
if (!$mysqli) {
    $jawaban['pesan'] = 'Layanan data sekolah tidak dapat dihubungi.';
    jb_siswa_jawab($jawaban);
}

$stmt = $mysqli->prepare("SELECT nis, nama, foto IS NOT NULL AND LENGTH(foto) > 0 AS berfoto
                            FROM jbsakad.siswa WHERE nisn = ? AND pinsiswa = ? LIMIT 1");
if (!$stmt) {
    $jawaban['pesan'] = 'Permintaan login tidak dapat diproses.';
    jb_siswa_jawab($jawaban);
}
$stmt->bind_param('ss', $nisn, $pin);
$stmt->execute();
$res = $stmt->get_result();
$siswa = $res ? $res->fetch_assoc() : null;
$stmt->close();
$mysqli->close();

if (!$siswa || $siswa['nis'] === '') {
    $jawaban['pesan'] = 'NISN atau PIN salah.';
    jb_siswa_jawab($jawaban);
}

/* Sesi informasi siswa warisan: nama sesinya harus sama persis dengan
   anjungan/infosiswa/infosiswa.session.php agar infosiswa.content.php mau menampilkan data. */
session_name('_JIBAS_ANJUNGAN_IS__');
session_start();
session_regenerate_id(true);
$_SESSION['islogin'] = true;
$_SESSION['infosiswa.nis'] = $siswa['nis'];
$_SESSION['infosiswa.name'] = $siswa['nama'];
$_SESSION['infosiswa.reporttype'] = 'PROFIL';

jb_siswa_jawab(array('sukses' => true, 'pesan' => '', 'nis' => $siswa['nis'], 'nama' => $siswa['nama'],
    'berfoto' => (bool)$siswa['berfoto']));
