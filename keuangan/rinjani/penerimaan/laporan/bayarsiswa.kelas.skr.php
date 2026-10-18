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
$page = RequestData("page", 1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Pembayaran Siswa Per Kelas</title>
    <link rel="stylesheet" type="text/css" href="../../style/style.css?<?=filemtime('../../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../../script/tables.js"></script>
    <script language="javascript" src="../../script/tools.js"></script>
    <script language="javascript" src="../../script/toast.js"></script>
    <script language="javascript" src="../../script/vldr.js"></script>
    <script language="javascript" src="../../script/qsbuilder.js"></script>
    <script language="javascript" src="bayarsiswa.kelas.skr.js?r=<?=filemtime('bayarsiswa.kelas.skr.js')?>"></script>
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

if ($idKelas == -1)
{
    $sql = "SELECT MAX(jml), COUNT(nis) 
              FROM ((SELECT p.nis, COUNT(p.replid) as jml 
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
    $sql = "SELECT MAX(jml), COUNT(nis) 
              FROM ((SELECT p.nis, COUNT(p.replid) as jml 
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

$table_width = 520 + $max_n_bayar * 100;
$startFromIndex = ($page - 1) * $nRowPerPage;
$totalPage = ceil($ndata / $nRowPerPage);
?>
<table width="100%" border="0" align="center">
<tr>
    <td valign="bottom">
        <a href="JavaScript:refresh()"><img src="../../images/ico/refresh.png" border="0"/>&nbsp;refresh</a>&nbsp;&nbsp;
        <a href="JavaScript:cetak()"><img src="../../images/ico/print.png" border="0"/>&nbsp;cetak</a>&nbsp;
        <a href="JavaScript:excel()"><img src="../../images/ico/excel.png" border="0"/>&nbsp;excel</a>&nbsp;
    </td>
</tr>
</table>
<br>

<?php
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

<div id="dvLaporan">
<table border="0" cellpadding="0" cellspacing="2">
<tr>
    <td width="180">
        <span style='color: #999'>Total <?= $namaPenerimaan ?></span><br>
        <span style='color: #333; font-size: 18px'><?= FormatRupiah($totalSemua) ?></span>
    </td>
</tr>
</table>
<br><br>

<table class="tab" id="table" border="1" cellpadding="5" style="border-collapse:collapse" cellspacing="0" width="<?=$table_width ?>">
<tr height="30" align="center" class="header">
    <td width="30" >No</td>
    <td width="90" style="cursor:pointer; color: <?php ColumnColor("s.nis") ?>;" onClick="changeUrutan('s.nis')">NIS</td>
    <td width="160" style="cursor:pointer; color: <?php ColumnColor("s.nama") ?>;" onClick="changeUrutan('s.nama')">Nama</td>
    <td width="50">Kelas</td>
<?php
    for($i = 1; $i <= $max_n_bayar; $i++) {
        echo "<td width='100'>Bayaran-$i</td>";
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
			 ORDER BY $urut ASC 
			 LIMIT $startFromIndex, $nRowPerPage";
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
			 ORDER BY $urut ASC 
			 LIMIT $startFromIndex, $nRowPerPage";
}
$result = $db->QueryDb($sql);
$cnt = $startFromIndex;
$totalall = 0;
while ($row = mysqli_fetch_array($result))
{
    $nis = $row['nis'];
?>
    <tr height="40">
        <td align="center" class="numberColumn"><?=++$cnt ?></td>
        <td align="left">
            <a class="ablue" onclick="showInfoSiswa('<?=$row['nis'] ?>')">
            <?=$row['nis'] ?>
            </a>
        </td>
        <td align="left"><?=$row['nama'] ?></td>
        <td align="center">
<?php       if ($idKelas == -1) echo  $row['tingkat']." - "; ?>
<?php       echo $row['kelas'] ?>
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
            <td>
                <table border="1" class="tab" width="100%" style="border-collapse:collapse" cellpadding="0" cellspacing="0">
                <tr height="20"><td align="center"><?=FormatRupiah($row2['jumlah']) ?></td></tr>
                <tr height="20"><td align="center"><?=$row2['tanggal'] ?></td></tr>
                </table>
            </td>
<?php	} //end while
        $totalall += $totalbayar;

        for ($i = 0; $i < $nblank; $i++)
        {
            echo "<td>&nbsp;</td>";
        } //end for
?>
        <td align="right"><?=FormatRupiah($totalbayar) ?></td>
    </tr>
<?php
} //end while
?>
</table>
</div>

<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idtingkat" value="<?= $idTingkat ?>">
<input type="hidden" id="namatingkat" value="<?= $namaTingkat  ?>">
<input type="hidden" id="idkelas" value="<?= $idKelas  ?>">
<input type="hidden" id="namakelas" value="<?= $namaKelas ?>">
<input type="hidden" id="idkategori" value="<?= $idKategori  ?>">
<input type="hidden" id="namakategori" value="<?= $namaKategori  ?>">
<input type="hidden" id="idpenerimaan" value="<?= $idPenerimaan ?>">
<input type="hidden" id="namapenerimaan" value="<?= $namaPenerimaan  ?>">
<input type="hidden" id="status" value="<?= $status ?>">
<input type="hidden" id="namastatus" value="<?= $namaStatus ?>">
<input type="hidden" id="urut" value="<?= $urut ?>">
<input type="hidden" id="totalpage" value="<?= $totalPage ?>">

<?php
echo "<div id='dvPageControl' style='width: 100%'>";
echo "Halaman&nbsp;&nbsp;";
echo "<input type='button' class='but' style='height: 28px; width: 28px' value=' < ' onclick='onPrevPage()'>";
echo "<select id='page' class='inputbox' style='width: 50px' onchange='onChangePage()'>";
for ($i = 1; $i <= $totalPage; $i++)
{
    $sel = $i == $page ? "selected" : "";
    echo "<option value='$i' $sel>$i</option>";
}
echo "</select>";
echo "<input type='button' class='but' style='height: 28px; width: 28px' value=' > ' onclick='onNextPage()'>";
echo "&nbsp;dari $totalPage, jumlah $ndata data";
echo "</div>";
?>

</body>
</html>