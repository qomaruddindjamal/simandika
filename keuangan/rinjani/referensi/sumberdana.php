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
require_once('../include/db.onfunc.php');
require_once('../library/msg.php');
require_once('../util/peek.php');
require_once('sumberdana.func.php');

if (getLevel() == 2)
{
    echo "<script>";
    echo "alert('Maaf, anda tidak berhak mengakses halaman ini!');";
    echo "window.history.back();";
    echo "</script>";
    exit();
}

$from = $_REQUEST['from'];
$sourcefrom = $_REQUEST['sourcefrom'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Sumber Dana</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/random.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="../script/qsbuilder.js?<?=filemtime('../script/qsbuilder.js')?>" ></script>
    <script language="javascript" src="sumberdana.js?<?=filemtime('sumberdana.js')?>"></script>
</head>
<body>
<input type="hidden" id="from" value="<?=$from?>">
<input type="hidden" id="sourcefrom" value="<?=$sourcefrom?>">

<table border="0" width="100%">
<tr>
    <td align="center" valign="top">

    <table border="0" width="95%" align="center">
    <tr>
        <td align="right" valign="top">

            <img class="help-icon-1" src="../images/help32.png" title="bantuan" onclick="showHelp()">
            <span class="pageTitle">Sumber Dana</span><br>
            <a class="pageLink" href="<?= $sourcefrom ?>"><?= $from ?></a>&nbsp&gt;&nbsp
            <span class="pageLinkCurrent">Sumber Dana</span>

        </td>
    </tr>
    </table>
    <br>

    <table border="0" cellpadding="0" cellspacing="0" width="95%" align="center">
    <tr>
        <td align="right" width="25%">

        </td>
        <td align="right" width="*">
            <a href="JavaScript:refresh()">
                <img src="../images/ico/refresh.png" border="0"/>&nbsp;refresh
            </a>&nbsp;&nbsp;
            <a href="JavaScript:cetak()">
                <img src="../images/ico/print.png" border="0"/>&nbsp;cetak
            </a>&nbsp;&nbsp;
            <a href="JavaScript:tambah()">
                <img src="../images/ico/tambah.png" border="0">&nbsp;tambah
            </a>
        </td>
    </tr>
    </table><br>

    <div id="dvTableContent">
<?php
    ShowTableSumberDana();
?>
    </div>

    </td>
</tr>
</table>

<div id="divDialog"></div>
<div id="toast-container"></div>
<div id="divHelpDialog" class="help-dialog"></div>

</body>
</html>