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
require_once('../library/msg.php');
require_once('../library/rupiah.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Laporan_tabungan_pegawai.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$db = new Db;
$db->TryOpenExit(true);

$departemen = $_REQUEST['departemen'];
$nip = $_REQUEST['nip'];
$nama = $_REQUEST['nama'];
$tanggal1 = $_REQUEST['tanggal1'];
$tanggal2 = $_REQUEST['tanggal2'];
$datetime1 = "$tanggal1 00:00:00";
$datetime2 = "$tanggal2 23:59:59";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Tabungan Pegawai</title>
</head>
<body style="margin: 10px;">
<center><font size="4" face="Arial"><strong>LAPORAN TABUNGAN PEGAWAI</strong></font><br /></center>
<table border="0">
    <tr>
        <td>Departemen:</td>
        <td><?=$departemen?></td>
    </tr>
    <tr>
        <td>Pegawai:</td>
        <td><?= "$nama - $nip" ?></td>
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

<?php
$sql = "SELECT DISTINCT t.idtabungan, dt.nama
          FROM jbsfina.tabunganp t, jbsfina.datatabunganp dt
         WHERE t.idtabungan = dt.replid
           AND t.nip = '$nip'
           AND t.tanggal BETWEEN '$datetime1' AND '$datetime2'";
$lsTab = array();
$res = $db->QueryDb($sql);
while($row = mysqli_fetch_row($res))
{
    $lsTab[] = array($row[0], $row[1]);
}

if (count($lsTab) == 0)
{
    echo "<i>Belum ada data tabungan!</i>";
    exit();
}

echo "<table>";
echo "<tr>";
echo "<td>No</td>";
echo "<td>Tabungan</td>";
echo "<td>Jumlah Setoran</td>";
echo "<td>Setoran Akhir</td>";
echo "<td>Jumlah Tarikan</td>";
echo "<td>Tarikan Akhir</td>";
echo "<td>Total Setoran</td>";
echo "<td>Total Tarikan</td>";
echo "<td>Saldo</td>";
echo "</tr>";

$cnt = 0;
for($i = 0; $i < count($lsTab); $i++)
{
    $idTab = $lsTab[$i][0];
    $nmTab = $lsTab[$i][1];

    $totsetor = 0;
    $tottarik = 0;
    $saldo = 0;
    $sql = "SELECT SUM(debet), SUM(kredit)
              FROM jbsfina.tabunganp
             WHERE idtabungan = '$idTab'
               AND nip = '$nip'";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        $tottarik = $row[0];
        $totsetor = $row[1];
        $saldo = $totsetor - $tottarik;
    }

    $subsetor = 0;
    $subtarik = 0;
    $sql = "SELECT SUM(debet), SUM(kredit)
              FROM jbsfina.tabunganp
             WHERE idtabungan = '$idTab'
               AND nip = '$nip'
               AND tanggal BETWEEN '$datetime1' AND '$datetime2'";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        $subtarik = $row[0];
        $subsetor = $row[1];
    }

    $lastsetor = 0;
    $tgllastsetor = "";
    $sql = "SELECT kredit, DATE_FORMAT(tanggal, '%d-%b-%Y %H:%i:%s')
             FROM jbsfina.tabunganp
            WHERE idtabungan = '$idTab'
              AND nip = '$nip'
              AND tanggal BETWEEN '$datetime1' AND '$datetime2'
              AND kredit <> 0
            ORDER BY replid DESC
            LIMIT 1";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        $lastsetor = $row[0];
        $tgllastsetor = $row[1];
    }

    $lasttarik = 0;
    $tgllasttarik = "";
    $sql = "SELECT debet, DATE_FORMAT(tanggal, '%d-%b-%Y %H:%i:%s')
              FROM jbsfina.tabunganp
             WHERE idtabungan = '$idTab'
               AND nip = '$nip'
               AND tanggal BETWEEN '$datetime1' AND '$datetime2'
               AND debet <> 0
             ORDER BY replid DESC
             LIMIT 1";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        $lasttarik = $row[0];
        $tgllasttarik = $row[1];
    }

    $cnt += 1;
    echo "<tr>";
    echo "<td> $cnt </td>";
    echo "<td>$nmTab</td>";
    echo "<td>" . FormatRupiah($subsetor) . "</td>";
    echo "<td>" . FormatRupiah($lastsetor) . "</td>";
    echo "<td>" . FormatRupiah($subtarik) . "</td>";
    echo "<td>" . FormatRupiah($lasttarik) . "</td>";
    echo "<td>" . FormatRupiah($totsetor) . "</td>";
    echo "<td>" . FormatRupiah($tottarik) . "</td>";
    echo "<td>" . FormatRupiah($saldo) . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";
?>

</body>
</html>