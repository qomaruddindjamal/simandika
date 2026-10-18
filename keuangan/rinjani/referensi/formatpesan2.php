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
require_once('../include/config.php');
require_once('../include/db.onfunc.php');
require_once('../library/msg.php');
require_once('../library/common.func.php');
require_once('../util/peek.php');
require_once('formatpesan2.func.php');

if (getLevel() == 2)
{
    echo "<script>";
    echo "alert('Maaf, anda tidak berhak mengakses halaman ini!');";
    echo "window.history.back();";
    echo "</script>";
    exit();
}

$db = new Db;
$db->TryOpenExit(true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Format Pesan Notifikasi</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="../script/qsbuilder.js?<?=filemtime('../script/qsbuilder.js')?>" ></script>
    <script language="javascript" src="formatpesan2.js?<?=filemtime('formatpesan2.js')?>"></script>
</head>
<body>
<table border="0" width="100%">
<tr>
    <td align="center" valign="top">

    <table border="0" width="95%" align="center">
    <tr>
        <td align="right" valign="top">

            <img class="help-icon-1" title="bantuan" onclick="showHelp()">
            <span class="pageTitle">Format Pesan Notifikasi</span><br>
            <a class="pageLink" href="referensi.php">Referensi</a>&nbsp&gt;&nbsp
            <span class="pageLinkCurrent">Format Pesan Notifikasi</span>

        </td>
    </tr>
    </table>
    <br>

    <table border="0" cellpadding="0" cellspacing="0" width="95%" align="center">
    <tr>
        <td align="right" width="25%">
            <span style="font-size: 14px;">Departemen:</span>&nbsp;&nbsp;
<?php
            $departemen = isset($_REQUEST["departemen"]) ? $_REQUEST["departemen"] : "";
            ShowSelectDepartemen_FPN($db);
?>
        </td>
        <td align="right" width="*">
            <a href="JavaScript:refresh()">
                <img src="../images/ico/refresh.png" border="0"/>&nbsp;Refresh
            </a>&nbsp;&nbsp;
        </td>
    </tr>
    </table><br>

    <table border="0" cellpadding="0" cellspacing="0" width="95%" align="center" style="margin-left: 50px">
    <tr>
        <td align="left">

        <span style="font-size: 12px; font-weight: bold">Pembayaran Siswa</span>:<br>
        <textarea id='sisformatsms' name='sisformatsms' class='inputbox' rows='4' cols='70'><?=$sisformatsms?></textarea><br><br>

        <span style="font-size: 12px; font-weight: bold">Pembayaran Calon Siswa</span>:<br>
        <textarea id='csisformatsms' name='csisformatsms' class='inputbox' rows='4' cols='70'><?=$csisformatsms?></textarea><br><br>

        <span style="font-size: 12px; font-weight: bold">Tabungan Siswa</span>:<br>
        <textarea id='tabunganformatsms' name='tabunganformatsms' class='inputbox' rows='4' cols='70'><?=$tabunganformatsms?></textarea><br><br>

        <span style="font-size: 12px; font-weight: bold">Tunggakan Siswa &amp; Calon Siswa</span>:<br>
        <textarea id='tungformatsms' name='tungformatsms' class='inputbox' rows='4' cols='70'><?=$tunggakformatsms?></textarea><br><br>

        <span style="font-size: 12px; font-weight: bold">Transaksi SchoolPay Cashless Payment</span>:<br>
        <textarea id='paymentformatsms' name='paymentformatsms' class='inputbox' rows='4' cols='70'><?=$paymentformatsms?></textarea><br>

        <br><br>
        <input type="button" class="dialogButtonPositive" value="Simpan" onclick="simpanPesanNotifikasi()">

        </td>
    </tr>
    </table>

    </td>
</tr>
</table>
</body>

<div id="toast-container"></div>
<div id="divDialog"></div>
<div id="divHelpDialog" class="help-dialog"></div>

</html>