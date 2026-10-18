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
require_once('penerimaanlain.laporan.func.php');

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Rekap_Penerimaan_Lain.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idPenerimaan = RequestData("idpenerimaan", 0);
$namaPenerimaan = RequestData("namapenerimaan", "");
$tanggal1 = RequestData("tanggal1", date("Y-m-d"));
$tanggal2 = RequestData("tanggal2", date("Y-m-d"));
$page = RequestData("page", 1);
$urut = RequestData("urut", "p.tanggal DESC, p.replid DESC");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Penerimaan Lain</title>
</head>
<body style="margin: 10px;">

<?php
$sql = "SELECT replid 
          FROM jbsfina.tahunbuku 
         WHERE departemen = '$departemen' 
           AND aktif = 1";
$res = $db->QueryDb($sql);
if (mysqli_num_rows($res) == 0)
{
    $db->Close();

    echo "<span style='color: maroon; font-size: 13px;'>";
    echo "Belum ada Tahun buku yang Aktif di departemen $departemen. Silakan isi/aktifkan Tahun Buku di menu Referensi";
    echo "</span>";

    exit();
}
$row = mysqli_fetch_row($res);
$idTahunBuku = $row[0];
echo "<input type='hidden' id='idtahunbuku' value='$idTahunBuku'>";

$sql_tot = "SELECT SUM(p.jumlah), COUNT(p.replid)  
	          FROM jbsfina.penerimaanlain p, jbsfina.jurnal j, jbsfina.datapenerimaan dp 
			 WHERE j.replid = p.idjurnal 
			   AND j.idtahunbuku = '$idTahunBuku' 
			   AND p.idpenerimaan = dp.replid 
			   AND p.idpenerimaan = '$idPenerimaan' 
			   AND dp.departemen = '$departemen' 
			   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'";


$res = $db->QueryDb($sql_tot);
$row = mysqli_fetch_row($res);
$totalJumlah = $row[0];
$nData = $row[1];

if ($nData == 0)
{
    $db->Close();

    echo "<span style='color: maroon; font-size: 13px;'>";
    echo "Belum ada data penerimaan $namaPenerimaan";
    echo "</span>";

    exit();
}
?>

<center><font size="4" face="Arial"><strong>PENERIMAAN LAIN</strong></font><br /></center>
<br />


<table>
<tr>
    <td>Total <?= $namaPenerimaan ?></td>
    <td><?= FormatRupiah($totalJumlah) ?></td>
</tr>
</table>
<br><br>

<table>
<tr>
    <td>No</td>
    <td>No. Jurnal/Tanggal</td>
    <td>Sumber</td>
    <td>Jumlah</td>
    <td>Keterangan</td>
    <td>Petugas</td>
</tr>
<?php
    $sql = "SELECT p.replid AS id, j.nokas, p.sumber, date_format(p.tanggal, '%d-%b-%Y') AS tanggal, p.keterangan, p.jumlah, p.petugas 
              FROM penerimaanlain p, jurnal j, datapenerimaan dp 
             WHERE j.replid = p.idjurnal 
               AND j.idtahunbuku = '$idTahunBuku'
               AND p.idpenerimaan = dp.replid 
               AND p.idpenerimaan = '$idPenerimaan' 
               AND dp.departemen = '$departemen' 
               AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
             ORDER BY $urut";

    $cnt = 0;
    $res = $db->QueryDb($sql);
    while ($row = mysqli_fetch_array($res))
    {
        ?>
        <tr>
            <td><?=++$cnt?></td>
            <td><?="<strong>" . $row['nokas'] . "</strong><br>" . $row['tanggal']?></td>
            <td><?=$row['sumber'] ?></td>
            <td><?=FormatRupiah($row['jumlah'])?></td>
            <td><?=$row['keterangan'] ?></td>
            <td><?=$row['petugas'] ?></td>
        </tr>
<?php
    }
    ?>
</table>

</body>
</html>