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
require_once('../library/common.func.php');
require_once('../include/config.php');
require_once('../library/rupiah.php');
require_once('../include/db.onfunc.php');
require_once('../library/departemen.php');
require_once('../include/errorhandler.php');
require_once('../library/date.func.php');
require_once('riwayat.login.func.php');

$db = new Db;
$db->TryOpenExit(true);

$sql = "SELECT DATE_FORMAT(CURDATE(), '%Y-%m-%d')";
$res = $db->QueryDb($sql);
$row = mysqli_fetch_row($res);
$dt1 = $row[0];
$fdt1 = LongDateFormat($dt1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Riwayat Login</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/stringutil.js"></script>
    <script language="javascript" src="../script/dateutil.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="../script/request.factory.js?r=<?=filemtime('../script/request.factory.js')?>"></script>
    <script language="javascript" src="riwayat.login.js?r=<?=filemtime('riwayat.login.js')?>"></script>
</head>

<body>

<table border="0" width="95%" align="center">
<tr>
    <td align="left" valign="top" width="60%">

        <table border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td width="60" align="right">
                <strong>Vendor:</strong>
            </td>
            <td align="left">
<?php           ShowCbVendor($db) ?>
            </td>
            <td rowspan="2" valign="middle">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" class="dialogButtonPositive" style="height: 50px; width: 85px;" value="Lihat" onclick="showRiwayatLogin()">
            </td>
        </tr>
        <tr>
            <td width="60" align="right">
                <strong>Tanggal:</strong>
            </td>
            <td align="left">
                <input type='text' id='ftanggal1' onclick='showPilihTanggal(1, "<?=$dt1?>")'
                       readonly size='15' value='<?=$fdt1?>'
                       class='inputbox' style='background-color:#ddd; width: 150px;'>&nbsp;
                <input type='hidden' id='tanggal1' class="inputbox" value='<?=$dt1?>'>
                <a href='#' onclick='showPilihTanggal(1, "<?=$dt1?>")'>
                    <img src='../../images/ico/calendar.png' border='0'/>
                </a>
            </td>
        </tr>
        </table>

    </td>
    <td align="right" valign="top" width="40%">

        <img class="help-icon-1" src="../images/help32.png" title="bantuan" onclick="showHelp()">
        <span class="pageTitle">Riwayat Login SchoolPay POS Android</span><br>
        <a class="pageLink" href="schoolpay.php"><b>SchoolPay</b></a>&nbsp;&gt;&nbsp;
        <span class="pageLinkCurrent">Riwayat Login SchoolPay POS Android

    </td>
</tr>
</table>
<br>

<table border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
<tr><td align="left">
    <span id="spReport"></span>
</td></tr>
</table>

<div id="divHelpDialog" class="help-dialog"></div>

</body>
</html>
