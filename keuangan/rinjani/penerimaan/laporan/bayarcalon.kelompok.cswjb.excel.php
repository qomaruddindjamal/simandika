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
require_once('../../library/msg.php');
require_once('../../library/rupiah.php');
require_once('../../util/peek.php');
require_once('../../include/errorhandler.php');
//require_once('bayarsiswa.kelas.jtt.func.php');

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Iuran_Wajib_Calon_Siswa_per_Kelompok.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idProses = RequestData("idproses", 0);
$namaProses = RequestData("namaproses", "");
$idKelompok = RequestData("idkelompok", 0);
$namaKelompok = RequestData("namakelompok", "");
$idKategori = RequestData("idkategori", 0);
$namaKategori = RequestData("namakategori", "");
$idPenerimaan = RequestData("idpenerimaan", 0);
$namaPenerimaan = RequestData("namapenerimaan", "");
$status = RequestData("status", -1);
$namaStatus = RequestData("namastatus", -1);
$urut = RequestData("urut", "s.nis");
$page = RequestData("page", 1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Laporan Pembayaran Calon Siswa Per Kelompok</title>
</head>
<body style="margin: 10px;">

<?php
$sql = "SELECT replid 
          FROM jbsfina.tahunbuku 
         WHERE departemen = '$departemen' 
           AND aktif = 1";
$res = $db->QueryDb($sql);
$row = mysqli_fetch_row($res);
$idTahunBuku = $row[0];

if ($status == -1)
{
    if ($idKelompok == -1)
    {
        // semua kelompok di proses terpilih
        $sql = "SELECT MAX(jumlah), COUNT(replid) 
		          FROM ((SELECT c.replid, count(p.replid) as jumlah 
                           FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsakad.calonsiswa c 
                          WHERE p.idbesarjttcalon = b.replid 
                            AND b.idcalon = c.replid 
                            AND b.info2 = '$idTahunBuku'
						    AND b.idpenerimaan = '$idPenerimaan' 
						  GROUP BY c.replid) AS x)";
    }
    else
    {
        // proses & kelompok terpilih
        $sql = "SELECT MAX(jumlah), COUNT(replid) 
		          FROM ((SELECT c.replid, count(p.replid) as jumlah 
                           FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsakad.calonsiswa c 
						  WHERE p.idbesarjttcalon = b.replid 
						    AND b.idcalon = c.replid 
						    AND b.info2 = '$idTahunBuku'
                            AND c.idkelompok = '$idKelompok' 
                            AND b.idpenerimaan = '$idPenerimaan' 
                          GROUP BY c.replid) AS x)";
    }
}
else
{
    if ($idKelompok == -1)
    {
        // semua kelas di tingkat terpilih
        $sql = "SELECT MAX(jumlah), COUNT(replid)
		          FROM ((SELECT c.replid, count(p.replid) as jumlah 
					       FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsakad.calonsiswa c 
						  WHERE p.idbesarjttcalon = b.replid 
						    AND b.idcalon = c.replid 
						    AND b.info2 = '$idTahunBuku' 
							AND b.idpenerimaan = '$idPenerimaan' 
							AND b.lunas = '$status' 
					      GROUP BY c.replid) AS x);";
    }
    else
    {
        // tingkat & kelas terpilih
        $sql = "SELECT MAX(jumlah), COUNT(replid) 
		          FROM ((SELECT c.replid, count(p.replid) as jumlah 
                           FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsakad.calonsiswa c 
					  	  WHERE p.idbesarjttcalon = b.replid 
					  	    AND b.idcalon = c.replid 
					  	    AND b.info2 = '$idTahunBuku' 
							AND c.idkelompok = '$idKelompok' 
							AND b.idpenerimaan = '$idPenerimaan' 
							AND b.lunas = '$status' 
						  GROUP BY c.replid) AS x);";
    }
}
// -- calculate table width ----------------------------------------
$row = $db->FetchSingleRow($sql);
$max_n_cicilan = $row[0];
$ndata = $row[1];

if ($max_n_cicilan == 0 && $ndata == 0)
{
    echo "<span style='color: maroon; font-size: 13px;'>";
    echo "Belum data pembayaran di kelompok terpilih";
    echo "</span>";
    exit();
}


if ($status == -1)
{
    if ($idKelompok == -1)
    {
        // semua kelompok di proses terpilih
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBiaya
						    FROM jbsfina.besarjttcalon b, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k 
						   WHERE c.replid = b.idcalon 
						     AND b.idpenerimaan = '$idPenerimaan' 
						     AND b.info2 = '$idTahunBuku' 
						     AND c.idkelompok = k.replid";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon 
								   FROM jbsfina.penerimaanjttcalon p 
								  RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k 
								  WHERE c.replid = b.idcalon 
								    AND b.idpenerimaan = '$idPenerimaan' 
								    AND b.info2 = '$idTahunBuku' 
								    AND c.idkelompok = k.replid";

        $sql = "SELECT DISTINCT c.nopendaftaran, c.nama, k.kelompok, b.replid AS id, b.besar, b.keterangan, b.lunas 
		          FROM jbsfina.penerimaanjttcalon p RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
				 WHERE c.replid = b.idcalon 
				   AND b.idpenerimaan = '$idPenerimaan' 
				   AND b.info2 = '$idTahunBuku'
				   AND c.idkelompok = k.replid 
				 ORDER BY $urut ASC";
    }
    else
    {
        // proses & kelompok terpilih
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBiaya
						    FROM jbsfina.besarjttcalon b, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
						   WHERE c.replid = b.idcalon 
						     AND b.idpenerimaan = '$idPenerimaan' 
						     AND b.info2 = '$idTahunBuku'
							 AND c.idkelompok = '$idKelompok' 
							 AND c.idkelompok = k.replid";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon  
								   FROM jbsfina.penerimaanjttcalon p 
								  RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
								  WHERE c.replid = b.idcalon 
								    AND b.idpenerimaan = '$idPenerimaan' 
								    AND b.info2 = '$idTahunBuku'
								    AND c.idkelompok = '$idKelompok' 
								    AND c.idkelompok = k.replid";

        $sql = "SELECT DISTINCT c.nopendaftaran, c.nama, k.kelompok, b.replid AS id, b.besar, b.keterangan, b.lunas 
		          FROM jbsfina.penerimaanjttcalon p 
		         RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
				 WHERE c.replid = b.idcalon 
				   AND b.idpenerimaan = '$idPenerimaan' 
				   AND b.info2 = '$idTahunBuku' 
				   AND c.idkelompok = '$idKelompok' 
				   AND c.idkelompok = k.replid 
				 ORDER BY $urut ASC";
    }
}
else
{
    if ($idKelompok == -1)
    {
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBiaya  
							FROM jbsfina.besarjttcalon b, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
						   WHERE c.replid = b.idcalon 
							 AND b.idpenerimaan = '$idPenerimaan' 
							 AND b.info2 = '$idTahunBuku'
							 AND c.idkelompok = k.replid 
							 AND b.lunas = '$status'";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon  
								   FROM jbsfina.penerimaanjttcalon p 
								  RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
								  WHERE c.replid = b.idcalon 
								    AND b.idpenerimaan = '$idPenerimaan' 
								    AND b.info2 = '$idTahunBuku'
									AND c.idkelompok = k.replid 
									AND b.lunas = '$status'";

        $sql = "SELECT DISTINCT c.nopendaftaran, c.nama, k.kelompok, b.replid AS id, b.besar, b.keterangan, b.lunas 
				  FROM jbsfina.penerimaanjttcalon p RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
				 WHERE c.replid = b.idcalon 
				   AND b.idpenerimaan = '$idPenerimaan' 
				   AND b.info2 = '$idTahunBuku' 
				   AND c.idkelompok = k.replid 
				   AND b.lunas = '$status' 
				 ORDER BY $urut ASC";
    }
    else
    {
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBayar  
							FROM jbsfina.besarjttcalon b, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
						   WHERE c.replid = b.idcalon 
						     AND b.idpenerimaan = '$idPenerimaan' 
						     AND b.info2 = '$idTahunBuku'
							 AND c.idkelompok = '$idKelompok' 
							 AND c.idkelompok = k.replid AND b.lunas = '$status'";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon
								   FROM jbsfina.penerimaanjttcalon p 
								  RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
								  WHERE c.replid = b.idcalon 
								    AND b.idpenerimaan = '$idPenerimaan' 
								    AND b.info2 = '$idTahunBuku'
								    AND c.idkelompok = '$idKelompok' 
								    AND c.idkelompok = k.replid 
								    AND b.lunas = '$status'";

        $sql = "SELECT DISTINCT c.nopendaftaran, c.nama, k.kelompok, b.replid AS id, b.besar, b.keterangan, b.lunas 
		          FROM jbsfina.penerimaanjttcalon p RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
				 WHERE c.replid = b.idcalon 
				   AND b.idpenerimaan = '$idPenerimaan' 
				   AND b.info2 = '$idTahunBuku'
				   AND c.idkelompok = '$idKelompok' 
				   AND c.idkelompok = k.replid 
				   AND b.lunas = '$status' 
				 ORDER BY $urut ASC";
    }
}

$totalBiayaAll = $db->FetchSingle($sql_sum_biaya, 0);
$row = $db->FetchSingleRow($sql_sum_bayar_diskon);
$totalBayarAll = $row[0] + $row[1];
$totalDiskonAll = $row[1];
?>
<center><font size="4" face="Arial"><strong>LAPORAN PEMBAYARAN IURAN WAJIB CALON SISWA</strong></font><br /></center>
<table border="0">
<tr>
    <td>Departemen:</td>
    <td><?=$departemen?></td>
</tr>
<tr>
    <td>Proses:</td>
    <td><?=$namaProses?></td>
</tr>
<tr>
    <td>Kelompok:</td>
    <td><?=$namaKelompok?></td>
</tr>
<tr>
    <td>Kategori:</td>
    <td><?=$namaKategori?></td>
</tr>
<tr>
    <td>Penerimaan:</td>
    <td><?=$namaPenerimaan?></td>
</tr>
<tr>
    <td>Status:</td>
    <td><?=$namaStatus?></td>
</tr>
</table>
<br><br>

<table border="0" cellpadding="0" cellspacing="2">
<tr>
    <td>Total <?= $namaPenerimaan ?></td>
    <td><?= FormatRupiah($totalBiayaAll) ?></td>
</tr>
<tr>
    <td>Total Pembayaran</td>
    <td><?= FormatRupiah($totalBayarAll) ?></td>
</tr>
<tr>
    <td>Total Diskon</span></td>
    <td><?= FormatRupiah($totalDiskonAll) ?></td>
</tr>
<tr>
    <td>Total Tunggakan</td>
    <td><?= FormatRupiah($totalBiayaAll - $totalBayarAll) ?></td>
</tr>
</table>
<br><br>

<table class="tab" id="table" border="1" cellpadding="5" style="border-collapse:collapse" cellspacing="0" width="<?=$table_width?>">
<tr>
    <td>No</td>
    <td>No Calon</td>
    <td>Nama</td>
    <td>Kelompok</td>
<?php
    for($i = 0; $i < $max_n_cicilan; $i++)
    {
        $n = $i + 1;
        echo "<td colspan='2'>Bayaran-$n</td>";
    } ?>
    <td>Status</td>
    <td><?=$namaPenerimaan ?></td>
    <td>Sub Total Pembayaran</td>
    <td>Sub Total Diskon</td>
    <td>Sub Total Tunggakan</td>
    <td>Keterangan</td>
</tr>
<?php
    $cnt = $startFromIndex;
    $result = $db->QueryDb($sql);
    while ($row = mysqli_fetch_array($result))
    {
        $idbesarjtt = $row['id'];
        $besarjtt = $row['besar'];
        $ketjtt = $row['keterangan'];
        $lunasjtt = $row['lunas'];

        if ($lunasjtt == 1)
            $infojtt = "<font color=blue><strong>Lunas</strong></font>";
        elseif ($lunasjtt == 2)
            $infojtt = "<font color=green><strong>Gratis</strong></font>";
        else
            $infojtt = "<font color=red><strong>Belum Lunas</strong></font>"; ?>

        <tr>
            <td><?=++$cnt ?></td>
            <td><?=$row['nopendaftaran'] ?></td>
            <td><?=$row['nama'] ?></td>
            <td>
                <?php if ($idKelompok == -1) echo $namaProses . " - "; ?>
                <?=$row['kelompok'] ?>
            </td>
<?php
            $sql = "SELECT COUNT(p.replid)
                      FROM jbsfina.penerimaanjttcalon p, jbsfina.jurnal j 
                     WHERE p.idjurnal = j.replid 
                       AND j.idtahunbuku = $idTahunBuku 
                       AND p.idbesarjttcalon = $idbesarjtt";
            $nbayar = $db->FetchSingle($sql, 0);
            $nblank = $max_n_cicilan - $nbayar;

            $totalbayar = 0;
            $totaldiskon = 0;

            if ($nbayar > 0)
            {
                $sql = "SELECT DATE_FORMAT(p.tanggal, '%d-%b-%y'), p.jumlah, p.info1 
                          FROM jbsfina.penerimaanjttcalon p, jbsfina.jurnal j 
                         WHERE p.idjurnal = j.replid 
                           AND j.idtahunbuku = $idTahunBuku 
                           AND p.idbesarjttcalon = $idbesarjtt 
                         ORDER BY p.tanggal";
                $result2 = $db->QueryDb($sql);
                while ($row2 = mysqli_fetch_row($result2))
                {
                    $totalbayar = $totalbayar + $row2[1] + $row2[2];
                    $totaldiskon = $totaldiskon + $row2[2];	?>
                    <td><?=FormatRupiah($row2[1]) ?></td>
                    <td><?=FormatRupiah($row2[0]) ?></td>
                    <?php
                }

                $totalBayarAll += $totalbayar;
                $totalDiskonAll += $totaldiskon;
            }

            for ($i = 0; $i < $nblank; $i++)
            {
                echo "<td>&nbsp;</td>";
                echo "<td>&nbsp;</td>";
            } ?>
            <td><?=$infojtt ?></td>
            <td><?=FormatRupiah($besarjtt) ?></td>
            <td><?=FormatRupiah($totalbayar) ?></td>
            <td><?=FormatRupiah($totaldiskon) ?></td>
            <td><?=FormatRupiah($besarjtt - $totalbayar) ?></td>
            <td><?=$ketjtt ?></td>
        </tr>
        <?php
    }
    ?>
</table>
</body>
</html>