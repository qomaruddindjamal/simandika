<?
/**[N]**
 * JIBAS Education Community
 * Jaringan Informasi Bersama Antar Sekolah
 *
 * Titik data JSON untuk kartu berita di beranda anjungan.
 *   ?lembaga=&kategori=&mulai=&jumlah=  -> daftar kartu
 *   ?detail=<replid>                    -> satu berita lengkap untuk jendela baca
 *
 * Parameter masuk hanya berupa angka dan nama kategori yang sudah di-whitelist:
 * lembaga dipakai sebagai jbsakad.departemen.replid (= id_lembaga), jadi nama lembaga
 * yang mengandung petik satu tidak pernah ikut tersusun ke dalam SQL dan pemisahan
 * data antar lembaga tetap terjaga. Nilai teks keluaran dikirim mentah; sisi peramban
 * mengisinya lewat textContent sehingga aman dari sisip HTML.
 **[N]**/ ?>
<?
require_once("../include/config.php");
require_once("../include/db_functions.php");

header('Content-Type: application/json; charset=utf-8');

$lembaga = isset($_REQUEST['lembaga']) ? (int)$_REQUEST['lembaga'] : 0;
$detail = isset($_REQUEST['detail']) ? (int)$_REQUEST['detail'] : 0;
$kategori = isset($_REQUEST['kategori']) ? (string)$_REQUEST['kategori'] : '';
$mulai = isset($_REQUEST['mulai']) ? max(0, (int)$_REQUEST['mulai']) : 0;
$jumlah = isset($_REQUEST['jumlah']) ? (int)$_REQUEST['jumlah'] : 9;
if ($jumlah < 1 || $jumlah > 30) $jumlah = 9;
if (!in_array($kategori, array('ifse', 'mading'), true)) $kategori = '';

function jb_berita_cuplikan($pesan, $maks = 180)
{
    $teks = trim(preg_replace('/\s+/u', ' ', strip_tags((string)$pesan)));
    $pendek = function_exists('mb_substr') ? mb_substr($teks, 0, $maks, 'UTF-8') : substr($teks, 0, $maks);
    if ($pendek !== $teks) $pendek = rtrim($pendek) . ' ...';
    return $pendek;
}

