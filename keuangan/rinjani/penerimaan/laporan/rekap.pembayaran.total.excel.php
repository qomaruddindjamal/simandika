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

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Rekap_Penerimaan_Total.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idKategori = RequestData("idkategori", 0);
$namaKategori = RequestData("namakategori", "");
$tanggal1 = RequestData("tanggal1", "");
$tanggal2 = RequestData("tanggal2", "");
$idLaporan = RequestData("idlaporan", 0);
$namaLaporan = RequestData("namalaporan", "");
$idPetugas = RequestData("idpetugas", 0);
$namaPetugas = RequestData("namapetugas", "");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekapitulasi Pembayaran</title>
</head>
<body style="margin: 10px;">

<center><font size="4" face="Arial"><strong>REKAPITULASI PENERIMAAN TOTAL</strong></font><br /></center>
<br />
<table border="0">
<tr>
    <td>Departemen:</td>
    <td><?=$departemen?></td>
</tr>
<tr>
    <td>Kategori:</td>
    <td><?=$namaKategori?></td>
</tr>
<tr>
    <td>Tanggal:</td>
    <td><?= LongDateFormat($tanggal1) . " s/d " . LongDateFormat($tanggal2) ?></td>
</tr>
<tr>
    <td>Laporan:</td>
    <td><?=$namaLaporan?></td>
</tr>
<tr>
    <td>Petugas:</td>
    <td><?=$namaPetugas?></td>
</tr>
</table>
<br>
<?php
if ($departemen == "ALL")
{
    $sql = "SELECT departemen 
              FROM jbsakad.departemen 
             ORDER BY urutan";
    $dres = $db->QueryDb($sql);
    $k = 0;
    while ($drow = mysqli_fetch_row($dres))
        $darray[$k++] = $drow[0];
}
else
{
    $darray = array( $departemen );
}

if ($idPetugas == "ALL")
    $sql_idpetugas = "";
elseif ($idPetugas == "landlord")
    $sql_idpetugas = " AND j.idpetugas IS NULL ";
else
    $sql_idpetugas = " AND j.idpetugas = '$idPetugas' ";

$total = 0;
echo "<table id='table' cellpadding='5' border='1' class='tab' cellspacing='0'>";
for($k = 0; $k < count($darray); $k++)
{
    $dept = $darray[$k];
    $cnt = 0;

    $sql = "SELECT replid 
              FROM jbsfina.tahunbuku 
             WHERE departemen = '$dept' 
               AND aktif = 1";
    $res = $db->QueryDb($sql);
    $ntb = mysqli_num_rows($res);
    if ($ntb == 0)
        continue;

    $row = mysqli_fetch_row($res);
    $idtahunbuku = $row[0];

    $subtotal = 0;
    $rarray = array();
    $sql = "SELECT replid, nama 
              FROM jbsfina.datapenerimaan 
             WHERE departemen = '$dept' 
               AND aktif = 1 
               AND idkategori = '$idKategori'";
    $pres = $db->QueryDb($sql);
    while($prow = mysqli_fetch_row($pres))
    {
        $idp = $prow[0];
        $pen = $prow[1];

        if ($idKategori == "JTT")
        {
            $sql = "SELECT SUM(p.jumlah), SUM(p.info1) 
			          FROM jbsfina.penerimaanjtt p, jbsfina.besarjtt b, jbsfina.datapenerimaan dp, jbsfina.jurnal j 
			         WHERE p.idbesarjtt = b.replid
					   AND b.idpenerimaan = dp.replid 
					   AND p.idjurnal = j.replid 
					   AND j.idtahunbuku = '$idtahunbuku'
					   AND dp.replid = '$idp'
					   AND dp.departemen = '$dept'
					   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'
					   $sql_idpetugas";
        }
        elseif ($idKategori == "SKR")
        {
            $sql = "SELECT SUM(p.jumlah), 0 
			          FROM jbsfina.penerimaaniuran p, jbsfina.datapenerimaan dp, jbsfina.jurnal j
			         WHERE p.idpenerimaan = dp.replid
					   AND p.idjurnal = j.replid
					   AND j.idtahunbuku = '$idtahunbuku' 
					   AND dp.replid = '$idp'
					   AND dp.departemen='$dept'
					   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'
					   $sql_idpetugas";
        }
        elseif ($idKategori == "CSWJB")
        {
            $sql = "SELECT SUM(p.jumlah), SUM(p.info1)
			          FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsfina.datapenerimaan dp, jbsfina.jurnal j 
			         WHERE p.idbesarjttcalon = b.replid
					   AND b.idpenerimaan = dp.replid
					   AND p.idjurnal = j.replid
					   AND j.idtahunbuku = '$idtahunbuku'
					   AND dp.replid = '$idp'
					   AND dp.departemen = '$dept'
					   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'
					   $sql_idpetugas";
        }
        elseif ($idKategori == "CSSKR")
        {
            $sql = "SELECT SUM(p.jumlah), 0 
			          FROM jbsfina.penerimaaniurancalon p, jbsfina.datapenerimaan dp, jbsfina.jurnal j
			         WHERE p.idjurnal = j.replid
					   AND j.idtahunbuku = '$idtahunbuku'
					   AND p.idpenerimaan = dp.replid
					   AND dp.replid = '$idp' 
					   AND dp.departemen='$dept'
					   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'
					   $sql_idpetugas";
        }
        elseif ($idKategori == "LNN")
        {
            $sql = "SELECT SUM(p.jumlah), 0 
			          FROM jbsfina.penerimaanlain p, jbsfina.datapenerimaan dp , jbsfina.jurnal j
			         WHERE p.idjurnal = j.replid
					   AND j.idtahunbuku = '$idtahunbuku'
					   AND p.idpenerimaan = dp.replid
					   AND dp.replid = '$idp' 
					   AND dp.departemen='$dept'
					   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'
					   $sql_idpetugas";
        }

        $jres = $db->QueryDb($sql);
        $jrow = mysqli_fetch_row($jres);
        $jumlah = 0;
        if (!is_null($jrow[0]))
            $jumlah = $jrow[0];

        $subtotal = $subtotal + $jumlah;
        $rarray[$cnt][0] = $pen;
        $rarray[$cnt][1] = $jumlah;
        $rarray[$cnt][2] = $idp;

        $cnt++;
    }

    $total = $total + $subtotal;

    for($i = 0; $i < $cnt; $i++)
    {
        $pen = $rarray[$i][0];
        $jumlah = $rarray[$i][1];
        $idpen = $rarray[$i][2];

        if ($i == 0)
        {
            echo "<tr>";
            echo "<td>";
            echo $dept;
            echo "</td>";
            echo "</tr>";
        }

        $no = $i + 1;
        echo "<tr>";
        echo "<td>$no</td>";
        echo "<td>$pen</td>";
        if ($jumlah == 0)
        {
            echo "<td>" . FormatRupiah($jumlah) . "</td>";
        }
        else
        {
            echo "<td>";
            echo FormatRupiah($jumlah);
            echo "</td>";
        }

        if ($i == 0)
        {
            echo "<td rowspan='$cnt'>" . FormatRupiah($subtotal) . "</td>";
        }
        echo "</tr>";
    }
}
echo "<tr>";
echo "<td colspan='3'>";
echo "T O T A L";
echo "</td>";
echo "<td>";
echo FormatRupiah($total);
echo "</td>";
echo "</tr>";
echo "</table>";
?>
</body>
</html>