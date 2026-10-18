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
    <title>SchoolPay</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="schoolpay.js?<?= filemtime('schoolpay.js')?>" ></script>
</head>
<body>

<table border="0" cellspacing="0" cellpadding="0" align="center" width="70%">
<tr>
    <td align="center" width="100%">
        <img class="help-icon-1" src="../images/help32.png" title="bantuan" onclick="showHelp()">
        <span class="pageTitle">SCHOOLPAY</span><br><br>

        <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" width="80">
                <img src="../images/settings01.png" style="width: 40px" title="Referensi">
            </td>
            <td align="left" valign="top" width="400">
                <span class="pageLinkCurrent">Referensi</span><br>
<?php           $qsb = new QsBuilder();
                $qsb->Add("from", "SchoolPay");
                $qsb->Add("sourcefrom", "../schoolpay/schoolpay.php") ?>
                <?=$bullet_blue?><a href="../referensi/tahunbuku2.php?<?=$qsb->CreateQs()?>">Tahun Buku</a><br>
                <?=$bullet_blue?><a href="../referensi/akunrek.php?<?=$qsb->CreateQs()?>">Kode Rekening Akutansi</a><br>
            </td>
        </tr>
        <tr style="height: 30px">
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td align="center" valign="top" width="80">
                <img src="../images/cate05.png" style="width: 40px" title="Pengaturan">
            </td>
            <td align="left" valign="top" width="400">
                <span class="pageLinkCurrent">Pengaturan</span><br>
                <?=$bullet_blue?><a href="konfigurasi.php">Konfigurasi SchoolPay</a><br>
                <?=$bullet_blue?><a href="userpos.php">Staf Vendor</a><br>
                <?=$bullet_blue?><a href="vendor.php">Daftar Vendor</a><br>
                <?=$bullet_blue?><a href="maxtrans.siswa.php">Batasan Transaksi Harian Siswa</a><br>
            </td>
        </tr>
        <tr style="height: 30px">
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td align="center" valign="top" width="80">
                <img src="../images/card01.png" style="width: 40px" title="Kartu Pembayaran">
            </td>
            <td align="left" valign="top" width="400">
                <span class="pageLinkCurrent">Kartu Pembayaran</span><br>
                <?=$bullet_blue?><a href="kartu.siswa.php">Kartu Pembayaran Siswa</a><br>
                <?=$bullet_blue?><a href="kartu.pegawai.php">Kartu Pembayaran Pegawai</a><br>
            </td>
        </tr>
        <tr style="height: 30px">
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td align="center" valign="top" width="80">
                <img src="../images/scan02.png" style="width: 70px" title="Pembayaran">
            </td>
            <td align="left" valign="top" width="400">
                <span class="pageLinkCurrent">Transaksi Pembayaran</span><br>
                <?=$bullet_blue?><a href="https://jibas.net/content/schoolpay/schoolpay.php" target="_blank">SchoolPay POS Android</a><br>
            </td>
        </tr>
        <tr style="height: 30px">
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td align="center" valign="top" width="80">
                <img src="../images/report04a.png" style="width: 40px" title="Pelaporan">
            </td>
            <td align="left" valign="top" width="400">
                <span class="pageLinkCurrent">Pelaporan</span><br>
                <?=$bullet_blue?><a href="dailytrans.php">Riwayat Transaksi Harian</a><br>
                <?=$bullet_blue?><a href="client.trans.php">Riwayat Transaksi Pelanggan</a><br>
                <?=$bullet_blue?><a href="cari.trans.php">Cari Informasi Transaksi</a><br>
                <?=$bullet_blue?><a href="rekap.trans.php">Rekapitulasi Transaksi Vendor</a><br>
                <?=$bullet_blue?><a href="stat.trans.php">Statistik Transaksi</a><br>
                <?=$bullet_blue?><a href="riwayat.login.php">Riwayat Login</a><br>
            </td>
        </tr>
        <tr style="height: 30px">
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td align="center" valign="top" width="80">
                <img src="../images/refund01.png" style="width: 40px" title="Refund">
            </td>
            <td align="left" valign="top" width="400">
                <span class="pageLinkCurrent">Refund Penerimaan Vendor</span><br>
                <?=$bullet_blue?><a href="tagihan.vendor.php">Tagihan Penerimaan Vendor</a><br>
                <?=$bullet_blue?><a href="refund.php">Refund Penerimaan Vendor</a><br>
            </td>
        </tr>
        </table>
    </td>
</tr>
</table>

<div id="divHelpDialog" class="help-dialog"></div>

</body>
</html>