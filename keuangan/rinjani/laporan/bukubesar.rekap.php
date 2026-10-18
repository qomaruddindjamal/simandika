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
require_once('../library/rupiah.php');
require_once('../library/msg.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('common.func.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = $_REQUEST["departemen"];
$idTahunBuku = $_REQUEST["idtahunbuku"];
$namaTahunBuku = $_REQUEST["namatahunbuku"];
$tanggal1 = $_REQUEST["tanggal1"];
$tanggal2 = $_REQUEST["tanggal2"];
$kategori = $_REQUEST["kategori"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Audit Perubahan Data</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/stringutil.js"></script>
    <script language="javascript" src="../script/dateutil.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="bukubesar.rekap.js?r=<?=filemtime('bukubesar.rekap.js')?>"></script>
</head>
<body style="margin: 0">
<input type="hidden" id="departemen" value="<?=$departemen?>">
<input type="hidden" id="idtahunbuku" value="<?=$idTahunBuku?>">
<input type="hidden" id="namatahunbuku" value="<?=$namaTahunBuku?>">
<input type="hidden" id="tanggal1" value="<?=$tanggal1?>">
<input type="hidden" id="tanggal2" value="<?=$tanggal2?>">
<input type="hidden" id="kategori" value="<?=$kategori?>">

<?php

$sql = "SELECT r.nama, r.kode, sum(jd.debet), sum(jd.kredit) 
          FROM jbsfina.jurnal j, jbsfina.jurnaldetail jd, jbsfina.rekakun r 
         WHERE j.replid = jd.idjurnal 
           AND j.idtahunbuku = '$idTahunBuku' 
           AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
           AND jd.koderek = r.kode";
if ($kategori != "ALL")
    $sql .= " AND r.kategori = '$kategori'";
$sql .= " GROUP BY r.nama, r.kode ORDER BY r.kode";

$res = $db->QueryDb($sql);
if (mysqli_num_rows($res) == 0)
{
    echo "<span style='color: maroon'>belum ada data laporan buku besar</span>";
    exit();
}

echo "<table border='0' cellpadding='0' cellspacing='0' width='95%' align='center'>";
echo "<tr>";
echo "<td align='right'>";
echo "<a href='#' onClick='document.location.reload()'><img src='../images/ico/refresh.png' border='0'>&nbsp;refresh</a>&nbsp;&nbsp;";
echo "<a href='JavaScript:cetak()'><img src='../images/ico/print.png' border='0'>&nbsp;cetak</a>";
echo "</td>";
echo "</tr>";
echo "</table>";

echo "<div id='dvRekap'>";
echo "<table class='tab' border='1' cellpadding='2' style='border-collapse:collapse' cellspacing='0' width='95%' align='center'>";
echo "<tr>";
echo "<td class='header-sm' width='5%' align='center'>No</td>";
echo "<td class='header-sm' width='*' align='center'>Rekening</td>";
echo "<td class='header-sm' width='25%' align='center'>Debet</td>";
echo "<td class='header-sm' width='25%' align='center'>Kredit</td>";
echo "</tr>";
echo "</table>";

$cnt = 0;
$totaldebet = 0;
$totalkredit = 0;
echo "<table class='tab' id='table' border='1' cellpadding='2' style='border-collapse:collapse' cellspacing='0' width='95%' align='center'>";
while($row = mysqli_fetch_row($res))
{
    $totaldebet += $row[2];
    $totalkredit += $row[3];

    $cnt += 1;
    echo "<tr onclick='show_detail(\"$row[1]\", \"$row[0]\")' style='cursor:pointer'>";
    echo "<td align='center' rowspan='2' class='numberColumn' width='5%'>$cnt</td>";
    echo "<td align='left' colspan='2' width='95%'><b>$row[1] $row[0]</b></td>";
    echo "</tr>";
    echo "<tr onclick='show_detail(\"$row[1]\", \"$row[0]\")' style='cursor:pointer'>";
    echo "<td align='right' width='50%'>" . FormatRupiah($row[2]) . "</td>";
    echo "<td align='right' width='50%'>" . FormatRupiah($row[3]) . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "</div>";
?>

</body>
</html>