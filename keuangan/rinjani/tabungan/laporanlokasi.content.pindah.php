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
require_once('../library/logger.php');
require_once('../library/rupiah.php');
require_once('../util/peek.php');
require_once('laporanlokasi.content.pindah.func.php');

$db = new Db();
$db->TryOpenExit();

$departemen = RequestData("departemen", "");
$idTabungan = RequestData("idtabungan", 0);
$namaTabungan = RequestData("namatabungan", "");
$stIdList64 = RequestData("stidlist64", "");
$stIdList = base64_decode($stIdList64);
$namaLokasi = RequestData("namalokasi", "");
$kodeLokasi = RequestData("kodelokasi", "");
$kelompok = RequestData("kelompok", "");

$table = $kelompok == "siswa" ? "jbsfina.tabungan" : "jbsfina.tabunganp";

$sql = "SELECT COUNT(replid), SUM(kredit - debet)
          FROM $table
         WHERE replid IN ($stIdList)";
$res = $db->QueryDb($sql);
$row = mysqli_fetch_row($res);
$jumlah = $row[0];
$saldo = $row[1];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pindah Lokasi Dana Tabungan <?=$namaTabungan?></title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js?<?=filemtime('../style/tables.css')?>"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/rupiah3.js"></script>
    <script language="javascript" src="laporanlokasi.content.pindah.js?r=<?= filemtime('laporanlokasi.content.pindah.js') ?>"></script>
</head>
<body style="padding: 10px">
<span class="dialogTitle">Pindah Lokasi Dana Tabungan <?=$namaTabungan?></span><br><br>

<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idtabungan" value="<?= $idTabungan ?>">
<input type="hidden" id="namatabungan" value="<?= $namaTabungan ?>">
<input type="hidden" id="stidlist64" value="<?= $stIdList64 ?>">
<input type="hidden" id="namalokasi" value="<?= $namaLokasi ?>">
<input type="hidden" id="kodelokasi" value="<?= $kodeLokasi ?>">
<input type="hidden" id="kelompok" value="<?= $kelompok ?>">
<input type="hidden" id="jumlah" value="<?= $jumlah ?>">
<input type="hidden" id="saldo" value="<?= $saldo ?>">

<table border="0" cellpadding="5" cellspacing="0" width="100%">
<tr>
    <td width="180">Departemen</td>
    <td width="600"><b><?=$departemen?></b></td>
</tr>
<tr>
    <td>Tabungan</td>
    <td><b><?=$namaTabungan?></b></td>
</tr>
<tr>
    <td>Lokasi Dana</td>
    <td><b><?=$namaLokasi?></b></td>
</tr>
<tr>
    <td>Jumlah Data</td>
    <td>
        <b><?=$jumlah?></b>
    </td>
</tr>
<tr>
    <td>Saldo</td>
    <td>
        <span style="font-size: 18px; font-family: 'Courier New', Courier, monospace">
        <b><?= FormatRupiah($saldo) ?></b>
        </span>
    </td>
</tr>
<tr>
    <td>Lokasi Tujuan <?= $tag_mandatory ?></td>
    <td>
<?php
        ShowSelectLokasiDanaTabunganPindah($db);
?>
    </td>
</tr>
<tr>
    <td valign="top">Keterangan</td>
    <td>
        <textarea id="keterangan" rows="2" cols="28" class="inputbox"></textarea>
    </td>
</tr>
<tr>
    <td valign="top">Alasan Pemindahan <?= $tag_mandatory ?></td>
    <td>
        <textarea id="alasan" rows="2" cols="28" class="inputbox"></textarea>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <br>
        <input type="button" class="dialogButtonPositive" id="btSimpan" value="Simpan" onclick="simpanPindahLokasi()">
        <input type="button" class="dialogButtonNegative" id="btTutup" value="Tutup" onclick="window.close()"><br>
        <span id="spInfo" style="color: blue"></span>
    </td>
</tr>
</table>

</body>
</html>
