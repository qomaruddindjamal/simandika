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
require_once('../include/errorhandler.php');

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Rekap_tabungan.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');


$db = new Db;
$db->TryOpenExit(true);

$departemen = $_REQUEST['departemen'];
$idpetugas = $_REQUEST["idpetugas"];
$namapetugas = $_REQUEST["namapetugas"];
$tanggal1 = $_REQUEST['tanggal1'];
$tanggal2 = $_REQUEST['tanggal2'];
$datetime1 = "$tanggal1 00:00:00";
$datetime2 = "$tanggal2 23:59:59";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekapitulasi Tabungan</title>
<body style="margin: 10px;">

<center><font size="4" face="Arial"><strong>REKAPITULAS TABUNGAN</strong></font><br /></center>
<table border="0">
<tr>
    <td>Departemen:</td>
    <td><?=$departemen?></td>
</tr>
<tr>
    <td>Petugas:</td>
    <td><?=$namapetugas?></td>
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
$sql = "SELECT DISTINCT t.idtabungan, dt.nama, 'siswa'
          FROM jbsfina.tabungan t, jbsfina.datatabungan dt
         WHERE t.idtabungan = dt.replid
           AND dt.departemen = '$departemen'
           AND t.tanggal BETWEEN '$datetime1' AND '$datetime2'";
if ($idpetugas != "ALL")
    $sql .= " AND t.petugas = '$idpetugas'";

$lsTab = array();

$res = $db->QueryDb($sql);
while($row = mysqli_fetch_row($res))
{
    $lsTab[] = array($row[0], $row[1], $row[2]);
}

$sql = "SELECT DISTINCT t.idtabungan, dt.nama, 'pegawai'
          FROM jbsfina.tabunganp t, jbsfina.datatabunganp dt
         WHERE t.idtabungan = dt.replid
           AND dt.departemen = '$departemen'
           AND t.tanggal BETWEEN '$datetime1' AND '$datetime2'";
if ($idpetugas != "ALL")
    $sql .= " AND t.petugas = '$idpetugas'";

$res = $db->QueryDb($sql);
while($row = mysqli_fetch_row($res))
{
    $lsTab[] = array($row[0], $row[1], $row[2]);
}

if (count($lsTab) == 0)
{
    echo "<br><br><i>Belum ada transaksi Tabungan</i>";
    echo "</body></html>";

    exit();
}

echo "<table>";
echo "<tr>";
echo "<td>No</td>";
echo "<td>Tabungan</td>";
echo "<td>Jumlah Setoran</td>";
echo "<td>Jumlah Tarikan</td>";
echo "<td>Selisih Saldo</td>";
echo "</tr>";

$totsetor = 0;
$tottarik = 0;
$totsaldo = 0;

$last_kelompok = "";
for($i = 0; $i < count($lsTab); $i++)
{
    $no = $i + 1;
    $idTab = $lsTab[$i][0];
    $nmTab = $lsTab[$i][1];
    $kelompok = $lsTab[$i][2];

    $table = $kelompok == "siswa" ? "jbsfina.tabungan" : "jbsfina.tabunganp";

    $jumsetor = 0;
    $jumtarik = 0;
    $jumsaldo = 0;
    $sql = "SELECT SUM(debet), SUM(kredit)
              FROM $table 
             WHERE idtabungan = '$idTab'
               AND tanggal BETWEEN '$datetime1' AND '$datetime2' ";
    if ($idpetugas != "ALL")
        $sql .= " AND petugas = '$idpetugas'";

    $res = $db->QueryDb($sql);
    if($row = mysqli_fetch_row($res))
    {
        $jumtarik = $row[0];
        $jumsetor = $row[1];
        $jumsaldo = $jumsetor - $jumtarik;
    }

    $totsetor += $jumsetor;
    $tottarik += $jumtarik;
    $totsaldo += $jumsaldo;

    if ($last_kelompok != $kelompok)
    {
        $last_kelompok = $kelompok;
        $judul = $kelompok == "siswa" ? "TABUNGAN SISWA" : "TABUNGAN PEGAWAI";
        echo "<tr><td colspan='5'>$judul</td></tr>";
    }

    echo "<tr>";
    echo "<td>$no</td>";
    echo "<td>$nmTab</td>";
    echo "<td>";
    echo FormatRupiah($jumsetor);
    echo "</td>";
    echo "<td>";
    echo FormatRupiah($jumtarik);
    echo "</td>";
    echo "<td>" . FormatRupiah($jumsaldo) . "</td>";
    echo "</tr>";
}
echo "<tr>";
echo "<td colspan='2'>T O T A L</td>";
echo "<td>" . FormatRupiah($totsetor) . "</td>";
echo "<td>" . FormatRupiah($tottarik) . "</td>";
echo "<td>" . FormatRupiah($totsaldo) . "</td>";
echo "</tr>";
echo "</table>";
echo "</div>";
?>
</body>
</html>