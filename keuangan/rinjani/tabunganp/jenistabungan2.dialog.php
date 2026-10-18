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
require_once('../include/db.onpage.php');
require_once('../include/db.onfunc.php');
require_once('jenistabungan2.dialog.func.php');

$db = new Db();
$db->TryOpenExit();

$idJenis = $_REQUEST["idjenis"];
$departemen = $_REQUEST["departemen"];

$nama = "";
$rekKas = "";
$rekUtang = "";
$keterangan = "";
$namaRekKas = "";
$namaRekUtang = "";
$isUsed = false;
$sendNotif = 0;

$title = "Jenis Tabungan Pegawai";
if ($idJenis != 0)
{
    $title = "Ubah Jenis Tabungan Pegawai";
    LoadValues($db, $idJenis);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$title?></title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/tooltips.rinjani.css?<?=filemtime('../style/tooltips.rinjani.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js?<?=filemtime('../style/tables.css')?>"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/tooltips.rinjani.js?r=<?= filemtime('../script/tooltips.rinjani.js') ?>"></script>
    <script language="javascript" src="jenistabungan2.dialog.js?r=<?= filemtime('jenistabungan2.dialog.js') ?>"></script>
</head>
<body style="padding: 10px">
<span class="dialogTitle"><?=$title?></span><br><br>
<input type="hidden" id="idjenis" value="<?=$idJenis?>">
<input type="hidden" id="departemen" value="<?=$departemen?>">

<table cellpadding="5" cellspacing="0">
<tr style="height: 30px;">
    <td width="120">Departemen</td>
    <td width="500"><b><?=$departemen?></b></td>
</tr>
<tr>
    <td>Nama<?=$tag_mandatory?></td>
    <td>
        <input id="nama" type="text" class="inputbox" style="width: 250px" value="<?=$nama?>">
    </td>
</tr>
<tr>
    <td>Rek Kas<?=$tag_mandatory?></td>
    <td>
        <input id="inforekkas" type="text" class="inputbox" readonly style="background-color: #efefef; width: 250px;"
               value="<?= "$rekKas $namaRekKas"?>">
        <input id="rekkas" type="hidden" value="<?=$rekKas?>">
<?php   if (!$isUsed) {  ?>
        <input type='button' class='but' style='width: 40px; height: 25px' value='(..)' title='pilih / tambah kode rekening' onclick='showRekAkunDialog("HARTA", "KAS")'>
<?php   } else { ?>
        <img src="../images/ico/warning16.png" title="tidak dapat diubah karena sudah digunakan dalam transaksi">
<?php   } ?>
        <img src="../images/help32.png" class="tooltip-icon"
             title="informasi"
             onclick="showTooltip(this, '../help/tp_tt_jenistabungan.html', 'auto', 400)" >
    </td>
</tr>
<tr>
    <td>Rek Utang<?=$tag_mandatory?></td>
    <td>
        <input id="inforekutang" type="text" class="inputbox" readonly style="background-color: #efefef; width: 250px;"
               value="<?= "$rekUtang $namaRekUtang"?>">
        <input id="rekutang" type="hidden" value="<?=$rekUtang?>">
<?php   if (!$isUsed) {  ?>
        <input type='button' class='but' style='width: 40px; height: 25px' value='(..)' title='pilih / tambah kode rekening' onclick='showRekAkunDialog("UTANG", "UTANG")'>
<?php   } else { ?>
        <img src="../images/ico/warning16.png" title="tidak dapat diubah karena sudah digunakan dalam transaksi">
<?php   } ?>
    </td>
</tr>
<tr>
    <td>Keterangan</td>
    <td>
        <textarea rows="3" cols="40" class="inputbox" id="keterangan"><?=$keterangan?></textarea>
    </td>
</tr>
<tr>
    <td>Notifikasi</td>
    <td>
        <input type="checkbox" id="sendnotif" <?= ($sendNotif == 1) ? "checked" : ""?>>
        <i>kirim melalui Jendela Sekolah | Telegram | SMS</i>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <br>
        <input type="button" class="dialogButtonPositive" value="Simpan" onclick="simpanJenisTabungan()">
        <input type="button" class="dialogButtonNegative" value="Tutup" onclick="window.close()">
    </td>
</tr>
</table>

<div id="tooltip" class="tooltip hidden" aria-hidden="true">
    <button class="tooltip-close">&times;</button>
    <div class="tooltip-arrow"></div>
    <div class="tooltip-content"></div>
</div>

</body>
</html>
