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
header('Content-Disposition: attachment; filename=Iuran_Wajib_Siswa_per_Kelas.xls');
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

if ($status == -1)
{
    if ($idKelas == -1)
    {
        // semua kelas di tingkat terpilih
        $sql = "SELECT MAX(jumlah), COUNT(nis) 
	              FROM ((SELECT b.nis AS nis, COUNT(p.replid) AS jumlah 
		 				   FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s, jbsakad.kelas k 
					 	  WHERE b.info2 = '$idTahunBuku' 
					 	    AND b.idpenerimaan = $idPenerimaan 
					 	    AND b.nis = s.nis  
						    AND s.idkelas = k.replid 
						    AND k.idtingkat = $idTingkat 
						  GROUP BY s.nis) AS x)";
    }
    else
    {
        // tingkat & kelas terpilih
        $sql = "SELECT MAX(jumlah), COUNT(nis) 
                FROM ((SELECT b.nis AS nis, COUNT(p.replid) AS jumlah 
                         FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s 
                        WHERE b.info2 = '$idTahunBuku' 
                          AND b.idpenerimaan = $idPenerimaan 
                          AND b.nis = s.nis 
                          AND s.idkelas = $idKelas 
                        GROUP BY s.nis) AS x)";
    }
}
else
{
    if ($idKelas == -1)
    {
        // semua kelas di tingkat terpilih
        $sql = "SELECT MAX(jumlah), COUNT(nis) 
                  FROM ((SELECT b.nis AS nis, COUNT(p.replid) AS jumlah 
                           FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s, jbsakad.kelas k 
                          WHERE b.nis = s.nis 
                            AND b.idpenerimaan = $idPenerimaan 
                            AND b.lunas = $status 
                            AND s.idkelas = k.replid 
                            AND k.idtingkat = $idTingkat 
                          GROUP BY s.nis) AS x)";
    }
    else
    {
        // tingkat & kelas terpilih
        $sql = "SELECT MAX(jumlah), COUNT(nis) 
                  FROM ((SELECT b.nis AS nis, COUNT(p.replid) AS jumlah 
                           FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s 
                          WHERE b.nis = s.nis 
                            AND b.idpenerimaan = $idPenerimaan 
                            AND b.lunas = $status 
                            AND s.idkelas = $idKelas 
                          GROUP BY s.nis) AS x)";
    }
}

// -- calculate table width ----------------------------------------
$row = $db->FetchSingleRow($sql);
$max_n_cicilan = $row[0];
$ndata = $row[1];
$table_width = 810 + $max_n_cicilan * 100;

