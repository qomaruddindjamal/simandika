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
require_once('../include/sessionchecker.php');
require_once('../include/sessioninfo.php');
require_once('../library/common.func.php');
require_once('../include/config.php');
require_once('../library/rupiah.php');
require_once('../include/db.onfunc.php');
require_once('../library/departemen.php');
require_once('../library/stringbuilder.php');
require_once('../include/errorhandler.php');
require_once('vendor.user.dialog.func.php');

$db = new Db();
$db->TryOpenExit();

$vendorId = $_REQUEST["vendorid"];
$vendorName = GetVendorName($db, $vendorId);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Staf Vendor</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/request.factory.js?r=<?=filemtime('../script/request.factory.js')?>"></script>
    <script language="javascript" src="vendor.user.dialog.js?r=<?=filemtime('vendor.user.dialog.js')?>"></script>
</head>

<body>
<table border="0" width="100%" cellpadding="10">
<tr>
    <td align="left" valign="top">

        <span style="font-size: 14pt">Staf Vendor <?= $vendorId ?></span><br>
        <i><?= $vendorName ?></i><br><br>

        <input type="hidden" id="vendorid" name="vendorid" value="<?=$vendorId?>">

        <table border="0" width="100%" cellpadding="5" cellspacing="2">
        <tr>
            <td align="right" width="15%"><strong>Staf Vendor:</strong></td>
            <td align="left" width="*">
<?php           ShowSelectPetugas($db, $vendorId); ?>
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                <strong>Tingkat:</strong>
            </td>
            <td align="left" valign="top">
                <select id="tingkat" name="tingkat" class="inputbox" style="width: 250px">
                    <option value="1">Manager</option>
                    <option value="2" selected>Operator</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <br>
                <input type="button" class="dialogButtonPositive" value="Simpan" style="width: 80px; height: 30px;"  onclick="simpanPetugasVendor()">
                <input type="button" class="dialogButtonNegative" value="Tutup" style="width: 80px; height: 30px;"  onclick="window.close()">
            </td>
        </tr>
        </table>

    </td>
</tr>
</table>
</body>
</html>