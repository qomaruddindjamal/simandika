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

$db = new Db;
$db->TryOpenExit(true);

$departemen = $_REQUEST['departemen'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Saldo Tabungan Terakhir</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="laporantotal.content.js?r=<?=filemtime('laporantotal.content.js')?>"></script>
</head>
<body style="margin: 10px;">
<input type="hidden" id="departemen" value="<?= $departemen ?>">

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

echo "<table border='0' cellpadding='2' cellspacing='0' align='center'>";
echo "<tr>";
echo "<td align='left' valign='top'>";
echo "<a href='#' onClick='document.location.reload()'><img src='../images/ico/refresh.png' border='0' title='refresh' >&nbsp;refresh</a>&nbsp;&nbsp;";
echo "<a href='JavaScript:cetak()'><img src='../images/ico/print.png' border='0' title='cetak'>&nbsp;cetak</a>&nbsp;&nbsp;";
echo "<a href='JavaScript:excel()'><img src='../images/ico/excel.png' border='0' title='excel'>&nbsp;excel</a>";
echo "</td>";
echo "</tr>";
echo "</table>";

echo "<div id='dvRekap'>";
echo "<table cellpadding='5' border='1' class='tab' cellspacing='0' align='center'>";
echo "<tr style='height: 30px' align='center' class='header'>";
echo "<td width='30'>No</td>";
echo "<td width='200'>Tabungan</td>";
echo "<td width='140'>Jumlah Setoran</td>";
echo "<td width='140'>Jumlah Tarikan</td>";
echo "<td width='140'>Saldo Terakhir</td>";
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
             WHERE idtabungan = '$idTab'";
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
        echo "<tr style='height: 30px'><td colspan='5' style='background-color: #efefef; font-weight: bold'>$judul</td></tr>";
    }

    echo "<tr height='40'>";
    echo "<td align='center'>$no</td>";
    echo "<td align='left'>$nmTab</td>";
    echo "<td align='right' style='background-color:#E0F3FF'>";
    echo "<a style='color: blue; font-weight: normal; text-decoration: underline' href=\"JavaScript:showDetail('$idTab', '$nmTab', 'SETORAN', '$kelompok')\">";
    echo "<strong>" . FormatRupiah($jumsetor) . "</strong>";
    echo "</a>";
    echo "</td>";
    echo "<td align='right' style='background-color:#F2E9C6'>";
    echo "<a style='color: blue; font-weight: normal; text-decoration: underline' href=\"JavaScript:showDetail('$idTab', '$nmTab', 'TARIKAN', '$kelompok')\">";
    echo "<strong>" . FormatRupiah($jumtarik) ."</strong>";
    echo "</a>";
    echo "</td>";
    echo "<td align='right' style='background-color:#DBF4C1'><strong>" . FormatRupiah($jumsaldo) . "</strong></td>";
    echo "</tr>";
}
echo "<tr style='height: 40px'>";
echo "<td align='center' colspan='2' bgcolor='#ededed'><strong>T O T A L</strong></td>";
echo "<td align='right' style='background-color:#E0F3FF'><strong>" . FormatRupiah($totsetor) . "</strong></td>";
echo "<td align='right' style='background-color:#F2E9C6'><strong>" . FormatRupiah($tottarik) . "</strong></td>";
echo "<td align='right' style='background-color:#DBF4C1'><strong>" . FormatRupiah($totsaldo) . "</strong></td>";
echo "</tr>";
echo "</table>";
echo "</div>";
?>
</body>
</html>