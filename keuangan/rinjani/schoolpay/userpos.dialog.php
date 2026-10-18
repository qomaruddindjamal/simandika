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
require_once('../include/errorhandler.php');
require_once('userpos.dialog.func.php');

$db = new Db();
$db->TryOpenExit();

$userReplid = $_REQUEST["replid"];

$userId = "";
$userName = "";
$origPass = "";
$keterangan = "";

LoadValue($db);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Staf Vendor SchoolPay</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/tooltips.rinjani.css?<?=filemtime('../style/tooltips.rinjani.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/tooltips.rinjani.js?r=<?=filemtime('../script/tooltips.rinjani.js')?>"></script>
    <script language="javascript" src="../script/request.factory.js?r=<?=filemtime('../script/request.factory.js')?>"></script>
    <script language="javascript" src="userpos.dialog.js?r=<?=filemtime('userpos.dialog.js')?>"></script>
</head>

<body>
<table border="0" width="100%" cellpadding="10">
<tr>
    <td align="left" valign="top">

        <span style="font-size: 14pt">Staf Vendor SchoolPay</span><br><br>

        <input type="hidden" id="userreplid" name="userreplid" value="<?=$userReplid?>">
        <input type="hidden" id="origpass" name="origpass" value="<?=$origPass?>">

        <table border="0" width="100%" cellpadding="5" cellspacing="2">
        <tr>
            <td align="right" width="15%">User Id<?=$tag_mandatory?></td>
            <td align="left" width="*">
                <input type="text" id="userid" maxlength="50" class="inputbox" name="userid" value="<?= $userId ?>" >
                <img src="../images/help32.png" class="tooltip-icon" title="informasi"
                     onclick="showTooltip(this, '../help/sp_tt_userid.html', 'auto', 400)">
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                Nama<?=$tag_mandatory?>
            </td>
            <td align="left" valign="top">
                <input type="text" id="username" name="username" class="inputbox" maxlength="255" size="45" value="<?= $userName ?>">
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                Password<?=$tag_mandatory?>
            </td>
            <td align="left" valign="top">
                <input type="password" id="password1" size="25" class="inputbox" maxlength="255" name="password1" value="<?= $origPass ?>"><br>
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                Konfirmasi<br>Password<?=$tag_mandatory?>
            </td>
            <td align="left" valign="top">
                <input type="password" id="password2" size="25" class="inputbox" maxlength="255" name="password2" value="<?= $origPass ?>"><br>
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">
                Keterangan
            </td>
            <td align="left" valign="top">
                <textarea rows="2" cols="45" id="keterangan" class="inputbox" name="keterangan"><?= $keterangan ?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <br>
                <input type="button" class="dialogButtonPositive" value="Simpan" style="width: 80px; height: 30px;"  onclick="simpanPetugas()">
                <input type="button" class="dialogButtonNegative" value="Tutup" style="width: 80px; height: 30px;"  onclick="window.close()">
            </td>
        </tr>
        </table>

    </td>
</tr>
</table>

<div id="tooltip" class="tooltip hidden" aria-hidden="true">
    <button class="tooltip-close">&times;</button>
    <div class="tooltip-arrow"></div>
    <div class="tooltip-content"></div>
</div>

</body>
</html>