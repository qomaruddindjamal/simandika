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
require_once('bayarcalon.kelompok.cswjb.func.php');

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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Pembayaran Iuran Sukarela Calon Siswa Per Kelompok</title>
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
    <script language="javascript" src="bayarcalon.kelompok.csskr.js?r=<?=filemtime('bayarcalon.kelompok.csskr.js')?>"></script>
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

$table_width = 480 + $max_n_bayar * 120;
$startFromIndex = ($page - 1) * $nRowPerPage;
$totalPage = ceil($ndata / $nRowPerPage);
?>
<table width="100%" border="0" align="center">
<tr>
    <td valign="bottom">
        <a href="JavaScript:refresh()"><img src="../../images/ico/refresh.png" border="0"/>&nbsp;refresh</a>&nbsp;&nbsp;
        <a href="JavaScript:cetak()"><img src="../../images/ico/print.png" border="0"/>&nbsp;cetak</a>&nbsp;&nbsp;
        <a href="JavaScript:excel()"><img src="../../images/ico/excel.png" border="0"/>&nbsp;excel</a>
    </td>
</tr>
</table>
<br>
<?php
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
		     ORDER BY $urut ASC 
		     LIMIT $startFromIndex, $nRowPerPage";
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
	         ORDER BY $urut ASC 
	         LIMIT $startFromIndex, $nRowPerPage";

}
$res = $db->QueryDb($sql_tot);
$row = mysqli_fetch_row($res);
$TotalPembayaran = $row[0];
?>

<div id="dvLaporan">

    <table border="0" cellpadding="0" cellspacing="2">
    <tr>
        <td width="180">
            <span style='color: #999'>Total <?= $namaPenerimaan ?></span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($TotalPembayaran) ?></span>
        </td>
    </tr>
    </table>
    <br><br>

    <table class="tab" id="table" border="1" cellpadding="5" style="border-collapse:collapse" cellspacing="0" width="<?=$table_width ?>">
    <tr height="30" align="center">
        <td class="header" width="30">No</td>
        <td class="header" width="90" style="cursor:pointer;  color: <?php ColumnColor("c.nopendaftaran") ?>;" onClick="onChangeUrut('c.nopendaftaran')">No Pendaftaran</td>
        <td class="header" width="160" style="cursor:pointer;  color: <?php ColumnColor("c.nama") ?>;" onClick="onChangeUrut('c.nama')">Nama</td>
        <td class="header" width="75" style="cursor:pointer;" onClick="onChangeUrut('k.kelompok')">Kelompok</td>
<?php   for($i = 1; $i <= $max_n_bayar; $i++) { ?>
            <td class="header" width="120">Bayaran-<?=$i?></td>
<?php   } ?>
        <td class="header" width="125">Total Pembayaran</td>
    </tr>
<?php
$cnt = $startFromIndex;
$totalall = 0;
$res = $db->QueryDb($sql);
while ($row = mysqli_fetch_array($res))
{
    $replid = $row['replid'];
?>
    <tr height="40">
        <td align="center" class="numberColumn"><?= ++$cnt ?></td>
        <td align="left">
            <a class="ablue" onclick="showInfoCalonSiswa('<?=$row['nopendaftaran'] ?>')">
                <?= $row['nopendaftaran'] ?>
            </a>
        </td>
        <td align="left"><?= $row['nama'] ?></td>
        <td align="left"><?= $row['kelompok'] ?></td>

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
        <td>
            <table border="1" class="tab" width="100%" style="border-collapse:collapse" cellpadding="0" cellspacing="0">
                <tr height="20"><td align="center"><?= FormatRupiah($row2['jumlah']) ?></td></tr>
                <tr height="20"><td align="center"><?= $row2['tanggal'] ?></td></tr>
            </table>
        </td>
<?php
        } //end while
        $totalall += $totalbayar;

        for ($i = 0; $i < $nblank; $i++) {
            echo "<td>&nbsp;</td>";
        } ?>
        <td align="right"><?=FormatRupiah($totalbayar) ?></td>
    </tr>
<?php
} // end while
?>
    </table>
</div>

<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idproses" value="<?= $idProses ?>">
<input type="hidden" id="namaproses" value="<?= $namaProses  ?>">
<input type="hidden" id="idkelompok" value="<?= $idKelompok  ?>">
<input type="hidden" id="namakelompok" value="<?= $namaKelompok ?>">
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