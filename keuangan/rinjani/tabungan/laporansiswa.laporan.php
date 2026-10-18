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
require_once('../library/userinfo.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = $_REQUEST['departemen'];
$nis = $_REQUEST['userid'];
$nama = $_REQUEST['username'];
$tanggal1 = $_REQUEST['tanggal1'];
$tanggal2 = $_REQUEST['tanggal2'];
$datetime1 = "$tanggal1 00:00:00";
$datetime2 = "$tanggal2 23:59:59";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Tabungan Siswa</title>
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
    <script language="javascript" src="laporansiswa.laporan.js?r=<?=filemtime('laporansiswa.laporan.js')?>"></script>
</head>
<body style="margin: 10px;">
<input type="hidden" id="nis" value="<?= $nis ?>">
<input type="hidden" id="nama" value="<?= $nama ?>">
<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="tanggal1" value="<?= $tanggal1 ?>">
<input type="hidden" id="tanggal2" value="<?= $tanggal2 ?>">

<?php
$userInfo = UserInfo::Siswa($db, $nis);
if ($userInfo->Exist == false)
{
    echo "<i>Tidak ditemukan data siswa $nis /khnck</i>";
    exit();
}
?>

<div id="divSectionUser">
<table border="0" width="100%">
<tr>
    <td width="70%">
<?php   UserInfo::ShowSiswaAvatar($userInfo) ?>
    </td>
    <td width="30%" align="right" valign="bottom">
        <div id='dvMenu'>
            <a class='hide-in-report' href='JavaScript:refresh()'><img src='../images/ico/refresh.png' border='0'>&nbsp;refresh</a>&nbsp;&nbsp;&nbsp;
            <a class='hide-in-report' href='JavaScript:cetak()'><img src='../images/ico/print.png' border='0'>&nbsp;cetak</a>&nbsp;&nbsp;&nbsp;
            <a class='hide-in-report' href='JavaScript:excel()'><img src='../images/ico/excel.png' border='0'>&nbsp;excel</a>
        </div>
    </td>
</tr>
</table>
</div>
<br>

<?php
$sql = "SELECT DISTINCT t.idtabungan, dt.nama
          FROM jbsfina.tabungan t, jbsfina.datatabungan dt
         WHERE t.idtabungan = dt.replid
           AND t.nis = '$nis'
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

echo "<div id='dvSectionReport'>";
echo "<table class='tab' id='tablejtt' border='1' cellpadding='5' style='border-collapse:collapse' cellspacing='0'>";
echo "<tr style='height: 30px' align='center'>";
echo "<td class='header-bgonly' width='30' rowspan='2'>No</td>";
echo "<td class='header-bgonly' width='270' rowspan='2'>Tabungan</td>";
echo "<td class='header-bgonly' width='150' rowspan='2' align='center'>JS<br><span style='font-size: 9px; font-weight: normal'>Jumlah Setoran</span></td>";
echo "<td class='header-bgonly' width='150' rowspan='2' align='center'>SA<br><span style='font-size: 9px; font-weight: normal'>Setoran Akhir</span></td>";
echo "<td class='header-bgonly' width='150' rowspan='2' align='center'>JT<br><span style='font-size: 9px; font-weight: normal'>Jumlah Tarikan</span></td>";
echo "<td class='header-bgonly' width='150' rowspan='2' align='center'>TA<br><span style='font-size: 9px; font-weight: normal'>Tarikan Akhir</span></td>";
echo "<td class='header-bgonly' width='450' colspan='3' align='center'>Rekapitulasi</td>";
echo "</tr>";
echo "<tr >";
echo "<td class='header-bgonly' width='150' align='center'>TS<br><span style='font-size: 9px; font-weight: normal'>Total Setoran</span></td>";
echo "<td class='header-bgonly' width='150' align='center'>TT<br><span style='font-size: 9px; font-weight: normal'>Total Tarikan</span></td>";
echo "<td class='header-bgonly' width='150' align='center'>TSD<br><span style='font-size: 9px; font-weight: normal'>Saldo</span></td>";
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
              FROM jbsfina.tabungan
             WHERE idtabungan = '$idTab'
               AND nis = '$nis'";
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
              FROM jbsfina.tabungan
             WHERE idtabungan = '$idTab'
               AND nis = '$nis'
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
                 FROM jbsfina.tabungan
                WHERE idtabungan = '$idTab'
                  AND nis = '$nis'
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
              FROM jbsfina.tabungan
             WHERE idtabungan = '$idTab'
               AND nis = '$nis'
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
    echo "<td align='center' class='numberColumn'> $cnt </td>";
    echo "<td align='left'><b>$nmTab</b><br><a class='asmall hide-in-report' onclick='showRiwayatTabungan($idTab, \"$nmTab\")'>riwayat</a></td>";
    echo "<td align='right' style='background-color:#E0F3FF'><b>" . FormatRupiah($subsetor) . "</b></td>";
    echo "<td align='right' style='background-color:#E0F3FF'><b>" . FormatRupiah($lastsetor) . "</b><br><i>$tgllastsetor</i></td>";
    echo "<td align='right' style='background-color:#F2E9C6'><b>" . FormatRupiah($subtarik) . "</b></td>";
    echo "<td align='right' style='background-color:#F2E9C6'><b>" . FormatRupiah($lasttarik) . "</b><br><i>$tgllasttarik</i></td>";
    echo "<td align='right' style='background-color:#DBF4C1'><b>" . FormatRupiah($totsetor) . "</b></td>";
    echo "<td align='right' style='background-color:#DBF4C1'><b>" . FormatRupiah($tottarik) . "</b></td>";
    echo "<td align='right' style='background-color:#DBF4C1'><b>" . FormatRupiah($saldo) . "</b></td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";
?>

</body>
</html>