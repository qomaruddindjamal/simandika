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
require_once('../library/departemen.php');
require_once('../library/msg.php');
require_once('../library/rupiah.php');
require_once('../include/errorhandler.php');
require_once('autotrans2.setting.penerimaan.func.php');

if (getLevel() == 2)
{
    echo "<script>";
    echo "alert('Maaf, anda tidak berhak mengakses halaman ini!');";
    echo "window.history.back();";
    echo "</script>";
    exit();
}

$kelompok = $_REQUEST["kelompok"];
$departemen = $_REQUEST["departemen"];

OpenDb();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="../style/style.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pilih Transaksi Penerimaan</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/rupiah2.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="autotrans2.setting.penerimaan.js?<?=filemtime('autotrans2.setting.penerimaan.js')?>"></script>
</head>

<body>

<table border="0" cellpadding="10" width="100%">
<tr><td>

<span style="font-size: 16px">Pilih Transaksi Penerimaan</span><br><br>
<input type="hidden" id="departemen" value="<?=$departemen?>">
<table border="0" cellpadding="3" width="100%" height="100%">
<tr>
    <td width="100">Kategori:<?=$tag_mandatory?></td>
    <td>
<?php
        $idKategori = "";
        ShowKategoriPenerimaan();
?>
    </td>
</tr>
<tr>
    <td>Penerimaan:<?=$tag_mandatory?></td>
    <td>
        <span id="spPenerimaan">
<?php
        ShowPenerimaan($departemen, $idKategori);
?>
        </span>
    </td>
</tr>
<tr>
    <td>Besar Cicilan:<?=$tag_mandatory?></td>
    <td>
        <input type="text" class='inputbox-money' style="font-size: 14px; width: 200px; background-color: #fdffc7" id="besar" onblur="formatRupiah('besar')" onfocus="unformatRupiah('besar')">
    </td>
</tr>
<tr>
    <td>Urutan:<?=$tag_mandatory?></td>
    <td>
        <input type="text"  class='inputbox' style="font-size: 14px; width: 30px; background-color: #fdffc7" maxlength="2" id="urutan">
    </td>
</tr>
<tr>
    <td valign="top">Keterangan:</td>
    <td>
        <textarea id="keterangan" class='inputbox' rows="3" cols="30"></textarea>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <br>
        <input type="button" value="Simpan" class="dialogButtonPositive" style="height: 30px; width: 80px;" onclick="simpanPenerimaan()">&nbsp;
        <input type="button" value="Tutup" class="dialogButtonNegative" style="height: 30px; width: 80px;" onclick="window.close()">
    </td>
</tr>
</table>

</td></tr>
</table>

</body>

</html>
<?php
CloseDb();
?>
