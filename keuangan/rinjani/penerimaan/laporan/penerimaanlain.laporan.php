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
    <link rel="stylesheet" type="text/css" href="../../style/style.css?<?=filemtime('../../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../../script/tables.js"></script>
    <script language="javascript" src="../../script/tools.js"></script>
    <script language="javascript" src="../../script/toast.js"></script>
    <script language="javascript" src="../../script/vldr.js"></script>
    <script language="javascript" src="../../script/dialogbox.js"></script>
    <script language="javascript" src="../../script/qsbuilder.js"></script>
    <script language="javascript" src="penerimaanlain.laporan.js?r=<?=filemtime('penerimaanlain.laporan.js')?>"></script>
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

$totalPage = ceil($nData / $nRowPerPage);
?>

<div id="dvLaporan">
<table width="95%" border="0">
<tr>
    <td width="20%">
        <span style='color: #999'>Total <?= $namaPenerimaan ?></span><br>
        <span style='color: #333; font-size: 18px'><?= FormatRupiah($totalJumlah) ?></span>
    </td>
    <td width="*" align="right" valign="bottom">
        <a href="JavaScript:refresh()"><img src="../../images/ico/refresh.png" border="0">&nbsp;refresh</a>&nbsp;
        <a href="JavaScript:cetak()"><img src="../../images/ico/print.png" border="0">&nbsp;cetak</a>&nbsp;
        <a href="JavaScript:excel()"><img src="../../images/ico/excel.png" border="0">&nbsp;excel</a>&nbsp;
    </td>
</tr>
</table>

<table class="tab" id="table" border="1" width="100%">
<tr height="30" align="center" class="header">
    <td width="5%">No</td>
    <td width="15%" style="cursor:pointer; color:<?php ColumnColor("p.tanggal DESC, p.replid DESC") ?>;"  onclick="onChangeUrut('p.tanggal DESC, p.replid DESC')">No. Jurnal/Tanggal</td>
    <td width="15%" style="cursor:pointer; color:<?php ColumnColor("p.sumber ASC") ?>;" onclick="onChangeUrut('p.sumber ASC')">Sumber</td>
    <td width="15%" style="cursor:pointer; color:<?php ColumnColor("p.jumlah DESC") ?>;" onclick="onChangeUrut('p.jumlah DESC')">Jumlah</td>
    <td width="25%">Keterangan</td>
    <td width="10%" style="cursor:pointer; color:<?php ColumnColor("p.petugas ASC") ?>;" onclick="onChangeUrut('p.petugas ASC')">Petugas</td>
</tr>
<?php
$startFromIndex = ($page - 1) * $nRowPerPage;
$sql = "SELECT p.replid AS id, j.nokas, p.sumber, date_format(p.tanggal, '%d-%b-%Y') AS tanggal, p.keterangan, p.jumlah, p.petugas 
	      FROM penerimaanlain p, jurnal j, datapenerimaan dp 
		 WHERE j.replid = p.idjurnal 
		   AND j.idtahunbuku = '$idTahunBuku'
		   AND p.idpenerimaan = dp.replid 
		   AND p.idpenerimaan = '$idPenerimaan' 
		   AND dp.departemen = '$departemen' 
		   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
		 ORDER BY $urut  
		 LIMIT $startFromIndex, $nRowPerPage";

$cnt = $startFromIndex;
$res = $db->QueryDb($sql);
while ($row = mysqli_fetch_array($res))
{
    ?>
    <tr height="25">
        <td align="center" class="numberColumn"><?=++$cnt?></td>
        <td align="center"><?="<strong>" . $row['nokas'] . "</strong><br>" . $row['tanggal']?></td>
        <td align="left"><?=$row['sumber'] ?></td>
        <td align="right"><?=FormatRupiah($row['jumlah'])?></td>
        <td><?=$row['keterangan'] ?></td>
        <td><?=$row['petugas'] ?></td>
    </tr>
<?php
}
?>
</table>
</div>

<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idpenerimaan" value="<?= $idPenerimaan ?>">
<input type="hidden" id="namapenerimaan" value="<?= $namaPenerimaan ?>">
<input type="hidden" id="tanggal1" value="<?= $tanggal1 ?>">
<input type="hidden" id="tanggal2" value="<?= $tanggal2 ?>">
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
echo "&nbsp;dari $totalPage, jumlah $nData data";
echo "</div>";
?>

</body>
</html>