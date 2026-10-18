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
require_once('../include/sessionchecker.php');
require_once('../include/sessioninfo.php');
require_once('../include/config.php');
require_once('../library/common.func.php');
require_once('../library/rupiah.php');
require_once('../include/db.onfunc.php');
require_once('../library/departemen.php');
require_once('../include/errorhandler.php');
require_once('konfigurasi.dialog.func.php');

$db = new Db();
$db->TryOpenExit();

$idPt = $_REQUEST["idpt"];
$dept = $_REQUEST["dept"];

$idTabungan = 0;
$rekKasVendor = "";
$namaRekKasVendor = "";
$rekUtangVendor = "";
$namaRekUtangVendor = "";
$maxTransVendor = 0;
$isReadOnly = "";

LoadValue($db);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Konfigurasi SchoolPay</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/tooltips.rinjani.css?<?=filemtime('tooltips.rinjani.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/rupiah2.js" ></script>
    <script language="javascript" src="../script/rupiah3.js" ></script>
    <script language="javascript" src="../script/qsbuilder.js" ></script>
    <script language="javascript" src="../script/vldr.js" ></script>
    <script language="javascript" src="../script/tooltips.rinjani.js?r=<?=filemtime('../script/tooltips.rinjani.js')?>"></script>
    <script language="javascript" src="../script/request.factory.js?r=<?=filemtime('../script/request.factory.js')?>"></script>
    <script language="javascript" src="konfigurasi.dialog.js?r=<?=filemtime('konfigurasi.dialog.js')?>"></script>
</head>

<body >
<table border="0" width="100%" cellpadding="10">
<tr>
    <td align="left" valign="top">

        <span style="font-size: 14pt">Konfigurasi Cashless Payment Siswa</span><br><br>

        <input type="hidden" id="idpt" name="idpt" value="<?=$idPt?>">
        <table border="0" width="100%" cellpadding="5" cellspacing="2">
        <tr>
            <td align="right" width="15%">Departemen</td>
            <td align="left" width="*">
                <input type="text" id="dept" name="dept" value="<?= $dept ?>" readonly
                       class="inputbox" style="background-color: #ededed;" >
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                Auto Debet<br>Tabungan<?= $tag_mandatory ?>
            </td>
            <td align="left" valign="top">
<?php           ShowSelectTabunganSiswa($db) ?>
<?php           if ($isReadOnly) { ?>
                    <img src="../images/ico/warning16.png" title="tidak dapat mengubah karena sudah digunakan dalam transaksi">
<?php           }  ?>
                <img src="../images/help32.png" class="tooltip-icon"  title="help"
                     onclick="showTooltip(this, '../help/sp_tt_tabungan.html', 'auto', 500)"  >

            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                Maksimum Transaksi<br>per Hari<?= $tag_mandatory ?>
            </td>
            <td align="left" valign="top">
                <input type="text" id="maxtrans" name="maxtrans" class="inputbox" value="<?= FormatRupiah($maxTransVendor) ?>" onblur="formatRupiah('maxtrans');" onfocus="unformatRupiah('maxtrans');">
                <img src="../images/help32.png" class="tooltip-icon"  title="help"
                     onclick="showTooltip(this, '../help/sp_tt_maksimum.html', 'auto', 500)"  >
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                Rek Kas<br>Vendor<?= $tag_mandatory ?>
            </td>
            <td align="left" valign="top">
                <input id="inforekkasvendor" type="text" class="inputbox" readonly
                       style="background-color: #efefef; width: 250px;"
                       value="<?= "$rekKasVendor $namaRekKasVendor"?>">
                <input id="rekkasvendor" type="hidden" value="<?= $rekKasVendor ?>">
<?php           if ($isReadOnly) { ?>
                    <img src="../images/ico/warning16.png" title="tidak dapat mengubah karena sudah digunakan dalam transaksi">
<?php           } else { ?>
                    <input type='button' class='but' style='width: 40px; height: 25px' value='(..)' title='pilih / tambah kode rekening' onclick='showRekAkunDialog("HARTA", "KAS")'>
<?php           } ?>
                <img src="../images/help32.png" class="tooltip-icon" title="help"
                     onclick="showTooltip(this, '../help/sp_tt_kasvendor.html', 'auto', 500)"  >
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                Rek Utang<br>Vendor<?= $tag_mandatory ?>
            </td>
            <td align="left" valign="top">
                <input id="inforekutangvendor" type="text" class="inputbox" readonly
                       style="background-color: #efefef; width: 250px;"
                       value="<?= "$rekUtangVendor $namaRekUtangVendor"?>">
                <input id="rekutangvendor" type="hidden" value="<?= $rekUtangVendor ?>">
<?php           if ($isReadOnly) { ?>
                    <img src="../images/ico/warning16.png" title="tidak dapat mengubah karena sudah digunakan dalam transaksi">
<?php           } else { ?>
                    <input type='button' class='but' style='width: 40px; height: 25px' value='(..)' title='pilih / tambah kode rekening' onclick='showRekAkunDialog("UTANG", "UTANG")'>
<?php           } ?>
                <img src="../images/help32.png" class="tooltip-icon" title="help"
                     onclick="showTooltip(this, '../help/sp_tt_piutangvendor.html', 'auto', 500)"  >
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <br>
                <input type="button" class="dialogButtonPositive" value="Simpan" style="width: 80px; height: 30px;" onclick="simpanKonfigurasi()">
                <input type="button" class="dialogButtonNegative" value="Tutup" style="width: 80px; height: 30px;" onclick="window.close()">
            </td>
        </tr>
        <tr>
            <td colspan="2" align="left">
                <fieldset style="border-color: #ddd; color: white; border-width: 1px;">
                    <legend style="background-color: #333; color: white;">&nbsp;Perhatian:&nbsp;</legend>
                    <span style="color: black; font-size: 12px; line-height: 20px;">
                    Mohon diperhatikan pilihan dan pengaturan untuk Konfigurasi ini. Karena setelah digunakan dalam Transaksi, konfigurasi tidak dapat diubah kecuali Maksimum Transaksi per Hari.
                    </span>
                </fieldset>
            </td>
        </tr>
        </table>

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