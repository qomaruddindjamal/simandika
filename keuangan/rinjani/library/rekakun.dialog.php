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
require_once('../include/rupiah.php');
require_once('../include/config.php');
require_once('../library/msg.php');
require_once('../include/db.onfunc.php');
require_once('rekakun.dialog.func.php');
require_once('rekakun.func.php');

$container = isset($_REQUEST["container"]) ? $_REQUEST["container"] : "self";
$kategori = $_REQUEST["kategori"];
$subKategori = $_REQUEST["subkategori"];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Kode Rekening</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js" ></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="../script/request.factory.js?r=<?=filemtime('../script/request.factory.js')?>"></script>
    <script language="javascript" src="rekakun.dialog.js?r=<?=filemtime('rekakun.dialog.js')?>"></script>
</head>

<body >
<input type="hidden" id="container" value="<?=$container?>">
<input type="hidden" id="kategori" value="<?=$kategori?>">
<input type="hidden" id="subkategori" value="<?=$subKategori?>">
<table border="0" width="100%" cellpadding="10">
<tr>
    <td align="left" valign="top">

    <span style="font-size: 14pt">Kode Rekening <?= $kategori ?></span><br><br>


    <fieldset>

        <legend><span id="spTitle" style="font-weight: bold; line-height: 25px;">Tambah Kode Rekening</span></legend>
        <input type="hidden" id="idkoderek" name="idkoderek" value="0" />
        <input type="hidden" id="kategori" name="kategori" value="<?=$kategori?>" />

        <div>
            <div style="margin-top: 5px">
                <span style="display:inline-block; width: 70px"><b>Kode</b></span>
                <input type="text" class="inputbox" id="kode" name="kode" style="width: 80px" maxlength="15">
                &nbsp;&nbsp;<b>Nama</b>&nbsp;
                <input type="text" class="inputbox" id="nama" name="nama" style="width: 200px" maxlength="100">
                <img src="../images/ico/warning16.png" id="imWarning" style="visibility: hidden" title="sudah digunakan"/>

            </div>
            <div style="margin-top: 5px">
                <span style="display:inline-block; width: 70px">Keterangan</span>
                <input type="text" class="inputbox" id="keterangan" name="keterangan" style="width: 200px" maxlength="100">&nbsp;&nbsp;
                <input type="button" class="dialogButtonPositive" value="Simpan" style="width: 80px; height: 25px;"  onclick="simpanKodeRek()">
                <input type="button" id="btKodeRekBaru" name="btKodeRekBaru" class="dialogButtonGreen" value="Baru" style="width: 40px; height: 25px; visibility: hidden"  onclick="setKodeRekBaru()">
            </div>
        </div>

    </fieldset>

    <br>

    <fieldset>

        <legend><span style="font-weight: bold; line-height: 25px;">Daftar Kode Rekening</span></legend>

        <div id="divDaftar" style="width: 98%; height: 340px; overflow: auto;">

<?php
            DaftarRekAkun($kategori);
?>

        </div>

    </fieldset>

    </td>
</tr>
</table>

<div id="divDialog"></div>
<div id="toast-container"></div>

</body>

</html>