function jb_berita_tanggal($datetime)
{
    $bulan = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    $ts = strtotime((string)$datetime);
    if ($ts === false) return '';
    return (int)date('j', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

function jb_berita_baris($replid, $kat, $judul, $pesan, $tanggal, $departemen, $penulis, $foto, $penuh)
{
    global $FILESHARE_ADDR;

    $gambar = ($foto === null || $foto === '') ? ''
        : rtrim($FILESHARE_ADDR, '/') . '/' . ltrim($foto, '/');
    $item = array(
        'id' => (int)$replid,
        'kategori' => $kat,
        'label' => $kat === 'mading' ? 'Mading' : 'Berita',
        'judul' => (string)$judul,
        'tanggal' => jb_berita_tanggal($tanggal),
        'lembaga' => (string)$departemen,
        'penulis' => $penulis === null || $penulis === '' ? 'Tim Humas' : (string)$penulis,
        'gambar' => $gambar,
    );
    $item[$penuh ? 'isi' : 'cuplikan'] = $penuh ? (string)$pesan : jb_berita_cuplikan($pesan);
    return $item;
}

/* Foto sampul: lampiran berita lebih dulu, lalu cover galeri lembaga pada kategori
   yang sama, terakhir kosong supaya sisi peramban memakai papan nama lembaga. */
$kolomFoto = "COALESCE(
                   (SELECT CONCAT(f.location, '/', f.filename)
                      FROM jbsvcr.notesfile f
                     WHERE f.notesid = n.replid
                     ORDER BY f.replid LIMIT 1),
                   (SELECT CONCAT(gf.location, '/', gf.filename)
                      FROM jbsvcr.gallery g
                      JOIN jbsvcr.galleryfile gf ON gf.galleryid = g.replid
                     WHERE g.departemen = n.departemen
                       AND g.kategori = n.kategori
                       AND gf.iscover = 1
                     ORDER BY g.tanggal DESC
                     LIMIT 1)
               ) AS foto";

$kolomDari = "FROM jbsvcr.notes n
          LEFT JOIN jbssdm.pegawai p ON p.nip = n.nip
          LEFT JOIN jbsakad.siswa s ON s.nis = n.nis";

OpenDb();

/* Detail satu berita untuk jendela baca. */
if ($detail > 0)
{
    $stmt = mysqli_prepare($mysqlconnection,
        "SELECT n.replid, n.kategori, n.judul, n.pesan, n.tanggal, n.departemen,
                COALESCE(p.nama, s.nama) AS penulis, $kolomFoto
           $kolomDari
          WHERE n.replid = ?");
    if ($stmt === false)
    {
        echo json_encode(array('error' => 'Berita tidak dapat dibaca.'));
        exit;
    }
    mysqli_stmt_bind_param($stmt, 'i', $detail);
    $item = null;
    if (mysqli_stmt_execute($stmt))
    {
        mysqli_stmt_bind_result($stmt, $replid, $kat, $judul, $pesan, $tanggal, $departemen, $penulis, $foto);
        if (mysqli_stmt_fetch($stmt) === true)
            $item = jb_berita_baris($replid, $kat, $judul, $pesan, $tanggal, $departemen, $penulis, $foto, true);
    }
    mysqli_stmt_close($stmt);

    echo json_encode($item === null ? array('error' => 'Berita tidak ditemukan.') : array('item' => $item));
    exit;
}

/* Nama lembaga diambil dari database, bukan dari permintaan pengunjung. */
$dept = '';
if ($lembaga > 0)
{
    $stmt = mysqli_prepare($mysqlconnection,
        "SELECT departemen FROM jbsakad.departemen WHERE replid = ? AND aktif = 1");
    mysqli_stmt_bind_param($stmt, 'i', $lembaga);
    if (!mysqli_stmt_execute($stmt))
    {
        mysqli_stmt_close($stmt);
        echo json_encode(array('error' => 'Data lembaga tidak dapat dibaca.'));
        exit;
    }
    mysqli_stmt_bind_result($stmt, $dept);
    if (!mysqli_stmt_fetch($stmt))
    {
        mysqli_stmt_close($stmt);
        echo json_encode(array('error' => 'Lembaga tidak ditemukan atau tidak aktif.'));
        exit;
    }
    mysqli_stmt_close($stmt);
}

$syarat = array();
$jenis = '';
$nilai = array();

if ($dept !== '')
{
    $syarat[] = 'n.departemen = ?';
    $jenis .= 's';
    $nilai[] = $dept;
}
if ($kategori !== '')
{
    $syarat[] = 'n.kategori = ?';
    $jenis .= 's';
    $nilai[] = $kategori;
}
$where = $syarat ? ('WHERE ' . implode(' AND ', $syarat)) : '';

$stmt = mysqli_prepare($mysqlconnection, "SELECT COUNT(*) AS n FROM jbsvcr.notes n $where");
if ($stmt === false)
{
    echo json_encode(array('error' => 'Query berita tidak dapat disiapkan.'));
    exit;
}
if ($jenis !== '') mysqli_stmt_bind_param($stmt, $jenis, ...$nilai);
$total = 0;
if (mysqli_stmt_execute($stmt))
{
    mysqli_stmt_bind_result($stmt, $total);
    mysqli_stmt_fetch($stmt);
}
mysqli_stmt_close($stmt);

$sql = "SELECT n.replid, n.kategori, n.judul, n.pesan, n.tanggal, n.departemen,
               COALESCE(p.nama, s.nama) AS penulis, $kolomFoto
          $kolomDari
         $where
         ORDER BY n.tanggal DESC, n.replid DESC
         LIMIT ?, ?";
$stmt = mysqli_prepare($mysqlconnection, $sql);
if ($stmt === false)
{
    echo json_encode(array('error' => 'Query berita tidak dapat disiapkan.'));
    exit;
}
$ambil = $mulai;
$batas = $jumlah;
array_push($nilai, $ambil, $batas);
mysqli_stmt_bind_param($stmt, $jenis . 'ii', ...$nilai);

$isi = array();
if (mysqli_stmt_execute($stmt))
{
    mysqli_stmt_bind_result($stmt, $replid, $kat, $judul, $pesan, $tanggal, $departemen, $penulis, $foto);
    while (mysqli_stmt_fetch($stmt) === true)
        $isi[] = jb_berita_baris($replid, $kat, $judul, $pesan, $tanggal, $departemen, $penulis, $foto, false);
}
mysqli_stmt_close($stmt);

echo json_encode(array(
    'total' => (int)$total,
    'mulai' => $mulai,
    'jumlah' => $jumlah,
    'items' => $isi,
));
