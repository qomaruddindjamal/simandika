<?php
/**
 * Titik masuk AJAX untuk modal Masuk pada anjungan modern (beranda.php). Tiga kegunaan:
 * POST nisn+pin untuk membuka sesi, GET ?aksi=status untuk menanyakan sesi yang berjalan,
 * dan POST ?aksi=keluar untuk menutupnya.
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

/* Cek sesi tanpa mengirim kredensial: dipakai halaman saat Info Siswa dibuka ulang atau
   setelah Keluar, supaya tidak perlu menampilkan form login lama di dalam kartu. */
if (isset($_GET['aksi']) && $_GET['aksi'] === 'status') {
    session_name('_JIBAS_ANJUNGAN_IS__');
    session_start();
    if (empty($_SESSION['islogin']) || empty($_SESSION['infosiswa.nis'])) {
        jb_siswa_jawab(array('sukses' => false, 'pesan' => '', 'login' => false));
    }
    jb_siswa_jawab(array('sukses' => true, 'pesan' => '', 'login' => true,
        'nis' => $_SESSION['infosiswa.nis'],
        'nama' => isset($_SESSION['infosiswa.name']) ? $_SESSION['infosiswa.name'] : ''));
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

require_once __DIR__ . '/include/database.config.php';

/* $db_host warisan berbentuk "host:port", jadi tidak perlu argumen port terpisah. */
$mysqli = @new mysqli($db_host, $db_user, $db_pass);
if ($mysqli->connect_errno) {
    $jawaban['pesan'] = 'Layanan data sekolah tidak dapat dihubungi.';
    jb_siswa_jawab($jawaban);
}
$mysqli->set_charset('utf8');

$stmt = $mysqli->prepare("SELECT nis, nama FROM jbsakad.siswa WHERE nisn = ? AND pinsiswa = ? LIMIT 1");
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

jb_siswa_jawab(array('sukses' => true, 'pesan' => '', 'nis' => $siswa['nis'], 'nama' => $siswa['nama']));
