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
require_once('bayarsiswa.kelas.header.func.php');

$db = new Db;
$db->TryOpenExit(true);

$sql = "SELECT DATE_FORMAT(CURDATE(), '%Y-%m-%d')";
$res = $db->QueryDb($sql);
$row = mysqli_fetch_row($res);
$dt1 = $row[0];
$fdt1 = LongDateFormat($dt1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Tunggakan Siswa Per Kelas</title>
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
    <script language="javascript" src="bayarsiswa.tunggak.header.js?r=<?=filemtime('bayarsiswa.tunggak.header.js')?>"></script>
</head>
<body style="margin: 0 20px">

<table border="0" width="100%" height="100%">
<tr>
    <td align="left" valign="top" width="70%">

    <table border="0" cellspacing="0" cellpadding="2">
    <tr>
        <td style="width: 100px"><strong>Departemen:</strong></td>
        <td>
<?php
            $departemen = RequestData("departemen", "");
            ShowSelectDepartemenLapBayarSiswa($db)
?>
            &nbsp;&nbsp;<strong>Kelas:</strong>&nbsp;&nbsp;
            <span id="spTingkat">
<?php
            $idTingkat = "";
            ShowSelectTingkatLapBayarSiswa($db);
?>
          </span>
          <span id="spKelas">
<?php
            $idKelas = "";
            ShowSelectKelasLapBayarSiswa($db);
?>
          </span>
        </td>
        <td rowspan="3" style="width: 120px" align="center">
            <a href="#" onclick="showLaporan()">
                <img src="../../images/view.png" border="0" height="48" width="48"/>
            </a>
        </td>
    </tr>
    <tr>
        <td><b>Pembayaran</b></td>
        <td>
<?php
            $idKategori = "";
            ShowSelectKategoriLapBayarSiswa($db, "'JTT'");
?>
            <span id="spPenerimaan">
<?php
            ShowSelectPenerimaanLapBayarSiswa($db);
?>
            </span>

        </td>
    </tr>
    <tr>
        <td><b>Telat Bayar:</b></td>
        <td>
<?php
            echo "<input type='text' id='telat' value='30' maxlength='3' class='inputbox' style='width:60px; text-align:center'><strong> hari, dari </strong>";
            echo "<input type='text' id='ftanggal' onclick='showPilihTanggal(\"$dt1\")' readonly value='$fdt1' class='inputbox inputbox-readonly' style='width: 160px; text-align: center;'>&nbsp;";
            echo "<input type='hidden' id='tanggal' value='$dt1'>";
            echo "<a href='#' onclick='showPilihTanggal(\"$dt1\")'>";
            echo "<img src='../../images/ico/calendar.png' border='0'/>";
            echo "</a>";
?>
        </td>
    </tr>
    </table>


    </td>
    <td align="right" valign="top" width="30%">
        <img class="help-icon-1" src="../../images/help32.png" title="bantuan" onclick="showHelp()">
        <span class="pageTitle">Siswa yang Menunggak</span><br>
        <a class="pageLink" href="../penerimaan.php" target="_parent"><b>Penerimaan</b></a>&nbsp;&gt;&nbsp;
        <span class="pageLinkCurrent">Siswa yang Menunggak</td>
    </td>
</tr>
</table>

</body>
</html>