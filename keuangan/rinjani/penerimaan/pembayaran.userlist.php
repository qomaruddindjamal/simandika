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
require_once('../library/qsbuilder.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('pembayaran.header.func.php');

$idtahunbuku = $_REQUEST['idtahunbuku'];
$idkategori = $_REQUEST['idkategori'];
$idpenerimaan = $_REQUEST['idpenerimaan'];
$departemen = $_REQUEST['departemen'];

$userGroup = "siswa";
if ($idkategori == "CSWJB" || $idkategori == "CSSKR")
    $userGroup = "calonsiswa";

$info = "NIS";
if ($idkategori == "CSWJB" || $idkategori == "CSSKR")
    $info = "No Calon Siswa";

$db = new Db;
$db->TryOpenExit(true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pembayaran</title>
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
    <script language="javascript" src="pembayaran.userlist.js?r=<?=filemtime('pembayaran.userlist.js')?>"></script>
</head>
<body style="margin: 5px">

<input type="hidden" id="idkategori" value="<?=$idkategori?>">
<input type="hidden" id="departemen" value="<?=$departemen?>">
<input type="hidden" id="idtahunbuku" value="<?=$idtahunbuku?>">
<input type="hidden" id="idpenerimaan" value="<?=$idpenerimaan?>">
<input type="hidden" id="usergroup" value="<?=$userGroup?>">

<strong>Scan Barcode <?=$info?>:</strong><br>
<input name="txBarcode" id="txBarcode" type="text"
       class="inputbox"
       style="width: 200px; font-size: 18px;"
       onfocus="this.style.background = '#27d1e5'"
       onblur="this.style.background = '#FFFFFF'"
       onkeyup="return scanBarcode(event)">
<br>
<span id="spScanInfo"  style="color: red"></span>
<br>


<?php

$tab_relPath = "../library/";
if ($userGroup == "siswa")
    require_once ("../library/tabs.siswa.php");
else
    require_once ("../library/tabs.calonsiswa.php");
?>

</body>
</html>