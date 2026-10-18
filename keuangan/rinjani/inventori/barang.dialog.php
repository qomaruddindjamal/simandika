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
require_once('../library/rupiah.php');
require_once('../library/msg.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('barang.dialog.func.php');

$db = new Db;
$db->TryOpenExit(true);

$idkelompok = RequestData("idkelompok", 0);
$namakelompok = RequestData("namakelompok", "");
$id = RequestData("id", 0);

$satuan = "unit";
$tanggal = date("Y-m-d");
$ftanggal = LongDateFormat($tanggal);
$totalharga = "";

$title = "Tambah Barang";
if ($id != 0)
{
    $title = "Ubah Barang";
    LoadValues($db);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= $title ?></title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/rupiah3.js"></script>
    <script language="javascript" src="../script/vldr.js?r=<?=filemtime('../script/vldr.js')?>"></script>
    <script language="javascript" src="../script/stringutil.js"></script>
    <script language="javascript" src="../script/dateutil.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="barang.dialog.js?r=<?=filemtime('barang.dialog.js')?>"></script>
</head>
<body style="margin: 10px">

<span class="dialogTitle"><?=$title?></span><br><br>
<input type="hidden" id="idkelompok" value="<?=$idkelompok?>">
<input type="hidden" id="id" value="<?=$id?>">

<table cellpadding="2" cellspacing="0">
<tr>
    <td>Kelompok</td>
    <td>
        <input type="hidden" id="idkelompok" value="<?=$idkelompok?>">
        <input id="kelompok" readonly type="text" class="inputbox" style="width: 250px; background-color: #ededed;"
               maxlength="100" value="<?=$namakelompok?>">
    </td>
</tr>
<tr>
    <td>Kode<?=$tag_mandatory?></td>
    <td>
        <input id="kode" type="text" class="inputbox" style="width: 150px" maxlength="20" value="<?=$kode?>">
    </td>
</tr>
<tr>
    <td>Nama<?=$tag_mandatory?></td>
    <td>
        <input id="nama" type="text" class="inputbox" style="width: 250px" maxlength="50" value="<?=$nama?>">
    </td>
</tr>
<tr>
    <td>Jumlah<?=$tag_mandatory?></td>
    <td>
        <input id="jumlah" type="text" class="inputbox" style="width: 80px" maxlength="5" value="<?=$jumlah?>"
               onblur="hitungTotal()" >
        &nbsp;&nbsp;
        satuan:
        <input id="satuan" type="text" class="inputbox" style="width: 80px" maxlength="5" value="<?=$satuan?>">
    </td>
</tr>
<tr>
    <td>Harga Satuan<?=$tag_mandatory?></td>
    <td>
        <input id="harga" type="text" class="inputbox-money fw-bold" style="width: 250px" maxlength="50" value="<?=$harga?>"
               onblur="Rupiah.FormatRupiah('harga'); hitungTotal();"
               onfocus="Rupiah.UnformatRupiah('harga')">
    </td>
</tr>
<tr>
    <td>Total Harga</td>
    <td>
        <input id="totalharga" type="text" class="inputbox" readonly style="width: 250px; background-color: #ededed;"
               maxlength="50" value="<?=$totalharga?>">
    </td>
</tr>
<tr>
    <td>Tanggal Perolehan<?=$tag_mandatory?></td>
    <td>
        <input id="ftanggal" type="text" class="inputbox" readonly style="width: 180px;  background-color: #ededed;"
               maxlength="50" value="<?=$ftanggal?>">
        <input type="hidden" id="tanggal" value="<?=$tanggal?>">
        <a href="#" onclick="showPilihTanggal()">
            <img src="../images/ico/calendar.png" border="0" id="bttutup"/>
        </a>
    </td>
</tr>
<tr>
    <td>Foto</td>
    <td>
        <input id="foto" type="file" class="inputbox" style="width: 250px; background-color: #ededed;">
    </td>
</tr>
<tr>
    <td>Kondisi</td>
    <td>
        <textarea rows="2" cols="40" class="inputbox" maxlength="255" id="kondisi"><?=$kondisi?></textarea>
    </td>
</tr>
<tr>
    <td>Keterangan</td>
    <td>
        <textarea rows="2" cols="40" class="inputbox" maxlength="255" id="keterangan"><?=$keterangan?></textarea>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <br>
        <input type="button" class="dialogButtonPositive" value="Simpan" onclick="simpanBarang()">
        <input type="button" class="dialogButtonNegative" value="Tutup" onclick="window.close()">
        <br>
        <span id="spInfo"></span>
    </td>
</tr>
</table>

</body>
</html>