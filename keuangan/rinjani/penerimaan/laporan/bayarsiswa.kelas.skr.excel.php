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
require_once('bayarsiswa.kelas.jtt.func.php');

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Iuran_Sukarela_Siswa_per_Kelas.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idTingkat = RequestData("idtingkat", 0);
$namaTingkat = RequestData("namatingkat", "");
$idKelas = RequestData("idkelas", 0);
$namaKelas = RequestData("namakelas", "");
$idKategori = RequestData("idkategori", 0);
$namaKategori = RequestData("namakategori", "");
$idPenerimaan = RequestData("idpenerimaan", 0);
$namaPenerimaan = RequestData("namapenerimaan", "");
$status = RequestData("status", -1);
$namaStatus = RequestData("namastatus", -1);
$urut = RequestData("urut", "s.nis");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Laporan Pembayaran Siswa Per Kelas</title>
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

if ($idKelas == -1)
{
    $sql = "SELECT MAX(jml), COUNT(nis) FROM ((SELECT p.nis, COUNT(p.replid) as jml 
		    						 FROM jbsfina.penerimaaniuran p, jbsfina.jurnal j, jbsakad.siswa s, jbsakad.kelas k 
									WHERE p.idjurnal = j.replid 
									  AND j.idtahunbuku = $idTahunBuku 
									  AND p.nis = s.nis 
									  AND p.idpenerimaan = $idPenerimaan 
									  AND s.idkelas = k.replid 
									  AND k.idtingkat = $idTingkat 
								    GROUP BY p.nis) as X)";
}
else
{
    $sql = "SELECT MAX(jml), COUNT(nis) FROM ((SELECT p.nis, COUNT(p.replid) as jml 
								     FROM jbsfina.penerimaaniuran p, jbsfina.jurnal j, jbsakad.siswa s 
								    WHERE p.idjurnal = j.replid 
								      AND j.idtahunbuku = $idTahunBuku 
									  AND p.nis = s.nis 
									  AND s.idkelas = $idKelas A 
									  AND p.idpenerimaan = $idPenerimaan 
								    GROUP BY p.nis) as X)";
}
$result = $db->QueryDb($sql);
$row = mysqli_fetch_row($result);
$max_n_bayar = $row[0];
$ndata = $row[1];

if ($idKelas == -1)
{
    $sql_tot = "SELECT SUM(p.jumlah)
                  FROM jbsfina.penerimaaniuran p, jbsfina.jurnal j, jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t
                 WHERE p.idjurnal = j.replid
                   AND j.idtahunbuku = $idTahunBuku
                   AND p.nis = s.nis
                   AND s.idkelas = k.replid
                   AND k.idtingkat = $idTingkat
                   AND p.idpenerimaan = $idPenerimaan
                   AND k.idtingkat = t.replid";
}
else
{
    $sql_tot = "SELECT SUM(p.jumlah)
                  FROM jbsfina.penerimaaniuran p, jbsfina.jurnal j, jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t
                 WHERE p.idjurnal = j.replid
                   AND j.idtahunbuku = $idTahunBuku
                   AND p.nis = s.nis
                   AND s.idkelas = k.replid
                   AND k.idtingkat = $idTingkat
                   AND k.replid = $idKelas
                   AND p.idpenerimaan = $idPenerimaan
                   AND k.idtingkat = t.replid";
}
$result = $db->QueryDb($sql_tot);
$row = mysqli_fetch_row($result);
$totalSemua = $row[0];
?>

<center><font size="4" face="Arial"><strong>LAPORAN PEMBAYARAN IURAN SUKARELA SISWA</strong></font><br /></center>
<br /><br /><br />

<table border="0">
<tr>
    <td>Departemen:</td>
    <td><?=$departemen?></td>
</tr>
<tr>
    <td>Tingkat:</td>
    <td><?=$namaTingkat?></td>
</tr>
<tr>
    <td>Kelas:</td>
    <td><?=$namaKelas?></td>
</tr>
<tr>
    <td>Kategori:</td>
    <td><?=$namaKategori?></td>