if ($status == -1)
{
    if ($idKelas == -1)
    {
        // semua kelas di tingkat terpilih
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBiaya
                            FROM jbsfina.besarjtt b, jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t 
                           WHERE b.info2 = '$idTahunBuku' 
                             AND s.nis = b.nis 
                             AND b.idpenerimaan = $idPenerimaan  
                             AND s.idkelas = k.replid 
                             AND k.idtingkat = $idTingkat 
                             AND k.idtingkat = t.replid";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon
                                   FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t 
                                  WHERE b.info2 = '$idTahunBuku' 
                                    AND s.nis = b.nis 
                                    AND b.idpenerimaan = $idPenerimaan  
                                    AND s.idkelas = k.replid 
                                    AND k.idtingkat = $idTingkat 
                                    AND k.idtingkat = t.replid";

        $sql = "SELECT DISTINCT b.nis, s.nama, k.kelas, t.tingkat, b.replid AS id, b.besar, b.keterangan, b.lunas 
                  FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t 
                 WHERE b.info2 = '$idTahunBuku' 
                   AND s.nis = b.nis 
                   AND b.idpenerimaan = $idPenerimaan 
                   AND s.idkelas = k.replid 
                   AND k.idtingkat = $idTingkat 
                   AND k.idtingkat = t.replid 
                 ORDER BY $urut ASC";
    }
    else
    {
        // tingkat & kelas terpilih
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBiaya 
                            FROM jbsfina.besarjtt b, jbsakad.siswa s, jbsakad.kelas k
                           WHERE b.info2 = '$idTahunBuku'
                             AND s.nis = b.nis 
                             AND b.idpenerimaan = $idPenerimaan 
                             AND s.idkelas = $idKelas 
                             AND s.idkelas = k.replid";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon 
                                   FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s, jbsakad.kelas k
                                  WHERE b.info2 = '$idTahunBuku' 
                                    AND s.nis = b.nis 
                                    AND b.idpenerimaan = $idPenerimaan 
                                    AND s.idkelas = $idKelas 
                                    AND s.idkelas = k.replid";

        $sql = "SELECT DISTINCT b.nis, s.nama, k.kelas, b.replid AS id, b.besar, b.keterangan, b.lunas 
                  FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s, jbsakad.kelas k 
                 WHERE b.info2 = '$idTahunBuku' 
                   AND s.nis = b.nis 
                   AND b.idpenerimaan = $idPenerimaan 
                   AND s.idkelas = $idKelas 
                   AND s.idkelas = k.replid 
                 ORDER BY $urut ASC";
    }
}
else
{
    if ($idKelas == -1)
    {
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBiaya
                            FROM jbsfina.besarjtt b, jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t 
                           WHERE b.info2 = '$idTahunBuku' 
                             AND s.nis = b.nis 
                             AND b.idpenerimaan = $idPenerimaan  
                             AND s.idkelas = k.replid 
                             AND k.idtingkat = $idTingkat 
                             AND k.idtingkat = t.replid 
                             AND b.lunas = $status";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon 
                                   FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t 
                                  WHERE b.info2 = '$idTahunBuku' 
                                    AND s.nis = b.nis 
                                    AND b.idpenerimaan = $idPenerimaan  
                                    AND s.idkelas = k.replid 
                                    AND k.idtingkat = $idTingkat 
                                    AND k.idtingkat = t.replid 
                                    AND b.lunas = $status";

        $sql = "SELECT DISTINCT b.nis, s.nama, k.kelas, t.tingkat, b.replid AS id, b.besar, b.keterangan, b.lunas 
                  FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t 
                 WHERE b.info2 = '$idTahunBuku' 
                   AND s.nis = b.nis 
                   AND b.idpenerimaan = $idPenerimaan 
                   AND s.idkelas = k.replid 
                   AND k.idtingkat = $idTingkat 
                   AND k.idtingkat = t.replid 
                   AND b.lunas = $status 
                 ORDER BY $urut ASC";
    }
    else
    {
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBiaya
                            FROM jbsfina.besarjtt b, jbsakad.siswa s, jbsakad.kelas k
                           WHERE b.info2 = '$idTahunBuku' 
                             AND s.nis = b.nis 
                             AND b.idpenerimaan = $idPenerimaan 
                             AND s.idkelas = $idKelas 
                             AND s.idkelas = k.replid 
                             AND b.lunas = $status";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon  
                                   FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s, jbsakad.kelas k
                                  WHERE b.info2 = '$idTahunBuku' 
                                    AND s.nis = b.nis 
                                    AND b.idpenerimaan = $idPenerimaan 
                                    AND s.idkelas = $idKelas 
                                    AND s.idkelas = k.replid 
                                    AND b.lunas = $status";

        $sql = "SELECT DISTINCT b.nis, s.nama, k.kelas, b.replid AS id, b.besar, b.keterangan, b.lunas 
                  FROM jbsfina.penerimaanjtt p RIGHT JOIN jbsfina.besarjtt b ON p.idbesarjtt = b.replid, jbsakad.siswa s, jbsakad.kelas k
                 WHERE b.info2 = '$idTahunBuku' 
                   AND s.nis = b.nis 
                   AND b.idpenerimaan = $idPenerimaan 
                   AND s.idkelas = $idKelas 
                   AND s.idkelas = k.replid 
                   AND b.lunas = $status 
                 ORDER BY $urut ASC";
    }
}

