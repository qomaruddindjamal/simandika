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
require_once('../util/peek.php');
require_once('../include/errorhandler.php');

$db = new Db;
$db->TryOpenExit(true);

$idpengeluaran = RequestData("idpengeluaran", 0);
$namapengeluaran = RequestData("namapengeluaran", "");
$departemen = RequestData("departemen", "");
$tanggal1 = RequestData("tanggal1", date("Y-m-d"));
$tanggal2 = RequestData("tanggal2", date("Y-m-d"));
$idtahunbuku = RequestData("idtahunbuku", 0);
$namatahunbuku = RequestData("namatahunbuku", "");

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Laporan_Transaksi_Pengeluaran.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Transaksi Pengeluaran</title>
</head>
<body style="margin: 10px">

<div id="dvLaporan">
<?php
    $sql = "SELECT COUNT(p.replid), SUM(p.jumlah) 
              FROM jbsfina.pengeluaran p, jbsfina.datapengeluaran d 
             WHERE p.idpengeluaran = d.replid 
               AND d.replid = '$idpengeluaran' 
               AND d.departemen = '$departemen' 
               AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
             ORDER BY p.tanggal";
    $res = $db->QueryDb($sql);
    $row = mysqli_fetch_row($res);
    $nData = $row[0];
    $totalJumlah = $row[1];
    ?>
    <strong>LAPORAN TRANSAKSI PENGELUARAN</strong>
    <br />
    <table border="0">
        <tr>
            <td>Departemen:</td>
            <td><?=$departemen?></td>
        </tr>
        <tr>
            <td>Tahun Buku</td>
            <td><?=$namatahunbuku?></td>
        </tr>
        <tr>
            <td>Pengeluaran</td>
            <td><?= $namapengeluaran ?></td>
        </tr>
        <tr>
            <td>Tanggal:</td>
            <td><?= LongDateFormat($tanggal1) . " s/d " . LongDateFormat($tanggal2) ?></td>
        </tr>
        <tr>
            <td>Tanggal Cetak:</td>
            <td><?= date('d F Y H:i:s') ?></td>
        </tr>
    </table>
    <br>

    <table>
    <tr>
        <td>Jumlah Pengeluaran:</td>
        <td><?= $nData ?></td>
    </tr>
    <tr>
        <td>Total Pengeluaran</td>
        <td><?= FormatRupiah($totalJumlah)  ?></td>
    </tr>
    </table><br>

    <table>
    <tr>
        <td>No</td>
        <td>Tanggal</td>
        <td>Pemohon</td>
        <td>Penerima</td>
        <td>Keperluan</td>
        <td>Petugas</td>
        <td>Jumlah</td>
    </tr>
<?php
    $sql = "SELECT p.replid AS id, p.keperluan, p.keterangan, p.jenispemohon, p.nip, p.namapemohon, 
                   p.nis, p.pemohonlain, p.penerima, date_format(p.tanggal, '%d-%b-%Y') as tanggal, 
                   date_format(p.tanggalkeluar, '%d-%b-%Y') as tanggalkeluar, p.petugas, p.jumlah 
              FROM jbsfina.pengeluaran p, jbsfina.datapengeluaran d 
             WHERE p.idpengeluaran = d.replid 
               AND d.replid = '$idpengeluaran' 
               AND d.departemen = '$departemen' 
               AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
             ORDER BY p.tanggal";

    $res = $db->QueryDb($sql);
    $cnt = 0;
    $total = 0;
    while ($row = mysqli_fetch_array($res))
    {
        $namapemohon = $row['namapemohon'];
        $total += $row['jumlah'];
        ?>

        <tr>
            <td><?=++$cnt ?></td>
            <td><?=$row['tanggal'] ?></td>
            <td><?=$row['namapemohon'] ?></td>
            <td><?=$row['penerima'] ?></td>
            <td>
                <?=$row['keperluan'] ?><br />
<?php           if (strlen(trim($row['keterangan'])) > 0)
                    echo "<b>Keterangan: </b> " . $row['keterangan']; ?>
            </td>
            <td><?=$row['petugas'] ?></td>
            <td><?=FormatRupiah($row['jumlah']) ?></td>
        </tr>
<?php
    }
    ?>
    </table>
</div>
</body>
</html>