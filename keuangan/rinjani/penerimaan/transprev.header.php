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
require_once('../util/peek.php');
require_once('../library/userinfo.php');
require_once('../include/errorhandler.php');
require_once('transprev.header.func.php');

$departemen = RequestData("departemen", "");

$db = new Db();
$db->TryOpenExit();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Multiple Transactions</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="transprev.header.js?r=<?=filemtime('transprev.header.js')?>"></script>
</head>

<body style="margin: 0;">
<table border="0" width="98%" cellpadding="0" cellspacing="0" align="center">
<tr>
    <td width="45%">

        <table width="100%" border="0">
        <tr>
            <td align="left" width = "15%"><strong>Departemen&nbsp;</strong></td>
            <td width="*">
<?php           ShowSelectDept($db); ?>
            <strong>Tahun Buku&nbsp;</strong>
<?php           ShowPrevAccYear($db);    ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Cari&nbsp;</strong>&nbsp;&nbsp;&nbsp;
                <input type="hidden" name="kelompok" id="kelompok">
                <select id="selkelompok" class="inputbox" style="width: 100px" onchange="onSelKelompokChange()">
                    <option value="siswa">Siswa</option>
                    <option value="calonsiswa">Calon Siswa</option>
                </select>
                <input type="text" name="noid" id="noid" class="inputbox inputbox-readonly" readonly style="width: 120px">
                <input type="text" name="nama" id="nama"  class="inputbox inputbox-readonly" readonly style="width: 200px">
                <input type="hidden" name="kelas" id="kelas">
                <input type="button" class="dialogButtonGray" value="..." style="width: 30px" onclick="SearchUser()">
            </td>
        </tr>
        </table>

    </td>
    <td width="6%" valign="middle">
        <a href="#" onclick="StartPayment()">
            <img src="../images/view.png" border="0" height="48" width="48"/>
        </a>
    </td>
    <td width="*" valign="middle">
        <strong>Scan Barcode:</strong><br>
        <input name="txBarcode" id="txBarcode" type="text" class="inputbox" style="width: 200px; font-size: 18px;"
               onfocus="this.style.background = '#27d1e5'"
               onblur="this.style.background = '#FFFFFF'"
               onkeyup="return scanBarcode(event)">
        <br>
        <span id="spScanInfo" name="spScanInfo" style="color: red"></span>
        <br>
    </td>
    <td width="25%" align="right" valign="top">
        <img class="help-icon-1"  src="../images/help32.png" title="bantuan" onclick="showHelp()">
        <span class="pageTitle">Pembayaran Sisa Tunggakan</span><br>
        <a class="pageLink" href="penerimaan.php" target="_parent"><b>Penerimaan</b></a>&nbsp;&gt;&nbsp;
        <span class="pageLinkCurrent">Pembayaran Sisa Tunggakan
    </td>
</tr>
</table>
</body>
</html>