</tr>
<tr>
    <td>Penerimaan:</td>
    <td><?=$namaPenerimaan?></td>
</tr>
</table>
<br>

<table border="0" cellpadding="0" cellspacing="2">
<tr>
    <td width="180">
        Total <?= $namaPenerimaan ?>: <?= FormatRupiah($totalSemua) ?>
    </td>
</tr>
</table>
<br><br>

<table class="tab" id="table" border="1" cellpadding="5" style="border-collapse:collapse" cellspacing="0" width="<?=$table_width ?>">
<tr height="30" align="center" class="header">
    <td width="30">No</td>
    <td width="90">NIS</td>
    <td width="160">Nama</td>
    <td width="50">Kelas</td>
<?php
    for($i = 1; $i <= $max_n_bayar; $i++) {
        echo "<td width='100' colspan='2'>Bayaran-$i</td>";
} ?>
    <td width="100">Total Pembayaran</td>
</tr>
<?php
    if ($idKelas == -1)
    {
        $sql = "SELECT DISTINCT p.nis, s.nama, k.kelas, t.tingkat 
                  FROM jbsfina.penerimaaniuran p, jbsfina.jurnal j, jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t 
                 WHERE p.idjurnal = j.replid 
                   AND j.idtahunbuku = $idTahunBuku 
                   AND p.nis = s.nis 
                   AND s.idkelas = k.replid 
                   AND k.idtingkat = $idTingkat 
                   AND p.idpenerimaan = $idPenerimaan 
                   AND k.idtingkat = t.replid 
                 ORDER BY $urut ASC";
    }
    else
    {
        $sql = "SELECT DISTINCT p.nis, s.nama, k.kelas 
                  FROM jbsfina.penerimaaniuran p, jbsfina.jurnal j, jbsakad.siswa s, jbsakad.kelas k 
                 WHERE p.idjurnal = j.replid 
                   AND j.idtahunbuku = $idTahunBuku 
                   AND p.nis = s.nis 
                   AND s.idkelas = k.replid 
                   AND s.idkelas = $idKelas 
                   AND p.idpenerimaan = $idPenerimaan 
                 ORDER BY $urut ASC";
    }
    $result = $db->QueryDb($sql);

    $cnt = $startFromIndex;
    $totalall = 0;
    while ($row = mysqli_fetch_array($result))
    {
        $nis = $row['nis'];
        ?>
        <tr height="40">
            <td align="center"><?=++$cnt ?></td>
            <td align="center"><?=$row['nis'] ?></td>
            <td align="left"><?=$row['nama'] ?></td>
            <td align="center">
<?php           if ($idKelas == -1) echo  $row['tingkat'] . " - "; ?>
<?php           echo $row['kelas'] ?>
            </td>
<?php	$sql = "SELECT date_format(p.tanggal, '%d-%b-%y') as tanggal, jumlah 
                  FROM jbsfina.penerimaaniuran p, jbsfina.jurnal j
				 WHERE p.idjurnal = j.replid 
				   AND j.idtahunbuku = $idTahunBuku 
				   AND nis = '$nis' 
				   AND idpenerimaan = $idPenerimaan";
            $result2 = $db->QueryDb($sql);
            $nbayar = mysqli_num_rows($result2);
            $nblank = $max_n_bayar - $nbayar;

            $totalbayar = 0;
            while ($row2 = mysqli_fetch_array($result2))
            {
                $totalbayar += $row2['jumlah']; ?>
                <td><?=FormatRupiah($row2['jumlah']) ?></td>
                <td><?=$row2['tanggal'] ?></td>
<?php	    }    //end while
            $totalall += $totalbayar;

            for ($i = 0; $i < $nblank; $i++)
            {
                echo "<td>&nbsp;</td>";
                echo "<td>&nbsp;</td>";
            } //end for
            ?>
            <td align="right"><?=FormatRupiah($totalbayar) ?></td>
        </tr>
        <?php
    } //end while
    ?>
</table>

</body>
</html>