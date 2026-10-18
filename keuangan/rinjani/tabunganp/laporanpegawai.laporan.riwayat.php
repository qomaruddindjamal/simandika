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
require_once('transaksi.tabungan.func.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = $_REQUEST['departemen'];
$nip = $_REQUEST['nip'];
$nama = $_REQUEST["nama"];
$idtabungan = $_REQUEST['idtabungan'];
$namatabungan = $_REQUEST["namatabungan"];

$page = 1;

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

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Riwayat Tabungan Pegawai</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="laporanpegawai.laporan.riwayat.js?r=<?=filemtime('laporanpegawai.laporan.riwayat.js')?>"></script>
</head>
<body style="margin: 10px;">
<input type="hidden" id="nip" value="<?= $nip ?>">
<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idtahunbuku" value="<?= $idtahunbuku ?>">
<input type="hidden" id="idtabungan" value="<?= $idtabungan ?>">

<span class="dialogTitle"><?=$namatabungan?></span><br>
<span style="font-size: 16px; font-family: 'Segoe UI', sans-serif;"><?= "$nama - $nip "?></span>
<br><br>

<div id="divSectionRiwayat" class="rounded-box" style="margin-top: 5px; padding: 20px;">
<table cellpadding='0' cellspacing='0' border='0' style='width: 100%'>
<tr><td>
    <div id='dvTabTabunganList'>
<?php   $page = 1;
        $totalPage = 0;
        $nData = 0;
        ShowTransaksiTabunganPegawai($db, false) ?>
    </div>
</td></tr>
<tr><td>
    <div id='dvPageControl'>
<?php   ShowPageControl() ?>
    </div>
</td></tr>
</table>
</div>

</body>
</html>