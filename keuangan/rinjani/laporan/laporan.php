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
require_once('../library/qsbuilder.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Keuangan</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
</head>
<body>

<table border="0" cellspacing="0" cellpadding="0" align="center" width="70%">
<tr>
    <td align="center" width="100%">
        <span class="pageTitle">LAPORAN KEUANGAN</span><br><br><br>

        <table border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td colspan="3" align="center">
                <span class="pageLinkCurrent">INFORMASI</span><br>
            </td>
        </tr>
        <tr>
            <td align="center" width="120">
                <a href="transaksi.php">
                    <img src="../images/laptrans01.png" style="width: 40px" border="0" title="Transaksi Keuangan"><br>
                    Transaksi Keuangan
                </a><br>
            </td>
            <td align="center" width="10">&nbsp;</td>
            <td align="center" width="120">
                <a href="audit.php">
                    <img src="../images/lapaudit01.png" style="width: 40px" border="0" title="Audit Perubahan Data"><br>
                    Audit Perubahan Data
                </a><br>
            </td>
        </tr>
        </table>
        <br><br>

        <table border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td colspan="5" align="center">
                <span class="pageLinkCurrent">AKUNTANSI</span><br>
            </td>
        </tr>
        <tr>
            <td align="center" width="120">
                <a href="bukubesar.php">
                    <img src="../images/lapbukubesar01.png" border="0" style="width: 40px" title="Buku Besar"><br>
                    Buku Besar
                </a><br>
            </td>
            <td align="center" width="10">&nbsp;</td>
            <td align="center" width="120">
                <a href="neracaper.php">
                    <img src="../images/laptrialbalance01.png" border="0" style="width: 40px" title="Neraca Percobaan"><br>
                    Neraca Percobaan
                </a><br>
            </td>
            <td align="center" width="10">&nbsp;</td>
            <td align="center" width="120">
                <a href="rugilaba.php">
                    <img src="../images/lapprofit01.png" border="0" style="width: 40px" title="Rugi Laba"><br>
                    Rugi Laba
                </a><br>
            </td>
        </tr>
        <tr>
            <td colspan="5" align="center" style="height: 10px">
                &nbsp;
            </td>
        </tr>
        <tr>
            <td align="center" width="120">
                <a href="modal.php">
                    <img src="../images/lapmodal01.png" border="0" style="width: 40px" title="Perubahan Modal"><br>
                    Perubahan Modal
                </a><br>
            </td>
            <td align="center" width="10">&nbsp;</td>
            <td align="center" width="120">
                <a href="neraca.php">
                    <img src="../images/lapneraca01.png" border="0" style="width: 40px" title="Neraca"><br>
                    Neraca
                </a><br>
            </td>
            <td align="center" width="10">&nbsp;</td>
            <td align="center" width="120">
                <a href="cashflow.php">
                    <img src="../images/lapcashflow01.png" border="0"  style="width: 40px" title="Arus Kas"><br>
                    Arus Kas
                </a><br>
            </td>
        </tr>
        </table>


    </td>
</tr>
</table>

</body>
</html>