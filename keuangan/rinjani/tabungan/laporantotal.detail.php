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
require_once('../library/msg.php');
require_once('../library/rupiah.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('laporantotal.detail.func.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = $_REQUEST['departemen'];
$idtabungan = $_REQUEST['idtabungan'];
$namatabungan = $_REQUEST['namatabungan'];
$jenis = $_REQUEST['jenis'];
$kelompok = $_REQUEST['kelompok'];

$title = "RINCIAN TRANSAKSI " . strtoupper($jenis);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$title?></title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="laporantotal.detail.js?r=<?= filemtime('laporantotal.detail.js') ?>"></script>
</head>
<body style="margin: 10px;">

<center>
    <span class="dialogTitle"><?=$title?></span>
</center>
<br>

<table border='0'>
<tr>
    <td width='100' align='right'>Departemen:</td>
    <td width='200' align='left'><strong><?=$departemen?></strong></td>
    <td width='100' align='right'>Tabungan:</td>
    <td width='200' align='left'><strong><?=$namatabungan?></strong></td>
</tr>
</table>
<br><br>

<?php
$colJumlah = $jenis == "SETORAN" ? "SUM(t.kredit) AS jumlah" : "SUM(t.debet) AS jumlah";
if ($kelompok == "siswa")
{
    $sql = "SELECT $colJumlah, COUNT(t.replid)
              FROM jbsfina.tabungan t, jbsakad.siswa s 
             WHERE t.nis = s.nis
               AND idtabungan = '$idtabungan'";
}
else
{
    $sql = "SELECT $colJumlah, COUNT(t.replid)
              FROM jbsfina.tabunganp t, jbssdm.pegawai p 
             WHERE t.nip = p.nip
               AND idtabungan = '$idtabungan'";
}
if ($jenis == "SETORAN")
    $sql .= " AND t.kredit <> 0";
else
    $sql .= " AND t.debet <> 0";

$res = $db->QueryDb($sql);
$row = mysqli_fetch_row($res);
$totaljumlah = $row[0];
$ndata = $row[1];
$npage = ceil($ndata / $nRowPerPage);
?>
<input type="hidden" id="departemen" value="<?=$departemen?>">
<input type="hidden" id="idtabungan" value="<?=$idtabungan?>">
<input type="hidden" id="namatabungan" value="<?=$namatabungan?>">
<input type="hidden" id="jenis" value="<?=$jenis?>">
<input type="hidden" id="kelompok" value="<?=$kelompok?>">
<input type="hidden" id="ndata" value="<?=$ndata?>">
<input type="hidden" id="npage" value="<?=$npage?>">

<table border="0" cellpadding="2">
<tr>
    <td width="120">
        <span style="color: #666">Jumlah Data</span><br>
        <span style="font-size: 20px"><?= $ndata ?></span>
    </td>
    <td width="250">
        <span style="color: #666">Total Transaksi</span><br>
        <span style="font-size: 20px"><?= FormatRupiah($totaljumlah) ?></span>
    </td>
</tr>
</table>
<br>

<div id="dvContent">
<?php
    $page = 1;
    ShowRincianSaldoTabungan();
?>
</div>

<?php
echo "<div id='dvPageControl' style='width: 100%'>";
echo "Halaman&nbsp;";
echo "<input type='button' class='but' style='height: 28px; width: 28px' value=' < ' onclick='onPrevPage()'>";
echo "<select id='page' class='inputbox' style='width: 50px' onchange='onChangePage()'>";
for ($i = 1; $i <= $npage; $i++)
{
    $sel = $i == $page ? "selected" : "";
    echo "<option value='$i' $sel>$i</option>";
}
echo "</select>";
echo "<input type='button' class='but' style='height: 28px; width: 28px' value=' > ' onclick='onNextPage()'>";
echo "&nbsp;dari $npage, jumlah $ndata data";
echo "</div>";
?>
</body>
</html>