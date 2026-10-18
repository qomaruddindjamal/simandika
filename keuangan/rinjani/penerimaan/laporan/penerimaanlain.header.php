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
require_once('../../include/sessioninfo.php');
require_once('../../include/sessionchecker.php');
require_once('../../library/common.func.php');
require_once('../../include/config.php');
require_once('../../include/db.onfunc.php');
require_once('../../library/departemen.php');
require_once('../../library/msg.php');
require_once('../../util/peek.php');
require_once('../../include/errorhandler.php');
require_once('penerimaanlain.header.func.php');

$db = new Db;
$db->TryOpenExit(true);

$sql = "SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 30 DAY), '%Y-%m-%d'),
               DATE_FORMAT(CURDATE(), '%Y-%m-%d')";
$res = $db->QueryDb($sql);
$row = mysqli_fetch_row($res);
$dt1 = $row[0];
$fdt1 = LongDateFormat($dt1);
$dt2 = $row[1];
$fdt2 = LongDateFormat($dt2);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Penerimaan Lain</title>
    <link rel="stylesheet" type="text/css" href="../../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../../script/tables.js"></script>
    <script language="javascript" src="../../script/tools.js"></script>
    <script language="javascript" src="../../script/toast.js"></script>
    <script language="javascript" src="../../script/vldr.js"></script>
    <script language="javascript" src="../../script/stringutil.js"></script>
    <script language="javascript" src="../../script/dateutil.js"></script>
    <script language="javascript" src="../../script/qsbuilder.js"></script>
    <script language="javascript" src="penerimaanlain.header.js?r=<?=filemtime('penerimaanlain.header.js')?>"></script>
</head>
<body style="margin: 0">

<table border="0" width="98%" height="100%" align="center">
<tr>
    <td align="left" valign="top" width="70%">

        <table border="0" cellspacing="0" cellpadding="2">
        <tr>
            <td style="width: 100px"><strong>Departemen:</strong></td>
            <td>
<?php
                $departemen = RequestData("departemen", "");
                ShowSelectDepartemenPenerimaanLain($db)
?>
                &nbsp;&nbsp;<strong>Penerimaan:</strong>&nbsp;
                <span id="spPenerimaan">
<?php
                ShowSelectPenerimaanLain($db);
?>
                </span>
            </td>
            <td rowspan="2" style="width: 120px" align="center">
                <a href="#" onclick="showLaporan()">
                    <img src="../../images/view.png" border="0" height="48" width="48"/>
                </a>
            </td>
        </tr>
        <tr>
            <td><b>Tanggal</b></td>
            <td>
                <?php
                echo "<input type='text' id='ftanggal1' onclick='showPilihTanggal(1, \"$dt1\")' readonly size='15' value='$fdt1' class='inputbox' style='background-color:#ddd; width: 150px;'>&nbsp;";
                echo "<input type='hidden' id='tanggal1' value='$dt1'>";
                echo "<a href='#' onclick='showPilihTanggal(1, \"$dt1\")'>";
                echo "<img src='../../images/ico/calendar.png' border='0'/>";
                echo "</a>&nbsp;&nbsp;s/d&nbsp;&nbsp;";
                echo "<input type='text' id='ftanggal2' onclick='showPilihTanggal(2, \"$dt2\")' readonly size='15' value='$fdt2' class='inputbox' style='background-color:#ddd; width: 150px;'>&nbsp;";
                echo "<input type='hidden' id='tanggal2' value='$dt2'>";
                echo "<a href='#' onclick='showPilihTanggal(2, \"$dt2\")'>";
                echo "<img src='../../images/ico/calendar.png' border='0'/>";
                ?>
            </td>
        </tr>
        </table>


    </td>
    <td align="right" valign="top" width="30%">
        <img class="help-icon-1" src="../../images/help32.png" title="bantuan" onclick="showHelp()">
        <span class="pageTitle">Penerimaan Lain</span><br>
        <a class="pageLink" href="../penerimaan.php" target="_parent"><b>Penerimaan</b></a>&nbsp;&gt;&nbsp;
        <span class="pageLinkCurrent">Penerimaan Lain</td>
    </td>
</tr>
</table>

</body>
</html>