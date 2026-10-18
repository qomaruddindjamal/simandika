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
require_once('bayartunggak.header.func.php');

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
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="bayartunggak.header.js?r=<?=filemtime('bayartunggak.header.js')?>"></script>
</head>

<body style="margin: 0;">
<table border="0" width="98%" cellpadding="0" cellspacing="0" align="center">
<tr>
    <td width="45%">

        <table width="100%" border="0">
        <tr>
            <td align="left" width="15%"><strong>Departemen&nbsp;</strong></td>
            <td width="*">
<?php           $departemen = "";
                ShowSelectDept($db); ?>
                <strong>Tahun Buku&nbsp;</strong>
                <span id="spTahunBuku">
<?php           ShowPrevAccYear($db);    ?>
                </span>
            </td>
        </tr>
        <tr>
            <td align="left"><strong>Kategori&nbsp;</strong></td>
            <td width="*">
<?php           $idkategori = "";
                ShowSelectKategoriPenerimaan($db); ?>
                <span id="spPenerimaan">
<?php           ShowSelectJenisPenerimaan($db); ?>
                </span>
            </td>
        </tr>
        </table>

    </td>
    <td width="6%" valign="middle">
        <a href="#" onclick="showContent()">
            <img src="../images/view.png" border="0" height="48" width="48"/>
        </a>
    </td>
    <td width="*" valign="middle">

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