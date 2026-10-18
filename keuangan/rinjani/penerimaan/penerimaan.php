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
    <title>Penerimaan</title>
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
        <span class="pageTitle">PENERIMAAN</span><br><br>

        <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" width="80">
                <img src="../images/settings01.png" style="width: 40px" title="Referensi">
            </td>
            <td align="left" valign="top" width="400">
                <span class="pageLinkCurrent">Referensi</span><br>
<?php           $qsb = new QsBuilder();
                $qsb->Add("from", "Penerimaan");
                $qsb->Add("sourcefrom", "../penerimaan/penerimaan.php") ?>
                <?=$bullet_blue?><a href="../referensi/tahunbuku2.php?<?=$qsb->CreateQs()?>">Tahun Buku</a><br>
                <?=$bullet_blue?><a href="../referensi/akunrek.php?<?=$qsb->CreateQs()?>">Kode Rekening Akutansi</a><br>
                <?=$bullet_blue?><a href="../referensi/sumberdana.php?<?=$qsb->CreateQs()?>">Sumber Dana</a><br>
            </td>
        </tr>
        <tr style="height: 30px">
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td align="center" valign="top" width="80">
                <img src="../images/categories01.png" style="width: 40px" title="Pengaturan">
            </td>
            <td align="left" valign="top" width="400">
                <span class="pageLinkCurrent">Pengaturan</span><br>
                <?=$bullet_blue?><a href="jenispenerimaan2.php">Jenis Penerimaan</a><br>
                <?=$bullet_blue?><a href="inputbayar2.php">Besar Pembayaran</a><br>
                <?=$bullet_blue?><a href="autotrans2.setting.php">Pengaturan Batch Payment</a>
            </td>
        </tr>
        <tr style="height: 30px">
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td align="center" valign="top" width="80">
                <img src="../images/sync01.png" style="width: 40px" title="Pembayaran">
            </td>
            <td align="left" valign="top" width="400">
                <span class="pageLinkCurrent">Pembayaran</span><br>
                <?=$bullet_blue?><a href="pembayaran.main.php">Single Payment</a><br>
                <?=$bullet_blue?><a href="multitrans2.php">Multi Payment</a><br>
                <?=$bullet_blue?><a href="autotrans2.payment.php">Batch Payment</a>&nbsp;<br>
                <hr style="border: 1px dashed #999;">
                <span class="pageLinkCurrent">Pembayaran Sisa Tunggakan</span>&nbsp;
                <span style="font-size: 10px; color: #666;"><i>(tahun buku lalu)</i></span><br>
                <?=$bullet_blue?><a href="bayartunggak.php">Single Payment - Sisa Tunggakan</a><br>
                <?=$bullet_blue?><a href="transprev.php">Multi Payment - Sisa Tunggakan</a>
            </td>
        </tr>
        <tr style="height: 30px">
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td align="center" valign="top" width="80">
                <img src="../images/report01.png" style="width: 40px" title="Pelaporan">
            </td>
            <td align="left" valign="top" width="400">
                <span class="pageLinkCurrent">Pelaporan</span><br>
                <?=$bullet_blue?><a href="laporan/bayarsiswa.kelas.php">Pembayaran per Kelas</a><br>
                <?=$bullet_blue?><a href="laporan/bayarsiswa.php">Pembayaran per Siswa</a><br>
                <?=$bullet_blue?><a href="laporan/bayarsiswa.tunggak.php">Pembayaran Siswa Yang Menunggak</a><br>
                <hr style="border: 1px dashed #999;">
                <?=$bullet_blue?><a href="laporan/bayarcalon.kelompok.php">Pembayaran per Kelompok Calon Siswa</a><br>
                <?=$bullet_blue?><a href="laporan/bayarcalon.php">Pembayaran per Calon Siswa</a><br>
                <?=$bullet_blue?><a href="laporan/bayarcalon.tunggak.php">Pembayaran Calon Siswa Yang Menunggak</a><br>
                <hr style="border: 1px dashed #999;">
                <?=$bullet_blue?><a href="laporan/rekap.pembayaran.php">Rekapitulasi Penerimaan</a><br>
                <?=$bullet_blue?><a href="laporan/rekap.tunggakan.php">Rekapitulasi Tunggakan Siswa</a><br>
                <?=$bullet_blue?><a href="laporan/penerimaanlain.php">Penerimaan Lain</a><br>
                <?=$bullet_blue?><a href="laporan/jurnalpenerimaan.php">Jurnal Penerimaan</a><br>
            </td>
        </tr>
        </table>
    </td>
</tr>

</table>

</body>
</html>