$totalBiayaAll = $db->FetchSingle($sql_sum_biaya, 0);
$row = $db->FetchSingleRow($sql_sum_bayar_diskon);
$totalBayarAll = $row[0] + $row[1];
$totalDiskonAll = $row[1];
?>


<center><font size="4" face="Arial"><strong>LAPORAN PEMBAYARAN IURAN WAJIB SISWA</strong></font><br></center>
<br><br><br>

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
<tr>
    <td>Status:</td>
    <td><?=$namaStatus?></td>
</tr>
</table>
<br>

<table border="0" cellpadding="0" cellspacing="2">
<tr>
    <td width="180">
        Total <?= $namaPenerimaan ?>: <?= FormatRupiah($totalBiayaAll) ?>
    </td>
    <td width="180">
        Total Pembayaran: <?= FormatRupiah($totalBayarAll) ?>
    </td>
    <td width="180">
        Total Diskon: <?= FormatRupiah($totalDiskonAll) ?>
    </td>
    <td width="180">
        Total Tunggakan: <?= FormatRupiah($totalBiayaAll - $totalBayarAll) ?>
    </td>
</tr>
</table>
<br>

<table id="table" border="1" cellpadding="5" style="border-collapse:collapse" cellspacing="0" width="<?=$table_width?>">
<tr height="30" align="center">
    <td width="30">No</td>
    <td width="80">NIS</td>
    <td width="140">Nama</td>
    <td width="75">Kelas</td>
<?php
    for($i = 0; $i < $max_n_cicilan; $i++)
    {
        $n = $i + 1;
        echo "<td colspan='2' width='120' align='center'>Bayaran-$n</td>";
    } ?>
    <td width="90">Status</td>
    <td width="125"><?=$namaPenerimaan ?></td>
    <td width="125">Sub Total Pembayaran</td>
    <td width="125">Sub Total Diskon</td>
    <td width="125">Sub Total Tunggakan</td>
    <td width="200">Keterangan</td>
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

        <tr height="40">
            <td align="center" class="numberColumn"><?=++$cnt ?></td>
            <td align="center"><?=$row['nis'] ?></td>
            <td><?=$row['nama'] ?></td>
            <td align="center">
                <?php if ($idKelas == -1) echo $row['tingkat'] . " - "; ?>
                <?=$row['kelas'] ?>
            </td>
<?php
        $sql = "SELECT COUNT(p.replid)
                  FROM jbsfina.penerimaanjtt p, jbsfina.jurnal j 
                 WHERE p.idjurnal = j.replid 
                   AND j.idtahunbuku = $idTahunBuku 
                   AND p.idbesarjtt = $idbesarjtt";
        $nbayar = $db->FetchSingle($sql, 0);
        $nblank = $max_n_cicilan - $nbayar;

        $totalbayar = 0;
        $totaldiskon = 0;

        if ($nbayar > 0)
        {
            $sql = "SELECT DATE_FORMAT(p.tanggal, '%d-%b-%y'), p.jumlah, p.info1 
                      FROM jbsfina.penerimaanjtt p, jbsfina.jurnal j 
                     WHERE p.idjurnal = j.replid 
                       AND j.idtahunbuku = $idTahunBuku 
                       AND p.idbesarjtt = $idbesarjtt 
                     ORDER BY p.tanggal";
            $result2 = $db->QueryDb($sql);
            while ($row2 = mysqli_fetch_row($result2))
            {
                $totalbayar = $totalbayar + $row2[1] + $row2[2];
                $totaldiskon = $totaldiskon + $row2[2];	?>
                <td><?=FormatRupiah($row2[1]) ?></td>
                <td><?=$row2[0] ?></td>
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
                <td align="center"><?=$infojtt ?></td>
                <td align="right"><?=FormatRupiah($besarjtt) ?></td>
                <td align="right"><?=FormatRupiah($totalbayar) ?></td>
                <td align="right"><?=FormatRupiah($totaldiskon) ?></td>
                <td align="right"><?=FormatRupiah($besarjtt - $totalbayar) ?></td>
                <td align="right"><?=$ketjtt ?></td>
        </tr>
<?php
    }
        ?>
    </table>

</body>
</html>