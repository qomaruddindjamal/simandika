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
header('Content-Disposition: attachment; filename=Iuran_Sukarela_Calon_Siswa_per_Kelompok.xls');
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
    <title>Laporan Pembayaran Iuran Sukarela alon Siswa Per Kelompok</title>
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

if ($idKelompok == -1)
{
    $sql = "SELECT max(jml), count(replid) 
              FROM ((SELECT s.replid, COUNT(p.replid) as jml 
			 		   FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j, jbsakad.calonsiswa s 
					  WHERE p.idjurnal = j.replid 
					    AND j.idtahunbuku = '$idTahunBuku' 
						AND p.idcalon = s.replid 
						AND p.idpenerimaan = '$idPenerimaan' 
					  GROUP BY s.replid) as X)";
}
else
{
    $sql = "SELECT max(jml), count(replid) 
              FROM ((SELECT s.replid, COUNT(p.replid) as jml 
				  	   FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j, jbsakad.calonsiswa s 
                      WHERE p.idjurnal = j.replid 
                        AND j.idtahunbuku = '$idTahunBuku'
					    AND p.idcalon = s.replid 
					    AND s.idkelompok = '$idKelompok' 
					    AND p.idpenerimaan = '$idPenerimaan' 
					  GROUP BY s.replid) as X)";
}
$row = $db->FetchSingleRow($sql);
$max_n_bayar = $row[0];
$ndata = $row[1];

if ($max_n_bayar == 0 && $ndata == 0)
{
    echo "<span style='color: maroon; font-size: 13px;'>";
    echo "Belum data pembayaran di kelompok terpilih";
    echo "</span>";
    exit();
}

if ($idKelompok == -1)
{
    $sql_tot = "SELECT SUM(p.jumlah) 
                  FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k 
                 WHERE p.idjurnal = j.replid 
                   AND j.idtahunbuku = '$idTahunBuku' 
                   AND p.idcalon = c.replid 
                   AND c.idkelompok = k.replid 
                   AND p.idpenerimaan = '$idPenerimaan'";

    $sql = "SELECT DISTINCT c.replid, c.nopendaftaran, c.nama, k.kelompok 
	          FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k 
			 WHERE p.idjurnal = j.replid 
			   AND j.idtahunbuku = '$idTahunBuku' 
			   AND p.idcalon = c.replid 
			   AND c.idkelompok = k.replid 
			   AND p.idpenerimaan = '$idPenerimaan' 
		     ORDER BY $urut ASC";
}
else
{
    $sql_tot = "SELECT SUM(p.jumlah) 
                  FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k 
                 WHERE p.idjurnal = j.replid 
                   AND j.idtahunbuku = '$idTahunBuku'
                   AND p.idcalon = c.replid 
                   AND c.idkelompok = k.replid 
                   AND c.idkelompok = '$idKelompok' 
                   AND p.idpenerimaan = '$idPenerimaan'";

    $sql = "SELECT DISTINCT c.replid, c.nopendaftaran, c.nama, k.kelompok 
	          FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k 
			 WHERE p.idjurnal = j.replid 
			   AND j.idtahunbuku = '$idTahunBuku'
			   AND p.idcalon = c.replid 
			   AND c.idkelompok = k.replid 
			   AND c.idkelompok = '$idKelompok' 
			   AND p.idpenerimaan = '$idPenerimaan' 
	         ORDER BY $urut ASC";

}
$res = $db->QueryDb($sql_tot);
$row = mysqli_fetch_row($res);
$TotalPembayaran = $row[0];
?>
<center><font size="4" face="Arial"><strong>LAPORAN PEMBAYARAN IURAN SUKARELA CALON SISWA</strong></font><br /></center>
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
    <td><?= FormatRupiah($TotalPembayaran) ?></td>
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
    for($i = 0; $i < $max_n_bayar; $i++)
    {
        $n = $i + 1;
        echo "<td colspan='2'>Bayaran-$n</td>";
    } ?>
    <td>Total Pembayaran</td>
</tr>
<?php
$cnt = 0;
$totalall = 0;
$res = $db->QueryDb($sql);
while ($row = mysqli_fetch_array($res))
{
    $replid = $row['replid'];
?>
    <tr height="40">
        <td><?= ++$cnt ?></td>
        <td><?= $row['nopendaftaran'] ?></td>
        <td><?= $row['nama'] ?></td>
        <td><?= $row['kelompok'] ?></td>
<?php
        $sql = "SELECT date_format(p.tanggal, '%d-%b-%y') as tanggal, jumlah 
                  FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j
                 WHERE p.idjurnal = j.replid 
                   AND j.idtahunbuku = '$idTahunBuku' 
                   AND idcalon = '$replid' 
                   AND idpenerimaan = '$idPenerimaan'";
        $result2 = $db->QueryDb($sql);
        $nbayar = mysqli_num_rows($result2);
        $nblank = $max_n_bayar - $nbayar;

        $totalbayar = 0;
        while ($row2 = mysqli_fetch_array($result2))
        {
            $totalbayar += $row2['jumlah']; ?>
            <td><?= FormatRupiah($row2['jumlah']) ?></td>
            <td><?= $row2['tanggal'] ?></td>
<?php
        }
        $totalall += $totalbayar;

        for ($i = 0; $i < $nblank; $i++)
        {
            echo "<td>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
        } ?>
        <td align="right"><?=FormatRupiah($totalbayar) ?></td>
    </tr>
<?php
} // end while
?>
</table>
</body>
</html>