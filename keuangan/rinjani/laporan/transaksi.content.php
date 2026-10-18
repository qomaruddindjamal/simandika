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
require_once('../library/logger.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('transaksi.content.func.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idTahunBuku = RequestData("idtahunbuku", 0);
$namaTahunBuku = RequestData("namatahunbuku", "");
$tanggal1 = RequestData("tanggal1", date('Y-m-d'));
$tanggal2 = RequestData("tanggal2", date('Y-m-d'));
$page = RequestData("page", 1);
$urut = RequestData("urut", "nokas");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Transaksi Keuangan</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/dialogbox.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="transaksi.content.js?r=<?=filemtime('transaksi.content.js')?>"></script>
</head>
<body style="margin: 10px;">

<?php
$sql = "SELECT COUNT(nokas) 
          FROM jbsfina.transaksilog 
         WHERE departemen = '$departemen' 
           AND tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
           AND idtahunbuku = '$idTahunBuku'";
$nData = $db->FetchSingle($sql, 0);
if ($nData == 0)
{
    echo "<span style='color: maroon'>belum ada transaksi keuangan di tanggal terpilih</span>";
    exit();
}
$totalPage = ceil($nData / $nRowPerPage);
$startIndex = ($page - 1) * $nRowPerPage;
?>

<table border="0" width="100%" align="center">
<tr>
    <td align="right">
        <a href="JavaScript:refresh()"><img src="../images/ico/refresh.png" border="0">&nbsp;refresh</a>&nbsp;&nbsp;
        <a href="JavaScript:cetak()"><img src="../images/ico/print.png" border="0">&nbsp;cetak</a>&nbsp;&nbsp;
        <a href="JavaScript:excel()"><img src="../images/ico/excel.png" border="0">&nbsp;excel</a>&nbsp;
    </td>
</tr>
</table>

<div id="dvDaftarTransaksi">
<?php
    ShowTransactionList($db);
?>
</div>

<?php
echo "<div id='dvPageControl' style='width: 100%'>";
echo "Halaman&nbsp;&nbsp;";
echo "<input type='button' class='but' style='height:28px;' value='  <  ' onclick='onPrevPage()'>";
echo "<select id='page' class='inputbox' style='width: 50px' onchange='onChangePage()'>";
for ($i = 1; $i <= $totalPage; $i++)
{
    $sel = $i == $page ? "selected" : "";
    echo "<option value='$i' $sel>$i</option>";
}
echo "</select>";
echo "<input type='button' class='but' style='height:28px;' value='  >  ' onclick='onNextPage()'>";
echo "&nbsp;dari $totalPage, jumlah $nData data";
echo "</div>";
?>

<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idtahunbuku" value="<?= $idTahunBuku ?>">
<input type="hidden" id="namatahunbuku" value="<?= $namaTahunBuku ?>">
<input type="hidden" id="tanggal1" value="<?= $tanggal1 ?>">
<input type="hidden" id="tanggal2" value="<?= $tanggal2 ?>">
<input type="hidden" id="ndata" value="<?= $nData ?>">
<input type="hidden" id="totalpage" value="<?= $totalPage ?>">
<input type="hidden" id="urut" value="<?= $urut ?>">

</body>
</html>