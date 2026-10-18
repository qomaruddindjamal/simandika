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
header('Content-Disposition: attachment; filename=Saldo_tabungan_lokasi.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$db = new Db;
$db->TryOpenExit(true);

$departemen = $_REQUEST['departemen'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Saldo Tabungan per Lokasi</title>
<body style="margin: 10px;">

<center><font size="4" face="Arial"><strong>SALDO TABUNGAN per LOKASI</strong></font><br /></center>
<table border="0">
<tr>
    <td>Departemen:</td>
    <td><?=$departemen?></td>
</tr>
<tr>
    <td>Tanggal Cetak:</td>
    <td><?= date('d F Y H:i:s') ?></td>
</tr>
</table>
<br>

<?php
$lsTab = array();

$sql = "SELECT DISTINCT t.idtabungan, dt.nama, 'siswa'
          FROM jbsfina.tabungan t, jbsfina.datatabungan dt
         WHERE t.idtabungan = dt.replid
           AND dt.departemen = '$departemen'";
$res = $db->QueryDb($sql);
while($row = mysqli_fetch_row($res))
{
    $lsTab[] = array($row[0], $row[1], $row[2]);
}

$sql = "SELECT DISTINCT t.idtabungan, dt.nama, 'pegawai'
          FROM jbsfina.tabunganp t, jbsfina.datatabunganp dt
         WHERE t.idtabungan = dt.replid
           AND dt.departemen = '$departemen'";
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

$sql = "SELECT kode, nama
          FROM jbsfina.lokasidana
         ORDER BY urutan";
$res = $db->QueryDb($sql);
$lsLokasi[] = array("***", "Tidak ada data");
while($row = mysqli_fetch_row($res))
{
    $lsLokasi[] = array($row[0], $row[1]);
}
$nLokasi = count($lsLokasi);

$lsTotalLokasi = array();
for($i = 0; $i < $nLokasi; $i++)
{
    $lsTotalLokasi[] = 0;
}

echo "<table>";
echo "<tr>";
echo "<td>No</td>";
echo "<td>Tabungan</td>";

for($i = 0; $i < count($lsLokasi); $i++)
{
    $kode = $lsLokasi[$i][0];
    $nama = $lsLokasi[$i][1];

    echo "<td>$nama";
    if ($kode != "***")
        echo "<br>$kode";
    echo "</td>";
}
echo "<td>Saldo Terakhir</td>";
echo "</tr>";

$totsaldo = 0;

$last_kelompok = "";
$ixData = 0;
$totalAllTab = 0;
for($i = 0; $i < count($lsTab); $i++)
{
    $no = $i + 1;
    $idTab = $lsTab[$i][0];
    $nmTab = $lsTab[$i][1];
    $kelompok = $lsTab[$i][2];

    $table = $kelompok == "siswa" ? "jbsfina.tabungan" : "jbsfina.tabunganp";

    if ($last_kelompok != $kelompok)
    {
        $last_kelompok = $kelompok;
        $judul = $kelompok == "siswa" ? "TABUNGAN SISWA" : "TABUNGAN PEGAWAI";
        $colspan = $nLokasi + 3;
        echo "<tr><td colspan='$colspan'>$judul</td></tr>";
    }

    echo "<tr>";
    echo "<td>$no</td>";
    echo "<td>$nmTab</td>";

    $totalTab = 0;

    for($j = 0; $j < count($lsLokasi); $j++)
    {
        $ixData += 1;

        $kode = $lsLokasi[$j][0];
        $namaLokasi = $lsLokasi[$j][1];

        if ($kode == "***")
            $kodeValue = " IS NULL";
        else
            $kodeValue = " = '$kode'";

        $sql = "SELECT IFNULL(GROUP_CONCAT(replid SEPARATOR ','), '') 
                  FROM $table
                 WHERE lokasidana $kodeValue
                   AND idtabungan = $idTab";
        $res = $db->QueryDb($sql);
        $row = mysqli_fetch_row($res);
        $stIdList = $row[0];

        if ($stIdList == "")
        {
            echo "<td>&nbsp;</td>";
        }
        else
        {
            $sql = "SELECT SUM(kredit) - SUM(debet)
                      FROM $table
                     WHERE replid IN ($stIdList)";
            $res = $db->QueryDb($sql);
            $row = mysqli_fetch_row($res);
            $jumlah = $row[0];

            $lsTotalLokasi[$j] += $jumlah;
            $totalTab += $jumlah;

            echo "<td>";
            echo FormatRupiah($jumlah);
            echo "</td>";
        }
    }

    $totalAllTab += $totalTab;

    echo "<td>" . FormatRupiah($totalTab) . "</td>";
    echo "</tr>";
}
echo "<tr>";
echo "<td colspan='2'><strong>T O T A L</strong></td>";
for($i = 0; $i < count($lsLokasi); $i++)
{
    echo "<td><b>" . FormatRupiah($lsTotalLokasi[$i]) . "</b></td>";
}

echo "<td><strong>" . FormatRupiah($totalAllTab) . "</strong></td>";
echo "</tr>";

echo "</table>";
echo "</div>";
?>

</body>
</html>