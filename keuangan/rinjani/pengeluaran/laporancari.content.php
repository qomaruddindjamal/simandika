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
require_once('../include/sessioninfo.php');
require_once('../include/sessionchecker.php');
require_once('../library/common.func.php');
require_once('../include/config.php');
require_once('../include/db.onfunc.php');
require_once('../library/departemen.php');
require_once('../library/msg.php');
require_once('../library/rupiah.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$tanggal1 = RequestData("tanggal1", date("Y-m-d"));
$tanggal2 = RequestData("tanggal2", date("Y-m-d"));
$idtahunbuku = RequestData("idtahunbuku", 0);
$namatahunbuku = RequestData("namatahunbuku", "");
$kriteria = RequestData("kriteria", 1);
$namakriteria = RequestData("namakriteria", "");
$keyword = RequestData("keyword", "");
$page = RequestData("page", 1);

$nRowPerPage = 10;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Pengeluaran</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/stringutil.js"></script>
    <script language="javascript" src="../script/dateutil.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="laporancari.content.js?r=<?=filemtime('laporancari.content.js')?>"></script>
</head>
<body style="margin: 10px">

<?php
$sql = "SELECT COUNT(p.replid), SUM(p.jumlah)
          FROM jbsfina.pengeluaran p, jbsfina.datapengeluaran d 
         WHERE p.idpengeluaran = d.replid 
           AND d.departemen = '$departemen' 
           AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'";

if ($kriteria == 1)
    $sql .= " AND p.namapemohon LIKE '%$keyword%'";
else if ($kriteria == 2)
    $sql .= " AND p.penerima LIKE '%$keyword%'";
else if ($kriteria == 3)
    $sql .= " AND p.petugas LIKE '%$keyword%'";
else if ($kriteria == 4)
    $sql .= " AND p.keperluan LIKE '%$keyword%'";
else if ($kriteria == 5)
    $sql .= " AND p.keterangan LIKE '%$keyword%'";

$res = $db->QueryDb($sql);
$row = mysqli_fetch_row($res);
$nData = $row[0];
$totalJumlah = $row[1];
if ($nData == 0)
{
    $db->Close();

    echo "<span style='color: maroon'>tidak ditemukan data transaksi pengeluaran</span><br>";
    exit();
}

$totalPage = ceil($nData / $nRowPerPage);
$startIndex = ($page - 1) * $nRowPerPage;
?>
<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idtahunbuku" value="<?= $idtahunbuku ?>">
<input type="hidden" id="namatahunbuku" value="<?= $namatahunbuku ?>">
<input type="hidden" id="tanggal1" value="<?= $tanggal1 ?>">
<input type="hidden" id="tanggal2" value="<?= $tanggal2 ?>">
<input type="hidden" id="kriteria" value="<?= $kriteria ?>">
<input type="hidden" id="namakriteria" value="<?= $namakriteria ?>">
<input type="hidden" id="keyword" value="<?= $keyword ?>">

<div id="dvLaporan">

<table border='0' width="95%" cellpadding='2'>
<tr>
    <td style='width: 180px'>
        <span style='color: #999'>Jumlah Pengeluaran</span><br>
        <span style='color: #333; font-size: 18px'><?= $nData ?></span>
    </td>
    <td style='width: 180px'>
        <span style='color: #999'>Total Pengeluaran</span><br>
        <span style='color: #333; font-size: 18px'><?= FormatRupiah($totalJumlah)  ?></span>
    </td>
    <td width="*" align="right" valign="bottom">
        <a href="#" class="hide-in-report" onClick="document.location.reload()"><img src="../images/ico/refresh.png" border="0" />&nbsp;refresh</a>&nbsp;
        <a href="JavaScript:cetak()" class="hide-in-report"><img src="../images/ico/print.png" border="0" />&nbsp;cetak</a>&nbsp;
        <a href="JavaScript:excel()" class="hide-in-report"><img src="../images/ico/excel.png" border="0" />&nbsp;excel</a>&nbsp;
    </td>
</tr>
</table><br>

<table class="tab" id="table" border="1" style="border-collapse:collapse" width="100%" align="center">
    <tr height="30" align="center" >
        <td class="header" width="4%" >No</td>
        <td class="header" width="10%">Tanggal</td>
        <td class="header" width="15%">Pemohon</td>
        <td class="header" width="15%">Penerima</td>
        <td class="header" width="*">Keperluan</td>
        <td class="header" width="15%">Jumlah</td>
        <td class="header hide-in-report" width="7%">&nbsp;</td>
    </tr>
<?php
    $sql = "SELECT p.replid AS id, p.keperluan, p.keterangan, p.jenispemohon, p.nip, p.namapemohon, 
                   p.nis, p.pemohonlain, p.penerima, date_format(p.tanggal, '%d-%b-%Y') as tanggal, 
                   date_format(p.tanggalkeluar, '%d-%b-%Y<br>%H:%i') as tanggalkeluar, p.petugas, p.jumlah,
                   d.replid, d.nama 
              FROM jbsfina.pengeluaran p, jbsfina.datapengeluaran d 
             WHERE p.idpengeluaran = d.replid 
               AND d.departemen = '$departemen' 
               AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'";

    if ($kriteria == 1)
        $sql .= " AND p.namapemohon LIKE '%$keyword%'";
    else if ($kriteria == 2)
        $sql .= " AND p.penerima LIKE '%$keyword%'";
    else if ($kriteria == 3)
        $sql .= " AND p.petugas LIKE '%$keyword%'";
    else if ($kriteria == 4)
        $sql .= " AND p.keperluan LIKE '%$keyword%'";
    else if ($kriteria == 5)
        $sql .= " AND p.keterangan LIKE '%$keyword%'";
    $sql .= " ORDER BY p.tanggal DESC
              LIMIT $startIndex, $nRowPerPage";

    $res = $db->QueryDb($sql);

    $cnt = $startIndex;
    $total = 0;
    while ($row = mysqli_fetch_array($res))
    {
        $namapemohon = $row['namapemohon'];
        $total += $row['jumlah'];
        ?>

        <tr height="30">
            <td align="center" valign="top" class="numberColumn"><?= ++$cnt ?></td>
            <td align="center" valign="top"><?=$row['tanggalkeluar'] ?></td>
            <td valign="top"><?=$row['namapemohon'] ?></td>
            <td valign="top"><?=$row['penerima'] ?></td>
            <td valign="top">
                <?=$row['keperluan'] ?><br>
                <b>Petugas: </b><?=$row['petugas'] ?><br>
<?php           if (strlen(trim($row['keterangan'])) > 0)
                    echo "<b>Keterangan: </b> " . $row['keterangan']; ?>
            </td>
            <td align="right" valign="top"><?=FormatRupiah($row['jumlah']) ?></td>
            <td valign="top" align="center" class="hide-in-report">
                <a href="JavaScript:cetakbukti('<?=$row['id'] ?>')"><img src="../images/ico/print.png" border="0"/></a>&nbsp;
<?php           if (getLevel() != 2) { ?>
                    <a href="JavaScript:edit('<?=$row['id'] ?>')"><img src="../images/ico/ubah.png" border="0"/></a>
<?php           } ?>
            </td>
        </tr>
        <?php
    }
    ?>
</table>
</div>

<?php
echo "<div id='dvPageControl' style='width: 100%'>";
echo "Halaman&nbsp;&nbsp;";
echo "<input type='button' class='but' style='width: 30px; height: 25px'  value=' < ' onclick='onPrevPage()'>";
echo "<select id='page' class='inputbox' style='width: 50px' onchange='onChangePage()'>";
for ($i = 1; $i <= $totalPage; $i++)
{
    $sel = $i == $page ? "selected" : "";
    echo "<option value='$i' $sel>$i</option>";
}
echo "</select>";
echo "<input type='button' class='but' style='width: 30px; height: 25px'  value=' > ' onclick='onNextPage()'>";
echo "&nbsp;dari $totalPage, jumlah $nData data";
echo "</div>";
?>

<input type="hidden" id="totalpage" value="<?= $totalPage ?>">
</body>
</html>