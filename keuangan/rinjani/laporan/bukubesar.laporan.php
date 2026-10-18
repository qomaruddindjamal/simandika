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
require_once('../library/rupiah.php');
require_once('../library/msg.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('bukubesar.laporan.func.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = $_REQUEST["departemen"];
$idTahunBuku = $_REQUEST["idtahunbuku"];
$namaTahunBuku = $_REQUEST["namatahunbuku"];
$tanggal1 = $_REQUEST["tanggal1"];
$tanggal2 = $_REQUEST["tanggal2"];
$kategori = $_REQUEST["kategori"];
$koderek = $_REQUEST["koderek"];
$namarek = $_REQUEST["namarek"];
$page = $_REQUEST["page"];
$urut = $_REQUEST["urut"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Buku Besar</title>
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
    <script language="javascript" src="bukubesar.laporan.js?r=<?=filemtime('bukubesar.laporan.js')?>"></script>
</head>
<body>


<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
<tr>
    <td width="70%" align="left">
        <span style="font-size: 24px; font-family: 'Segoe UI', sans-serif; color: #333;"><?= "$koderek $namarek"?></span>
    </td>
    <td width="30%" align="right">
        <a href="#" onClick="document.location.reload()"><img src="../images/ico/refresh.png" border="0">&nbsp;refresh</a>&nbsp;&nbsp;
        <a href="JavaScript:cetak()"><img src="../images/ico/print.png" border="0">&nbsp;cetak</a>&nbsp;&nbsp;
        <a href="JavaScript:excel()"><img src="../images/ico/excel.png" border="0">&nbsp;excel</a>
    </td>
</tr>
</table>
<br>

<input type="hidden" id="departemen" value="<?=$departemen?>">
<input type="hidden" id="idtahunbuku" value="<?=$idTahunBuku?>">
<input type="hidden" id="namatahunbuku" value="<?=$namaTahunBuku?>">
<input type="hidden" id="tanggal1" value="<?=$tanggal1?>">
<input type="hidden" id="tanggal2" value="<?=$tanggal2?>">
<input type="hidden" id="kategori" value="<?=$kategori?>">
<input type="hidden" id="koderek" value="<?=$koderek?>">
<input type="hidden" id="namarek" value="<?=$namarek?>">
<input type="hidden" id="urut" value="<?=$urut?>">

<div id="dvLaporanJumlah">
<?php   $totalPage = 0;
        $nData = 0;
        ShowRekapBukuBesar($db); ?>
</div>

<br>

<div id="dvLaporanData">
<?php   ShowTransaksiBukuBesar($db); ?>
</div>

<div id="dvPageControl">
<?php   ShowPageControl()  ?>
</div>

</body>
</html>