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
require_once('lokasidana.dialog.func.php');

$db = new Db();
$db->TryOpenExit();

$idLokasiDana = $_REQUEST["idlokasidana"];

$kode = "";
$nama = "";
$urutan = "";
$kelompok = "";
$keterangan = "";

$title = "Lokasi Dana";
if ($idLokasiDana > 0)
{
    $title = "Ubah Lokasi Dana";
    FetchLokasiDana($db, $idLokasiDana);
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
    <script language="javascript" src="lokasidana.dialog.js?r=<?= filemtime('lokasidana.dialog.js') ?>"></script>
</head>
<body style="padding: 10px">
<span class="dialogTitle"><?=$title?></span><br><br>
<input type="hidden" id="idlokasidana" value="<?=$idLokasiDana?>">
<table cellpadding="5" cellspacing="0" style="border-width: 0px;">
<tr>
    <td>Kode</td>
    <td>
        <input id="kode" maxlength="20" type="text" class="inputbox" style="width: 100px;" value="<?=$kode?>">
    </td>
</tr>
<tr>
    <td>Nama</td>
    <td>
        <input id="nama" maxlength="100" type="text" class="inputbox" style="width: 340px;" value="<?=$nama?>">
    </td>
</tr>
<tr>
    <td>Urutan</td>
    <td>
        <input id="urutan" maxlength="2" type="text" class="inputbox" style="width: 50px;" value="<?=$urutan?>">
    </td>
</tr>
<tr>
    <td>Kelompok</td>
    <td>
<?php
        ShowSelectKelompokLokasiDana();
?>
    </td>
</tr>
<tr>
    <td>Keterangan</td>
    <td>
        <textarea rows="3" cols="40" class="inputbox" id="keterangan"><?=$keterangan?></textarea>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <input type="button" class="dialogButtonPositive" value="Simpan" onclick="simpanLokasiDana()">
        <input type="button" class="dialogButtonNegative" value="Tutup" onclick="window.close()">
    </td>
</tr>
</table>

<div id="toast-container"></div>

</body>
</html>
