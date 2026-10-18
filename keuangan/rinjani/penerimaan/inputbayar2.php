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
require_once('../library/departemen.php');
require_once('../library/msg.php');
require_once('../include/errorhandler.php');
require_once('inputbayar2.func.php');

if (getLevel() == 2)
{
    echo "<script>";
    echo "alert('Maaf, anda tidak berhak mengakses halaman ini!');";
    echo "window.history.back();";
    echo "</script>";
    exit();
}

$db = new Db();
$db->TryOpenExit();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="../style/style.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Besar Pembayaran</title>
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
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="../script/random.js"></script>
    <script language="javascript" src="inputbayar2.js?r=<?=filemtime('inputbayar2.js')?>"></script>
</head>

<body>

<table border="0" width="100%" align="center">
<tr><td align="left" valign="top">

    <table border="0" width="95%" align="center">
    <tr>
        <td align="right">
            <img class="help-icon-1"  src="../images/help32.png" title="bantuan" onclick="showHelp()">
            <span class="pageTitle">Besar Pembayaran</span><br>
            <a class="pageLink" href="penerimaan.php"><b>Penerimaan</b></a>&nbsp;&gt;&nbsp;
            <span class="pageLinkCurrent">Besar Pembayaran</td>
        </td>
    </tr>
    </table><br />


    <table border="0" cellpadding="2" cellspacing="5" align="left">
    <tr>
        <td width="160">&nbsp;</td>
        <td colspan="3">
            <span style="color: #666; font-style: italic">Menentukan besar pembayaran yang harus dibayarkan siswa/calon siswa per jenis penerimaan</span>
            <br><br>
        </td>
    </tr>
    <tr>
        <td width="160">&nbsp;</td>
        <td width="180"><strong>Kategori&nbsp;</strong></td>
        <td width="250">
<?php   $idKategori = "";
        ShowSelectKategori() ?>
        </td>
        <td width="260">&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><strong>Departemen</strong></td>
        <td>
<?php   $departemen = "";
        ShowSelectDepartemen($db) ?>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><strong>Jenis Penerimaan</strong></td>
        <td>
        <div id="divPenerimaan">
<?php   ShowSelectPenerimaan($db, $departemen, $idKategori) ?>
        </div>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><strong>Besar Total Pembayaran</strong></td>
        <td>
            <input type="text" class="inputbox-money" style="font-size: 14px; width: 200px; background-color: #fdffc7" id="besar"
                   onblur="formatRupiah('besar')" onfocus="unformatRupiah('besar')">

        </td>
        <td><span style="color: #666; font-style: italic">besar total pembayaran yang harus dilunasi</span></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><strong>Besar Cicilan</strong></td>
        <td>
            <input type="text" class="inputbox-money" style="font-size: 14px; width: 200px; background-color: #fdffc7" id="cicilan"
                   onblur="formatRupiah('cicilan')" onfocus="unformatRupiah('cicilan')">
        </td>
        <td><span style="color: #666; font-style: italic">besar cicilan pembayaran yang dibayarkan ketika membayar</span></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><strong>Cicilan Pertama</strong></td>
        <td>
            <input type="checkbox" id="cicilanpertama">&nbsp;set cicilan pertama Rp 0
        </td>
        <td><span style="color: #666; font-style: italic">set cicilan pertama Rp 0 supaya muncul di Laporan Tunggakan</span></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><span id="lbTingkat" style="font-weight: bold">Tingkat</span></td>
        <td>
            <div id="divTingkat">
<?php       $idTingkat = 0;
            ShowSelectTingkatSiswa($db, $departemen) ?>
            </div>
        </td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td valign="top"><span id="lbKelas" style="font-weight: bold">Kelas</span></td>
        <td>
            <div id="divKelas">
<?php       ShowSelectKelasSiswa($db, $departemen, $idTingkat) ?>
            </div>
        </td>
        <td valign="top"><span style="color: #666; font-style: italic">pilih minimal satu kelas</span></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td valign="top">&nbsp;</td>
        <td>
            <input type="button" class="dialogButtonPositive" style="height: 30px; width: 100px;" value="Simpan" onclick="simpan()">
        </td>
        <td><span style="color: #666; font-style: italic">Siswa/calon siswa yang sudah terdata besar pembayarannya, tidak akan di data ulang besar pembayarannya</span></td>
    </tr>
    </table>


</td></tr>
</table>

<div id="toast-container"></div>
<div id="divHelpDialog" class="help-dialog"></div>

</body>
</html>

