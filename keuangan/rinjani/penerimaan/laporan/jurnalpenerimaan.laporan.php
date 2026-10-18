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
require_once('../../include/sessioninfo.php');
require_once('../../include/sessionchecker.php');
require_once('../../library/common.func.php');
require_once('../../include/config.php');
require_once('../../include/db.onfunc.php');
require_once('../../library/departemen.php');
require_once('../../library/msg.php');
require_once('../../library/rupiah.php');
require_once('../../library/userinfo.php');
require_once('../../util/peek.php');
require_once('../../include/errorhandler.php');
require_once('jurnalpenerimaan.laporan.func.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idKategori = RequestData("idkategori", 0);
$namaKategori = RequestData("namakategori", "");
$idTahunBuku = RequestData("idtahunbuku", 0);
$namaTahunBuku = RequestData("namatahunbuku", "");
$tanggal1 = RequestData("tanggal1", date('Y-m-d'));
$tanggal2 = RequestData("tanggal2", date('Y-m-d'));
$page = RequestData("page", 1);
$urut = RequestData("urut", "tanggal DESC, replid DESC");

$nRowPerPage = 10;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Jurnal Penerimaan</title>
    <link rel="stylesheet" type="text/css" href="../../style/style.css?<?=filemtime('../../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../../script/tables.js"></script>
    <script language="javascript" src="../../script/tools.js"></script>
    <script language="javascript" src="../../script/toast.js"></script>
    <script language="javascript" src="../../script/vldr.js"></script>
    <script language="javascript" src="../../script/dialogbox.js"></script>
    <script language="javascript" src="../../script/qsbuilder.js"></script>
    <script language="javascript" src="jurnalpenerimaan.laporan.js?r=<?=filemtime('jurnalpenerimaan.laporan.js')?>"></script>
</head>
<body style="margin: 10px;">

<?php
switch($idKategori)
{
    case "JTT":
        $sumber = "AND sumber = 'penerimaanjtt'";
        $kriteria = 5;
        break;
    case "SKR":
        $sumber = "AND sumber = 'penerimaaniuran'";
        $kriteria = 6;
        break;
    case "CSWJB":
        $sumber = "AND sumber = 'penerimaanjttcalon'";
        $kriteria = 7;
        break;
    case "CSSKR":
        $sumber = "AND sumber = 'penerimaaniurancalon'";
        $kriteria = 8;
        break;
    case "LNN":
        $sumber = "AND sumber = 'penerimaanlain'";
        $kriteria = 9;
        break;
    default:
        $sumber = "AND sumber LIKE 'penerimaan%'";
        $kriteria = 10;
}

$sql_tot = "SELECT COUNT(replid) 
              FROM jbsfina.jurnal 
             WHERE idtahunbuku = '$idTahunBuku' 
                   $sumber 
               AND tanggal BETWEEN '$tanggal1' AND '$tanggal2'";
$res = $db->QueryDb($sql_tot);
$row = mysqli_fetch_row($res);
$nData = $row[0];

if ($nData == 0)
{
    $db->Close();

    echo "<span style='color: maroon; font-size: 13px;'>";
    echo "Belum ada data jurnal untuk $namaKategori di departemen $departemen antara tanggal " . LongDateFormat($tanggal1) . " s/d " . LongDateFormat($tanggal2);
    echo "</span>";

    exit();
}

$startFromIndex = ($page - 1) * $nRowPerPage;
$nPage = ceil($nData / $nRowPerPage);

$sql = "SELECT * 
          FROM jbsfina.jurnal 
         WHERE idtahunbuku = '$idTahunBuku' 
               $sumber 
           AND tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
         ORDER BY $urut 
         LIMIT $startFromIndex, $nRowPerPage";

echo "<table width='100%' border='0' align='center'>";
echo "<tr>";
echo "<td valign='bottom'>";
echo "<a href='JavaScript:refresh()'><img src='../../images/ico/refresh.png' border='0'>&nbsp;refresh</a>&nbsp;";
echo "<a href='JavaScript:cetak()'><img src='../../images/ico/print.png' border='0'>&nbsp;cetak</a>&nbsp;";
echo "</td>";
echo "</tr>";
echo "</table><br>";


echo "<div id='dvContent'>";
echo "<table border='1' id='tabContent' style='border-collapse:collapse' cellpadding='5' cellspacing='0' width='95%' class='tab' bordercolor='#000000'>";
echo "<tr height='30' align='center'>";
echo "<td width='4%' class='header'>No</td>";
echo "<td width='15%' class='header'>No. Jurnal/Tanggal</td>";
echo "<td width='35%' class='header'>Transaksi</td>";
echo "<td class='header'>Detail Jurnal</td>";
echo "</tr>";

$cnt = 1 + $nData - ($page - 1) * $nRowPerPage;
$res = $db->QueryDb($sql);
while($row = mysqli_fetch_array($res))
{
    $cnt = $cnt - 1;
    $idjurnal = $row['replid'];
    $jurnal = NamaJurnal($row['sumber']);

    echo "<tr height='25'>";
    echo "<td align='center' rowspan='2' class='numberColumn'><strong>$cnt</strong></td>";
    echo "<td colspan='2'><span style='font-size: 12px; color: #2455aa; font-weight: bold'>$row[transaksi]</span></td>";
    echo "<td valign='top' style='background-color: #f7f7f7' rowspan='2'>";

    $sql = "SELECT jd.koderek, ra.nama, jd.debet, jd.kredit 
              FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
             WHERE jd.idjurnal = '$idjurnal' 
               AND jd.koderek = ra.kode 
             ORDER BY jd.replid";
    $res2 = $db->QueryDb($sql);

    echo "<table border='1' style='border-collapse: collapse' width='100%' cellpadding='2' bgcolor='#FFFFFF'>";
    while ($row2 = mysqli_fetch_array($res2))
    {
        echo "<tr height='25'>";
        echo "<td width='8%' align='center'>$row2[koderek]</td>";
        echo "<td width='*' align='left'>$row2[nama]</td>";
        echo "<td width='23%' align='right'>" . FormatRupiah($row2['debet']) . "</td>";
        echo "<td width='23%' align='right'>" . FormatRupiah($row2['kredit']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</td>";
    echo "</tr>";

    echo "<tr height='25'>";
    echo "<td align='center' valign='top'><strong>$row[nokas]</strong><br><i>" . LongDateFormat($row['tanggal']) . "<br>$row[jam]</i></td>";
    echo "<td align='left' valign='top'>";
    echo "<strong>Petugas: </strong>$row[petugas]<br>";
    echo "<strong>Sumber: </strong>$jurnal<br>";
    if (strlen($row['keterangan']) > 0)
        echo "<strong>Keterangan: </strong>$row[keterangan]";
    echo "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";

echo "halaman&nbsp;&nbsp;";
echo "<input type='button' class='but' style='width: 30px; height: 25px'  value=' < ' onclick='prevPage()'>&nbsp;&nbsp;";
echo "<select id='page' class='inputbox' style='width: 80px' onchange='changePage()'>";
for($i = 1; $i <= $nPage; $i++)
{
    $sel = $page == $i ? "selected" : "";
    echo "<option value='$i' $sel>$i</option>";
}
echo "</select>&nbsp;&nbsp;";
echo "<input type='button' class='but' style='width: 30px; height: 25px'  value=' > ' onclick='nextPage()'>";
echo " dari $nPage, jumlah $nData data";
?>

<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idkategori" value="<?= $idKategori ?>">
<input type="hidden" id="namakategori" value="<?= $namaKategori ?>">
<input type="hidden" id="idtahunbuku" value="<?= $idTahunBuku ?>">
<input type="hidden" id="namatahunbuku" value="<?= $namaTahunBuku ?>">
<input type="hidden" id="tanggal1" value="<?= $tanggal1 ?>">
<input type="hidden" id="tanggal2" value="<?= $tanggal2 ?>">
<input type="hidden" id="urut" value="<?= $urut ?>">
<input type="hidden" id="totalpage" value="<?= $nPage ?>">

</body>
</html>