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
require_once('../library/rupiah.php');
require_once('../include/config.php');
require_once('../include/db.onfunc.php');
require_once('../include/errorhandler.php');
require_once('../include/getheader.php');
require_once('bukubesar.laporan.func.php');

$db = new Db();
$db->TryOpenExit();

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Laporan_Buku_Besar.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$departemen = $_REQUEST["departemen"];
$idTahunBuku = $_REQUEST["idtahunbuku"];
$namaTahunBuku = $_REQUEST["namatahunbuku"];
$tanggal1 = $_REQUEST["tanggal1"];
$tanggal2 = $_REQUEST["tanggal2"];
$kategori = $_REQUEST["kategori"];
$koderek = $_REQUEST["koderek"];
$namarek = $_REQUEST["namarek"];
$page = 1;
$urut = $_REQUEST["urut"];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Laporan Buku Besar</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>

<center><font size="4" face="Arial"><strong>LAPORAN BUKU BESAR</strong></font><br /></center>
<br /><br /><br />

<table border="0">
    <tr>
        <td>Departemen:</td>
        <td><?=$departemen?></td>
    </tr>
    <tr>
        <td>Tahun Buku:</td>
        <td><?=$namaTahunBuku?></td>
    </tr>
    <tr>
        <td>Tanggal:</td>
        <td><?= LongDateFormat($tanggal1) . " s/d " . LongDateFormat($tanggal2) ?></td>
    </tr>
    <tr>
        <td>Rekening:</td>
        <td><?= "$koderek $namarek" ?></td>
    </tr>
    <tr>
        <td>Tanggal Cetak:</td>
        <td><?= date('d F Y H:i:s') ?></td>
    </tr>
</table>

<?php
ShowRekapBukuBesar($db);
?>
<br>
<?php
ShowTransaksiBukuBesar($db, false);
?>

</body>
</html>