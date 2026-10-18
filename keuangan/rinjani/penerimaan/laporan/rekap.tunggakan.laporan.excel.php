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
header('Content-Disposition: attachment; filename=Rekapitulasi_Tunggakan_Siswa.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idTingkat = RequestData("idtingkat", 0);
$namaTingkat = RequestData("namatingkat", "");
$idKelas = RequestData("idkelas", "");
$namaKelas = RequestData("namakelas", "");
$idTahunBuku = RequestData("idtahunbuku", 0);
$namaTahunBuku = RequestData("namatahunbuku", "");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekapitulasi Pembayaran</title>
</head>
<body>

<center><font size="4" face="Arial"><strong>REKAPITULASI TUNGGAKAN SISWA</strong></font><br /></center>
<br />
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
        <td><?=$namaTingkat?></td>
    </tr>
    <tr>
        <td>Tahun Buku:</td>
        <td><?=$namaTahunBuku?></td>
    </tr>
</table>
<br>
<br>

<?php
// Ambil penerimaan di departemen terpilih
$arrpen = array();
$sql = "SELECT replid, nama 
          FROM jbsfina.datapenerimaan 
         WHERE departemen = '$departemen' 
           AND idkategori = 'JTT'";
$res = $db->QueryDb($sql);
$i = 0;
while($row = mysqli_fetch_row($res))
{
    $arrpen[$i][0] = $row[0];
    $arrpen[$i][1] = $row[1];
    $i++;
}
$n_arrpen = $i;
$width = 1180 + $n_arrpen * 600;

// Ambil data siswa
if ($idTingkat == -1)
{
    // semua tingkat & kelas
    $sqlsiswa = "SELECT DISTINCT s.nis, s.nama, s.pinsiswa, t.tingkat, k.kelas, s.alamatsiswa, s.kodepossiswa, s.namaayah, s.namaibu, s.telponortu, s.hportu
			 	   FROM jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t, jbsfina.besarjtt b
				  WHERE s.idkelas = k.replid 
				    AND k.idtingkat = t.replid 
				    AND s.nis = b.nis 
				    AND b.besar <> 0 
				    AND b.lunas = 0 
				    AND b.info2 = '$idTahunBuku' 
				    AND t.departemen = '$departemen' 
   		          ORDER BY t.urutan, k.kelas, s.nama";
}
else
{
    if ($idKelas == -1)
    {
        // semua kelas di tingkat terpilih
        $sqlsiswa = "SELECT DISTINCT s.nis, s.nama, s.pinsiswa, t.tingkat, k.kelas, s.alamatsiswa, s.kodepossiswa, s.namaayah, s.namaibu, s.telponortu, s.hportu
				 	   FROM jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t, jbsfina.besarjtt b
				 	  WHERE s.idkelas = k.replid 
				 	    AND k.idtingkat = t.replid 
				 	    AND s.nis = b.nis 
				 	    AND b.besar <> 0 
				 	    AND b.lunas = 0 
				 	    AND b.info2 = '$idTahunBuku' 
				 	    AND t.replid = '$idTingkat' 
				 	    AND t.departemen = '$departemen' 
			  	      ORDER BY t.urutan, k.kelas, s.nama";
    }
    else
    {
        // tingkat & kelas terpilih
        $sqlsiswa = "SELECT DISTINCT s.nis, s.nama, s.pinsiswa, t.tingkat, k.kelas, s.alamatsiswa, s.kodepossiswa, s.namaayah, s.namaibu, s.telponortu, s.hportu
				 	   FROM jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t, jbsfina.besarjtt b
				 	  WHERE s.idkelas = k.replid 
				 	    AND k.idtingkat = t.replid 
				 	    AND s.nis = b.nis 
				 	    AND b.besar <> 0 
				 	    AND b.lunas = 0 
				 	    AND b.info2 = '$idTahunBuku' 
				 	    AND k.replid = '$idKelas' 
				 	    AND t.replid = '$idTingkat' 
				 	    AND t.departemen = '$departemen' 
	  			      ORDER BY t.urutan, k.kelas, s.nama";
    }

}
?>

<table width="<?=$width?>" id="table" border="1" cellpadding="5" style="border-collapse:collapse" cellspacing="0"  align="left">
    <tr>
        <td rowspan="2">No</td>
        <td rowspan="2">NIS</td>
        <td rowspan="2">Nama</td>
        <td rowspan="2">PIN</td>
        <td rowspan="2">Tingkat</td>
        <td rowspan="2">Kelas</td>
        <td rowspan="2">Ayah</td>
        <td rowspan="2">Ibu</td>
        <td rowspan="2">Alamat</td>
        <td rowspan="2">Kode Pos</td>
        <td rowspan="2">Telpon Ortu</td>
        <td rowspan="2">HP Ortu</td>
        <?php
        for ($i = 0; $i < $n_arrpen; $i++)
        {
            echo "<td colspan='8'>" . $arrpen[$i][1] . "</td>";
        } ?>
    </tr>
    <tr>
        <?php
        for ($i = 0; $i < $n_arrpen; $i++)
        { ?>
            <td>Cicilan</td>
            <td>Total</td>
            <td>Pembayaran</td>
            <td>Diskon</td>
            <td>Sisa</td>
            <td>Tgl.Akhir</td>
            <td>Bay.Akhir</td>
            <td>Ket.Akhir</td>
            <?php
        } ?>
    </tr>
    <?php

    $res = $db->QueryDb($sqlsiswa);
    $n = 0;

    $arrtotal = array();
    for($i = 0; $i < $n_arrpen; $i++)
    {
        for ($j = 0; $j < 8; $j++)
        {
            if ($j >= 0 && $j <= 4)
                $arrtotal[$i * 8 + $j] = 0;
            else
                $arrtotal[$i * 8 + $j] = "";
        }
    }

    while ($row = mysqli_fetch_array($res))
    {
    $n++;
    $nis = $row['nis'];

    $color = "#FFF";
    $color2 = "#FFFFD5";
    if ($n % 2 == 0)
    {
        $color = "#EEE";
        $color2 = "#FFFFB3";
    } ?>

    <tr>
        <td><?=$n?></td>
        <td><?=$row['nis']?></td>
        <td><?=$row['nama']?></td>
        <td><?=$row['pinsiswa']?></td>
        <td><?=$row['tingkat']?></td>
        <td><?=$row['kelas']?></td>
        <td><?=$row['namaayah']?></td>
        <td><?=$row['namaibu']?></td>
        <td><?=$row['alamatsiswa']?></td>
        <td><?=$row['kodepossiswa']?></td>
        <td><?=$row['telponortu']?></td>
        <td><?=$row['hportu']?></td>
        <?php
        for ($i = 0; $i < $n_arrpen; $i++)
        {
            $idpenerimaan = $arrpen[$i][0];
            $sql = "SELECT b.nis, b.besar, SUM(p.jumlah) AS jumlah, b.cicilan, SUM(p.info1) AS diskon
                  FROM jbsfina.besarjtt b, jbsfina.penerimaanjtt p
                 WHERE b.replid = p.idbesarjtt 
                   AND b.idpenerimaan = '$idpenerimaan' 
                   AND b.nis = '$nis' 
                   AND b.info2 = '$idTahunBuku'
                 GROUP BY b.nis";
            $res2 = $db->QueryDb($sql);
            $row2 = mysqli_fetch_row($res2);
            $besar = $row2[1];
            $jumlah = $row2[2] + $row2[4];
            $bcicilan = $row2[3];
            $diskon = $row2[4];
            $sisa = $besar - $jumlah;
            if (0 == mysqli_num_rows($res2))
            {
                $sql = "SELECT b.besar, b.cicilan
                      FROM jbsfina.besarjtt b
                     WHERE b.idpenerimaan = '$idpenerimaan' 
                       AND b.nis = '$nis' 
                       AND b.info2 = '$idTahunBuku'";
                $res2 = $db->QueryDb($sql);
                if (0 != mysqli_num_rows($res2))
                {
                    $row2 = mysqli_fetch_row($res2);
                    $besar = $row2[0];
                    $jumlah = 0;
                    $bcicilan = $row2[1];
                    $diskon = 0;
                    $sisa = $besar;
                }
            }

            $sql = "SELECT DATE_FORMAT(p.tanggal, '%d-%b-%Y') AS tanggal, p.jumlah, p.keterangan, p.info1
                  FROM jbsfina.besarjtt b, jbsfina.penerimaanjtt p 
                 WHERE b.replid = p.idbesarjtt 
                   AND b.idpenerimaan = '$idpenerimaan' 
                   AND b.nis = '$nis' 
                   AND b.info2 = '$idTahunBuku'
                 ORDER BY tanggal DESC, p.replid DESC
                 LIMIT 1";
            $res2 = $db->QueryDb($sql);
            $row2 = mysqli_fetch_row($res2);
            $tglakhir = $row2[0];
            $jumakhir = $row2[1];
            $ketakhir = $row2[2];
            $dknakhir = $row2[3];

            if ($sisa != 0)
            {
                $idx = $i * 8;
                $arrtotal[$idx] += $bcicilan;
                $arrtotal[$idx + 1] += $besar;
                $arrtotal[$idx + 2] += $jumlah;
                $arrtotal[$idx + 3] += $diskon;
                $arrtotal[$idx + 4] += $sisa;
            }
            if ($sisa == 0)
            {
                echo  "<td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>
                   <td>&nbsp;</td>";
            }
            else
            {
                echo "<td>" . FormatRupiah($bcicilan) . "</td>";
                echo "<td>" . FormatRupiah($besar) . "</td>";
                echo "<td>" . FormatRupiah($jumlah) . "</td>";
                echo "<td>" . FormatRupiah($diskon) . "</td>";
                echo "<td>" . FormatRupiah($sisa) . "</td>";
                echo "<td>$tglakhir</td>";
                echo "<td>" . FormatRupiah($jumakhir) . "</td>";
                echo "<td>$ketakhir</td>";
            }
        }
        }

        echo "<tr>";
        echo "<td colspan='12'><strong>T O T A L</strong></td>";
        for($i = 0; $i < $n_arrpen; $i++)
        {
            for ($j = 0; $j < 8; $j++)
            {
                if ($j < 5)
                    echo "<td>" . FormatRupiah($arrtotal[$i * 8 + $j]) . "</td>";
                else
                    echo "<td>&nbsp</td>";
            }
        }
        echo "</tr>";
        ?>
</table>
</body>
</html>