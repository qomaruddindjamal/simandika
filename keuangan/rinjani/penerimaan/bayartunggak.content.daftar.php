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
require_once('bayartunggak.content.daftar.func.php');

$idtahunbuku = $_REQUEST['idtahunbuku'];
$namatahunbuku = $_REQUEST['namatahunbuku'];
$idkategori = $_REQUEST['idkategori'];
$namakategori = $_REQUEST['namakategori'];
$idpenerimaan = $_REQUEST['idpenerimaan'];
$namapenerimaan = $_REQUEST['namapenerimaan'];
$departemen = $_REQUEST['departemen'];
$urut = $_REQUEST["urut"];

$kelompok = "Siswa";
$kelompokId = "NIS";
if ($idkategori == "CSWJB")
{
    $kelompok = "Calon Siswa";
    $kelompokId = "No Pendaftaran";
}

$db = new Db();
$db->TryOpenExit();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Pembayaran Sisa Tunggakan</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="bayartunggak.content.daftar.js?r=<?=filemtime('bayartunggak.content.daftar.js')?>"></script>
</head>

<body style="margin: 10px;">
<input type="hidden" id="idtahunbuku" value="<?=$idtahunbuku ?>" />
<input type="hidden" id="namatahunbuku" value="<?=$namatahunbuku ?>" />
<input type="hidden" id="idkategori" value="<?=$idkategori ?>" />
<input type="hidden" id="namakategori" value="<?=$namakategori ?>" />
<input type="hidden" id="idpenerimaan" value="<?=$idpenerimaan ?>" />
<input type="hidden" id="namapenerimaan" value="<?=$namapenerimaan ?>" />
<input type="hidden" id="departemen" value="<?=$departemen ?>" />
<input type="hidden" id="kelompok" value="<?=$kelompok ?>" />
<input type="hidden" id="urut" value="<?= $urut ?>" />

<strong>Scan Barcode <?=$kelompokId?>:</strong><br>
<input name="txBarcode" id="txBarcode" class="inputbox" type="text" style="width: 200px; font-size: 18px;"
       onfocus="this.style.background = '#27d1e5'"
       onblur="this.style.background = '#FFFFFF'"
       onkeyup="return scanBarcode(event)">
<br>
<span id="spScanInfo" name="spScanInfo" style="color: red"></span>
<br><br>

<?php
$page = 1;
if ($idkategori == "CSWJB")
    ShowDaftarCalonSiswaContainer();
else
    ShowDaftarSiswaContainer();
?>

<input type="hidden" id="urut" value="<?=$urut?>">


</body>
</html>