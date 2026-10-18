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
require_once('../include/config.php');
require_once('../library/common.func.php');
require_once('../include/db.onfunc.php');
require_once('../library/msg.php');
require_once('../library/departemen.php');
require_once('../util/peek.php');
require_once('tutupbuku2.func.php');

if (getLevel() == 2)
{
    echo "<script>";
    echo "alert('Maaf, anda tidak berhak mengakses halaman ini!');";
    echo "window.history.back();";
    echo "</script>";
    exit();
}

$db = new Db();
$db->TryOpenExit();

$_SESSION["TBSTEP"] = 1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Tutup Buku</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="../script/dateutil.js" ></script>
    <script language="javascript" src="../script/stringutil.js" ></script>
    <script language="javascript" src="../script/qsbuilder.js?<?=filemtime('../script/qsbuilder.js')?>" ></script>
    <script language="javascript" src="tutupbuku2.js?<?=filemtime('tutupbuku2.js')?>"></script>
</head>
<body>
<table border="0" width="100%">
<tr>
    <td align="center" valign="top">

    <table border="0" width="95%" align="center">
    <tr>
        <td align="right" valign="top">

            <img class="help-icon-1" title="bantuan" onclick="showHelp()">
            <span class="pageTitle">Tutup Buku</span><br>
            <a class="pageLink" href="referensi.php">Referensi</a>&nbsp&gt;&nbsp
            <span class="pageLinkCurrent">Tutup Buku</span>

        </td>
    </tr>
    </table>
    <br>

    <table width="70%" align="center" border="0" cellpadding="7" cellspacing="0" style="border-color:#ccc">
    <tr style="background-color:#ccc">
        <td align="center" width="25%">
            <span style="font-size:16px; color:#333">Langkah 1 dari 3</span>
        </td>
        <td align="left" valign="middle">
            <span style="font-size:12px; color:#333; font-style: italic">
                Menentukan tanggal tutup buku, memeriksa laporan neraca
            </span>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="left" height="300" valign="top" style="background-color:#efefef">

            <table border="0" cellpadding="2" cellspacing="2" width="70%" align="left" background="">
            <tr>
                <td align="left" width="18%"><strong>Departemen:</strong></td>
                <td align="left">
<?php
                ShowSelectDepartemen_TB($db);
?>
                </td>
            </tr>
            <tr>
                <td align="left"><strong>Tanggal Tutup Buku:</strong></td>
                <td align="left">
                    <input type="text" id="txttutup" readonly size="15"
                           value="<?= LongDateFormat(date("Y-m-d")) ?>"
                           class="inputbox" style="background-color:#ddd; width: 150px;">&nbsp;
                    <input type="hidden" id="ttutup" value="<?= date("Y-m-d") ?>">
                    <a href="#" onclick="showPilihTanggal()">
                        <img src="../images/ico/calendar.png" border="0" id="bttutup"/>
                    </a>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input class="dialogButtonPositive" type="button" value="Lanjut >" id="lanjut"
                           onclick="prosesTutupBuku1()">&nbsp;
                    <input class="dialogButtonNegative" type="button" value="Batal" id="batal"
                           onclick="document.location.href = 'referensi.php'">
                </td>
            </tr>
            </table>

            <br><br><br>
            <table id="tabError" style="background-color:#dfdfdf; visibility: hidden" width="80%" align="center">
            <tr>
                <td align="center" height="80" valign="middle">
                    <span style="color:#F00; font-weight: bold" id="spError">

                    </span>
                </td>
            </tr>
            </table>

        </td>
    </tr>
    </table>

    </td>
</tr>
</table>

<div id="divDialog"></div>
<div id="toast-container"></div>
<div id="divHelpDialog" class="help-dialog"></div>

</body>
</html>