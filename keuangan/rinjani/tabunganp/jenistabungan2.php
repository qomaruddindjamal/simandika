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
require_once('jenistabungan2.func.php');

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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Jenis Tabungan Pegawai</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="jenistabungan2.js?r=<?=filemtime('jenistabungan2.js')?>"></script>
</head>

<body>

<table border="0" width="100%" height="100%">
<tr>
    <td align="center" valign="top">

    <table border="0" width="100%" align="center">
    <tr>
        <td align="left" valign="top">

        <table border="0" width="95%" align="center">
        <tr>
            <td align="right">
                <img class="help-icon-1" src="../images/help32.png" title="bantuan" onclick="showHelp()">
                <span class="pageTitle">Jenis Tabungan Pegawai</span><br>
                <a class="pageLink" href="tabunganp.php"><b>Tabungan Pegawai</b></a>&nbsp;&gt;&nbsp;
                <span class="pageLinkCurrent">Jenis Tabungan Pegawai
            </td>
        </tr>
        </table><br>

        <table border="0" cellpadding="5" cellspacing="0" align="left" width="90%" style="margin-left: 100px; margin-bottom: 10px;">
        <tr>
            <td width="100" align="right"><strong>Departemen</strong></td>
            <td width="200">
<?php           $departemen = isset($_REQUEST["departemen"]) ? $_REQUEST["departemen"] : "";
                ShowSelectDepartemenTabunganPegawai($db); ?>
            </td>
            <td width="*" align="right">
                <a href="#" onClick="refresh()"><img src="../images/ico/refresh.png" border="0"/>&nbsp;refresh</a>&nbsp;&nbsp;
                <a href="JavaScript:cetak()"><img src="../images/ico/print.png" border="0"/>&nbsp;cetak</a>&nbsp;&nbsp;
                <a href="JavaScript:tambah()"><img src="../images/ico/tambah.png" border="0">&nbsp;tambah</a>
            </td>
        </tr>
        </table>

        <div id="dvTableContent">
<?php       ShowDaftarJenisTabunganPegawai($db); ?>
        </div>

        </td>
    </tr>
    </table>

    </td>
</tr>
</table>

<div id="divHelpDialog" class="help-dialog"></div>

</body>
</html>