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
require_once('../../include/sessioninfo.php');
require_once('../../include/sessionchecker.php');
require_once('../../library/common.func.php');
require_once('../../include/config.php');
require_once('../../include/db.onfunc.php');
require_once('../../library/departemen.php');
require_once('../../library/msg.php');
require_once('../../library/rupiah.php');
require_once('../../library/userinfo.php');
require_once('../../util/peek.php');
require_once('../../include/errorhandler.php');
require_once('bayarcalon.laporan.func.php');

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Laporan_Pembayaran_Calon_Siswa.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');


$db = new Db;
$db->TryOpenExit(true);

$departemen = $_REQUEST['departemen'];
$nic = $_REQUEST['userid'];
$nama = $_REQUEST['username'];
$tanggal1 = $_REQUEST['tanggal1'];
$tanggal2 = $_REQUEST['tanggal2'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Pembayaran Calon Siswa</title>
</head>
<body style="margin: 10px;">
<?php
$sql = "SELECT replid 
          FROM jbsfina.tahunbuku 
         WHERE departemen = '$departemen' 
           AND aktif = 1";
$res = $db->QueryDb($sql);
if (mysqli_num_rows($res) == 0)
{
    $db->Close();

    echo "<span style='color: maroon; font-size: 13px;'>";
    echo "Belum ada Tahun buku yang Aktif di departemen $departemen. Silakan isi/aktifkan Tahun Buku di menu Referensi";
    echo "</span>";

    exit();
}
$row = mysqli_fetch_row($res);
$idtahunbuku = $row[0];
echo "<input type='hidden' id='idtahunbuku' value='$idtahunbuku'>";

$userInfo = UserInfo::CalonSiswa($db, $nic);
if ($userInfo->Exist == false)
{
    echo "<i>Tidak ditemukan data calon siswa $nic /khnck</i>";
    exit();
}
?>
<input type="hidden" id="idcalon" value="<?= $userInfo->IdCalonSiswa ?>">

<center><font size="4" face="Arial"><strong>LAPORAN PEMBAYARAN CALON SISWA</strong></font><br /></center>
<br /><br /><br />

<table border="0">
    <tr>
        <td>Nama:</td>
        <td><?=$userInfo->Nama?></td>
    </tr>
    <tr>
        <td>No PendaftaranS:</td>
        <td><?=$userInfo->NIC?></td>
    </tr>
    <tr>
        <td>Departemen:</td>
        <td><?=$userInfo->Departemen?></td>
    </tr>
    <tr>
        <td>Proses:</td>
        <td><?=$userInfo->Proses?></td>
    </tr>
    <tr>
        <td>Kelompok:</td>
        <td><?=$userInfo->Kelompok?></td>
    </tr>
</table>
<br>

<div id="dvLaporan">
<?php
    $idcalon = $userInfo->IdCalonSiswa;
    ShowLaporanPembayaranCalonSiswa($db);
    ?>
</div>

</body>
</html>