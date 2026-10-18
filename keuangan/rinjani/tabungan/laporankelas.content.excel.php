<?php
/**[N]**
 * JIBAS Education Community
 * Jaringan Informasi Bersama Antar Sekolah
 *
 * @version: 33.0 (Jan 05, 2026)
 * @notes:
 *
 * Copyright (C) 2024 JIBAS (http://www.jibas.net)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 **[N]**/ ?>
<?php
require_once('../include/sessioninfo.php');
require_once('../include/sessionchecker.php');
require_once('../library/common.func.php');
require_once('../include/config.php');
require_once('../include/db.onfunc.php');
require_once('../library/msg.php');
require_once('../library/rupiah.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('laporankelas.content.func.php');

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Laporan_tabungan_siswa_per_kelas.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idTingkat = RequestData("idtingkat", 0);
$namaTingkat = RequestData("namatingkat", "");
$idKelas = RequestData("idkelas", 0);
$namaKelas = RequestData("namakelas", "");
$idTabungan = RequestData("idtabungan", 0);
$namaTabungan = RequestData("namatabungan", "");
$urut = RequestData("urut", "s.nama");
$page = RequestData("page", 1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Tabungan Siswa Per Kelas</title>
</head>
<body style="margin: 10px;">
<center><font size="4" face="Arial"><strong>LAPORAN TABUNGAN SISWA PER KELAS</strong></font><br /></center>
<table border="0">
<tr>
    <td>Departemen:</td>
    <td><?=$departemen?></td>
</tr>
<tr>
    <td>Tingkat:</td>
    <td><?=$namaTingkat?></td>
</tr>
<tr>
    <td>Kelas:</td>
    <td><?=$namaKelas?></td>
</tr>
<tr>
    <td>Tabungan:</td>
    <td><?=$namaTabungan?></td>
</tr>
</table>
<br>

<?php
$nisList = "";
PrepareNisList($db);
if ($nisList == "")
{
    echo "<span style='color: maroon'>belum ada data tabungan di tingkat/kelas atau tabungan terpilih</span>";
    exit();
}
?>

<br>
<?php
ShowRekapTabunganSiswa($db, false);
ShowDaftarTabunganSiswa($db, false);
?>
</body>
</html>