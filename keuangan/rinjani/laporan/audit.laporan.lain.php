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
require_once('audit.laporan.lain.func.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idTahunBuku = RequestData("idtahunbuku", 0);
$namaTahunBuku = RequestData("namatahunbuku", "");
$tanggal1 = RequestData("tanggal1", date('Y-m-d'));
$tanggal2 = RequestData("tanggal2", date('Y-m-d'));
$lap = RequestData("lap", "");
$page = RequestData("page", 1);

$jenisLaporan = "Perubahan Data Penerimaan Lain";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Audit Perubahan Data</title>
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
    <script language="javascript" src="audit.laporan.lain.js?r=<?=filemtime('audit.laporan.lain.js')?>"></script>
</head>
<body>
<input type="hidden" id="departemen" value="<?=$departemen?>">
<input type="hidden" id="idtahunbuku" value="<?=$idTahunBuku?>">
<input type="hidden" id="namatahunbuku" value="<?=$namaTahunBuku?>">
<input type="hidden" id="tanggal1" value="<?=$tanggal1?>">
<input type="hidden" id="tanggal2" value="<?=$tanggal2?>">
<input type="hidden" id="lap" value="<?=$lap?>">
<input type="hidden" id="jenis" value="<?=$jenisLaporan?>">

<span style="font-size: 18px; color: #999"><?= $jenisLaporan ?></span>
<br><br>
<table width="100%" border="0" height="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td align="right">
            <a href="#" onClick="document.location.reload()"><img src="../images/ico/refresh.png" border="0">&nbsp;refresh</a>&nbsp;
            <a href="JavaScript:cetak()"><img src="../images/ico/print.png" border="0">&nbsp;cetak</a>&nbsp;
        </td>
    </tr>
</table>
<?php
$sql = "SELECT COUNT(a.replid) 
          FROM jbsfina.auditinfo a, jbsfina.jurnal j 
         WHERE a.info1 = j.replid 
           AND j.idtahunbuku = '$idTahunBuku' 
           AND a.departemen = '$departemen' 
           AND a.tanggal >= '$tanggal1 00:00:00' 
           AND a.tanggal <= '$tanggal2 23:59:59'
           AND a.sumber = '$lap'";
$nData = $db->FetchSingle($sql, 0);
$totalPage = ceil($nData / $nRowPerPage);
echo "<input type='hidden' id='totalpage' value='$totalPage'>";
?>

<div id="dvDaftarAudit">
    <?php
    ShowLaporanAuditPenerimaanLain($db);
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

</body>
</html>