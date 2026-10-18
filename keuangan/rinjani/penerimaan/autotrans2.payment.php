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
require_once('../library/rupiah.php');
require_once('../include/errorhandler.php');
require_once('autotrans2.payment.func.php');

$_SESSION["autotransstep"] = 1;

$departemen = RequestData("departemen", "");
$selKelompok = RequestData("selkelompok", "");

$db = new Db();
$db->TryOpenExit();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="../style/style.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Batch Payment</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/rupiah2.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="../script/random.js"></script>
    <script language="javascript" src="autotrans2.payment3.js?<?=filemtime('autotrans2.payment3.js')?>"></script>
</head>

<body>

<table border="0" width="100%" align="center">
<tr><td align="center" valign="top">

    <table border="0" width="95%" align="center">
    <tr>
        <td align="right">
            <img class="help-icon-1"  src="../images/help32.png"  title="bantuan" onclick="showHelp()">
            <span class="pageTitle">Batch Payment</span><br>
            <a class="pageLink" href="penerimaan.php"><b>Penerimaan</b></a>&nbsp;&gt;&nbsp;
            <span class="pageLinkCurrent">Batch Payment
        </td>
    </tr>
    </table><br>

    <form name="main" method="post" action="autotrans2.payment.save.php" onsubmit="return validateSubmit()">
    <table width="100%" border="0" align="center">
    <tr style="height: 35px;">
        <td width="2%">&nbsp;</td>
        <td align="left" width="10%">Departemen:</td>
        <td width="*">
<?php
            ShowSelectDept($db);
?>
        Tahun Buku:&nbsp;
<?php
            ShowAccYear($db);
?>
        </td>
    </tr>
    <tr style="height: 35px;">
        <td width="2%">&nbsp;</td>
        <td>Nama&nbsp;<?=$tag_mandatory?></td>
        <td>
            <select id="selkelompok" class="inputbox" style="width: 100px" onchange="onSelKelompokChange()">
                <option value="siswa" <?= $selKelompok == "siswa" ? "selected" : "" ?>>Siswa</option>
                <option value="calonsiswa" <?= $selKelompok == "calonsiswa" ? "selected" : "" ?>>Calon Siswa</option>
            </select>
            <input type="hidden" name="kelompok" id="kelompok">
            <input type="text" name="noid" id="noid" readonly
                   class="inputbox inputbox-readonly" style="font-size: 14px; width: 120px;">
            <input type="text" name="nama" id="nama" readonly
                   class="inputbox inputbox-readonly" style="font-size: 14px; width: 200px">
            <input type="hidden" name="kelas" id="kelas">
            <input type="button" class="dialogButtonGray" value="..." onclick="SearchUser()">
            &nbsp;&nbsp;&nbsp;
            <i>Scan Barcode</i>&nbsp;&nbsp;
            <input name="txBarcode" id="txBarcode" type="text"
                   class="inputbox"
                   style="width: 200px; font-size: 18px;"
                   onfocus="this.style.background = '#27d1e5'"
                   onblur="this.style.background = '#FFFFFF'"
                   onkeyup="return scanBarcode(event)">
            <br>
            <span id="spScanInfo" name="spScanInfo" style="color: red"></span>
        </td>
    </tr>
    <tr style="height: 35px;">
        <td width="2%">&nbsp;</td>
        <td align="left">
            <span id="lbPengaturan" style="visibility: hidden;">Pilihan Batch Payment<?=$tag_mandatory?></span>
        </td>
        <td width="*">
            <div id="divPaymentSelect">

            </div>
            <div id="divPaymentInfo" style="font-style: italic">

            </div>
        </td>
    </tr>
    <tr>
        <td width="2%">&nbsp;</td>
        <td align="left">&nbsp;</td>
        <td width="*">
            <div id="divPaymentList">

            </div>
        </td>
    </tr>
    </table>
    </form>

</td></tr>
</table>

<div id="divHelpDialog" class="help-dialog"></div>

</body>

</html>
