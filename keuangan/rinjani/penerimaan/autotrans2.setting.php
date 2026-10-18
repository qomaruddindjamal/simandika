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
require_once('../library/rupiah.php');
require_once('../include/errorhandler.php');
require_once('autotrans2.setting.func.php');

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
    <title>Pengaturan Batch Paymentt</title>
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
    <script language="javascript" src="autotrans2.setting.js?<?=filemtime('autotrans2.setting.js')?>"></script>
</head>

<body>

<table border="0" width="100%" align="center">
<tr><td align="center" valign="top">

    <table border="0" width="95%" align="center">
    <tr>
        <td align="right">
            <img class="help-icon-1"  src="../images/help32.png" title="bantuan" onclick="showHelp()">
            <span class="pageTitle">Pengaturan Batch Payment</span><br>
            <a class="pageLink" href="penerimaan.php"><b>Penerimaan</b></a>&nbsp;&gt;&nbsp;
            <span class="pageLinkCurrent">Pengaturan Batch Payment</td>
        </td>
    </tr>
    </table><br />

    <table border="0" cellpadding="2" cellspacing="0" width="1120" align="center">
    <tr>
        <td width="100"><strong>Departemen</strong></td>
        <td>
<?php       $departemen = "";
            ShowSelectDepartemen($db) ?>
        </td>
        <td width="200" align="right">
            <a onclick="refreshPage()" style="cursor: pointer"><img src="../images/ico/refresh.png">&nbsp;refresh</a>&nbsp;&nbsp;
            <a onclick="tambahAutoTrans()" style="cursor: pointer"><img src="../images/ico/tambah.png">&nbsp;tambah</a>
        </td>
    </tr>
    </table>

    <div id="divDaftar">
<?php
    ShowDaftar($db, $departemen)
?>
    </div>


</td></tr>
</table>

<div id="divHelpDialog" class="help-dialog"></div>

</body>
</html>

