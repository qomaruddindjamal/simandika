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
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('kelompok.dialog.func.php');

$db = new Db;
$db->TryOpenExit(true);

$idgroup = RequestData("idgroup", 0);
$namagroup = RequestData("namagroup", "");
$id = RequestData("id", 0);

$nama = "";
$keterangan = "";

$title = "Tambah Kelompok Barang";
if ($id != 0)
{
    $title = "Ubah Kelompok Barang";
    LoadValues($db);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= $title ?></title>
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
    <script language="javascript" src="kelompok.dialog.js?r=<?=filemtime('kelompok.dialog.js')?>"></script>
</head>
<body style="margin: 10px">

<span class="dialogTitle"><?=$title?></span><br><br>
<input type="hidden" id="idgroup" value="<?=$idgroup?>">
<input type="hidden" id="id" value="<?=$id?>">

<table cellpadding="5" cellspacing="0">
<tr>
    <td>Kategori</td>
    <td>
        <input id="kategori" readonly type="text" class="inputbox" style="width: 250px; background-color: #ededed;"
               maxlength="100" value="<?=$namagroup?>">
    </td>
</tr>
<tr>
    <td>Kelompok<?=$tag_mandatory?></td>
    <td>
        <input id="nama" type="text" class="inputbox" style="width: 250px" maxlength="100" value="<?=$nama?>"><br>
        <span style="color: #666; font-size: 10px">misalnya Mobil, Motor, PC, Laptop</span>
    </td>
</tr>
<tr>
    <td>Keterangan</td>
    <td>
        <textarea rows="3" cols="40" class="inputbox" maxlength="255" id="keterangan"><?=$keterangan?></textarea>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <br>
        <input type="button" class="dialogButtonPositive" value="Simpan" onclick="simpanKelompok()">
        <input type="button" class="dialogButtonNegative" value="Tutup" onclick="window.close()">
        <br>
        <span id="spInfo"></span>
    </td>
</tr>
</table>

</body>
</html>