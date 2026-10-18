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
require_once('kartu.siswa.func.php');

$db = new Db();
$db->TryOpenExit();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Kartu Pembayaran Siswa</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/random.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="../script/request.factory.js?r=<?=filemtime('../script/request.factory.js')?>"></script>
    <script language="javascript" src="kartu.siswa.js?r=<?=filemtime('kartu.siswa.js')?>"></script>
</head>

<body>

<table border="0" width="95%" align="center">
<tr>
    <td align="right" valign="top">

        <img class="help-icon-1" src="../images/help32.png" title="bantuan" onclick="showHelp()">
        <span class="pageTitle">Kartu Pembayaran Siswa</span><br>
        <a class="pageLink" href="schoolpay.php"><b>SchoolPay</b></a>&nbsp;&gt;&nbsp;
        <span class="pageLinkCurrent">Kartu Pembayaran Siswa</td>

    </td>
</tr>
</table>
<br>

<table border="0" width="100%" align="left">
<tr>
<td align="left" valign="top" width="10%">
    &nbsp;
</td>
<td align="left" valign="top" width="*">

    <table border="0" cellpadding="5" cellspacing="0">
    <tr>
        <td width="100" align="right">
            <strong>Departemen:</strong>
        </td>
        <td align="left">
<?php       ShowCbDepartemen($db) ?>
        </td>
        <td rowspan="2" valign="middle">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="button" id="btLihat" class="dialogButtonPositive" style="height: 50px; width: 85px;" value="Lihat" onclick="showKartuSiswa()">
        </td>
    </tr>
    <tr>
        <td width="100" align="right">
            <strong>Kelas:</strong>
        </td>
        <td align="left">
            <span id="spCbTingkat">
<?php           ShowCbTingkat($db, $selDepartemen) ?>
            </span>
            <span id="spCbKelas">
<?php           ShowCbKelas($db, $selDepartemen, $selIdTingkat) ?>
            </span>
        </td>
    </tr>
    </table>

    </td>
</tr>
<tr>
    <td align="left" valign="top" width="10%">
        &nbsp;
    </td>
    <td align="left" valign="top" width="*">

        <table border="0" cellspacing="2" cellpadding="2">
        <tr><td align="left">
            <span id="spReport"></span>
        </td></tr>
        </table>

    </td>
</tr>
</table>

<div id="divHelpDialog" class="help-dialog"></div>

</body>
</html>
