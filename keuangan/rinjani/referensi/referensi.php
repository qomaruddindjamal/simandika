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
    <title>Referensi</title>
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
        <span class="pageTitle">REFERENSI</span>
        <br><br>

<?php   $qsb = new QsBuilder();
        $qsb->Add("from", "Referensi");
        $qsb->Add("sourcefrom", "referensi.php"); ?>

        <table border="0" cellpadding="5" cellspacing="0" align="center">
        <tr>
            <td colspan="7" align="center">
                <span class="pageLinkCurrent">PENGATURAN</span><br>
            </td>
        </tr>
        <tr>
            <td align="center" width="120">
                <a href="sumberdana.php?<?= $qsb->CreateQs() ?>">
                    <img src="../images/cate01.png" style="width: 40px" border="0" title="Sumber Dana"><br>
                    Sumber Dana
                </a><br>
            </td>
            <td align="center" width="10">&nbsp;</td>
            <td align="center" width="120">
                <a href="lokasidana.php?<?= $qsb->CreateQs() ?>">
                    <img src="../images/cate01.png" style="width: 40px" border="0" title="Lokasi Dana"><br>
                    Lokasi Dana
                </a><br>
            </td>
            <td align="center" width="10">&nbsp;</td>
            <td align="center" width="120">
                <a href="akunrek.php?<?= $qsb->CreateQs() ?>">
                    <img src="../images/code01.png" style="width: 40px" title="Kode Rekening Akutansi" border="0"><br>
                    Kode Rekening Akutansi
                </a><br>
            </td>
            <td align="center" width="10">&nbsp;</td>
            <td align="center" width="120">
                <a href="formatpesan2.php">
                    <img src="../images/notif01.png" style="width: 40px" title="Format Pesan Notifikasi"><br>
                    Format Pesan Notifikasi
                </a><br>
            </td>
        </tr>
        </table>
        <br>

        <table border="0" cellpadding="5" cellspacing="0" align="center">
        <tr>
            <td colspan="3" align="center">
                <span class="pageLinkCurrent">TAHUN BUKU</span><br>
            </td>
        </tr>
        <tr>
            <td align="center" width="120">
                <a href="tahunbuku2.php?<?= $qsb->CreateQs() ?>">
                    <img src="../images/calendar01.png" style="width: 40px" title="Tahun Buku" border="0"><br>
                    Tahun Buku
                </a><br>
            </td>
            <td align="center" width="10">&nbsp;</td>
            <td align="center" width="120">
                <a href="tutupbuku21.php">
                    <img src="../images/book01.png" style="width: 40px" border="0" title="Tutup Buku"><br>
                    Tutup Buku
                </a><br>
            </td>
        </tr>
        </table>

    </td>
</tr>
</table>

</body>
</html>