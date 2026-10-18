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
    <title>Saldo Tabungan per Lokasi Dana</title>
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
    <script language="javascript" src="laporanlokasi.content.js?r=<?=filemtime('laporanlokasi.content.js')?>"></script>
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

for($i = 0; $i < count($lsLokasi); $i++)
{
    $kodeLokasi = $lsLokasi[$i][0];
    $nama = $lsLokasi[$i][1];

    echo "<td width='140'>$nama";
    if ($kodeLokasi != "***")
        echo "<br>$kodeLokasi";
    echo "</td>";
}
echo "<td width='140'>Saldo</td>";
echo "</tr>";

$totsaldo = 0;
$totalAllTab = 0;

$last_kelompok = "";
$ixData = 0;
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
        echo "<tr style='height: 30px'><td colspan='$colspan' style='background-color: #efefef; font-weight: bold'>$judul</td></tr>";
    }

    echo "<tr height='40'>";
    echo "<td align='center'>$no</td>";
    echo "<td align='left'>$nmTab</td>";

    $totalTab = 0;

    for($j = 0; $j < count($lsLokasi); $j++)
    {
        $ixData += 1;

        $kodeLokasi = $lsLokasi[$j][0];
        $namaLokasi = $lsLokasi[$j][1];

        if ($kodeLokasi == "***")
            $kodeLokasiValue = " IS NULL";
        else
            $kodeLokasiValue = " = '$kodeLokasi'";

        $sql = "SELECT IFNULL(GROUP_CONCAT(replid SEPARATOR ','), '') 
                  FROM $table
                 WHERE lokasidana $kodeLokasiValue
                   AND idtabungan = $idTab";
        $res = $db->QueryDb($sql);
        $row = mysqli_fetch_row($res);
        $stIdList = $row[0];

        if ($stIdList == "")
        {
            echo "<td align='center'>&nbsp;</td>";
        }
        else
        {
            $stIdList64 = base64_encode($stIdList);

            $sql = "SELECT SUM(kredit) - SUM(debet)
                      FROM $table
                     WHERE replid IN ($stIdList)";
            $res = $db->QueryDb($sql);
            $row = mysqli_fetch_row($res);
            $jumlah = $row[0];

            $lsTotalLokasi[$j] += $jumlah;
            $totalTab += $jumlah;

            echo "<td align='right'>";
            echo "<input type='hidden' id='stidlist64-$ixData' value='$stIdList64'>";
            echo "<input type='hidden' id='idtab-$ixData' value='$idTab'>";
            echo "<input type='hidden' id='nmtab-$ixData' value='$nmTab'>";
            echo "<input type='hidden' id='kodelokasi-$ixData' value='$kodeLokasi'>";
            echo "<input type='hidden' id='namalokasi-$ixData' value='$namaLokasi'>";
            echo "<input type='hidden' id='kelompok-$ixData' value='$kelompok'>";
            
            echo "<a style='color: blue; font-weight: normal;' href=\"JavaScript:showDetail($ixData)\">";
            echo FormatRupiah($jumlah);
            echo "</a>";

            if (getLevel() != 2)
                echo "&nbsp;<img src='../images/ico/ubah.png' class='hide-in-report' title='pindah lokasi dana' style='cursor: pointer;' onclick='pindahLokasiDana($ixData)'>";

            echo "</td>";
        }
    }

    $totalAllTab += $totalTab;

    echo "<td align='right' style='background-color:#DBF4C1'><strong>" . FormatRupiah($totalTab) . "</strong></td>";
    echo "</tr>";
}
echo "<tr style='height: 40px'>";
echo "<td align='center' colspan='2' bgcolor='#ededed'><strong>T O T A L</strong></td>";
for($i = 0; $i < count($lsLokasi); $i++)
{
    echo "<td width='140' align='right'><b>" . FormatRupiah($lsTotalLokasi[$i]) . "</b></td>";
}

echo "<td align='right' style='background-color:#DBF4C1'><strong>" . FormatRupiah($totalAllTab) . "</strong></td>";
echo "</tr>";

echo "</table>";
echo "</div>";
?>
</body>
</html>