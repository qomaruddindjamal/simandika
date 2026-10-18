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
require_once('../include/db.onpage.php');
require_once('../include/db.onfunc.php');
require_once('../util/peek.php');
require_once('tahunbuku2.dialog.func.php');

$db = new Db();
$db->TryOpenExit();

$idTahunBuku = $_REQUEST["idtahunbuku"];
$departemen = $_REQUEST["departemen"];

$tahunBuku = "";
$tglMulai = "";
$awalan = "";
$keterangan = "";

$title = "Tahun Buku Baru";
if ($idTahunBuku > 0)
{
    $title = "Ubah Tahun Buku";
    LoadValues($db);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$title?></title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/qsbuilder.js?r=<?= filemtime('../script/qsbuilder.js')?>"></script>
    <script language="javascript" src="../script/vldr.js?r=<?= filemtime('../script/vldr.js')?>"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/stringutil.js"></script>
    <script language="javascript" src="../script/dateutil.js"></script>
    <script language="javascript" src="tahunbuku2.dialog.js?r=<?= filemtime('tahunbuku2.dialog.js') ?>"></script>
</head>
<body style="padding: 10px">
<div id="toast-container"></div>
<span class="dialogTitle"><?=$title?></span><br><br>

<input type="hidden" id="idtahunbuku" value="<?=$idTahunBuku?>">

<table cellpadding="5" cellspacing="0" style="border-width: 0px;">
<tr>
    <td width="160">Departemen</td>
    <td width="500">
        <input type="text" id="departemen" class="inputbox"
               style="width: 180px; background-color: #ddd" readonly value="<?=$departemen?>">
    </td>
</tr>
<tr>
    <td>Tahun Buku<?=$tag_mandatory?></td>
    <td>
        <input id="tahunbuku" maxlength="100" type="text" class="inputbox" style="width: 180px;" value="<?=$tahunBuku?>">
    </td>
</tr>
<tr>
    <td>Tanggal Mulai<?=$tag_mandatory?></td>
    <td>
        <input id="txtglmulai" maxlength="100" type="text" class="inputbox" readonly style="width: 160px;" value="<?=LongDateFormat($tglMulai)?>">
        <input id="tglmulai" type="hidden" class="inputbox" value="<?=$tglMulai?>">
        <a href="JavaScript:showSelectDate()"><img src="../images/ico/calendar.png" border="0"></a>
    </td>
</tr>
<tr>
    <td>Awalan<?=$tag_mandatory?></td>
    <td>
        <input id="awalan" maxlength="5" type="text" class="inputbox" style="width: 180px;" value="<?=$awalan?>">
        <?= $imReadOnly ?>
    </td>
</tr>
<tr>
    <td>Keterangan</td>
    <td>
        <textarea rows="3" cols="30" class="inputbox" id="keterangan"><?=$keterangan?></textarea>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <input type="button" class="dialogButtonPositive" value="Simpan" onclick="simpanTahunBuku()">
        <input type="button" class="dialogButtonNegative" value="Tutup" onclick="window.close()">
    </td>
</tr>
</table>
</body>
</html>
