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
require_once ("../library/msg.php");

function ShowSelectDepartemenCariJurnal($db)
{
    global $departemen;

    try
    {
        echo "<select name='departemen' id='departemen' onChange='change_dep()' class='inputbox' style='width:200px'>";
        $dep = getDepartemen($db, getAccess());
        foreach($dep as $value)
        {
            if ($departemen == "") $departemen = $value;
            $sel = $departemen == $value ? "selected" : "";
            echo "<option value='$value' $sel>$value</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kv16p");
    }
}

function ShowTahunBukuCariJurnal($db)
{
    global $departemen;

    try
    {
        $idTahunBuku = 0;
        $tahunBuku = "";

        $sql = "SELECT replid, tahunbuku 
                  FROM jbsfina.tahunbuku 
                 WHERE aktif = 1 
                   AND departemen = '$departemen'";
        $res = $db->QueryDb($sql);
        if ($row = mysqli_fetch_row($res))
        {
            $idTahunBuku = $row[0];
            $tahunBuku = $row[1];
        }

        echo "<input type='text' id='tahunbuku' style='width: 180px; background-color: #efefef' readonly class='inputbox' value='$tahunBuku'>";
        echo "<input type='hidden' id='idtahunbuku' value='$idTahunBuku'>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kqp73");
    }
}

function ShowTanggalCariJurnal($db)
{
    try
    {
        $sql = "SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 30 DAY), '%Y-%m-%d'),
                       DATE_FORMAT(CURDATE(), '%Y-%m-%d')";
        $res = $db->QueryDb($sql);
        $row = mysqli_fetch_row($res);
        $dt1 = $row[0];
        $fdt1 = LongDateFormat($dt1);
        $dt2 = $row[1];
        $fdt2 = LongDateFormat($dt2);

        echo "<input type='text' id='ftanggal1' onclick='showPilihTanggal(1, \"$dt1\")' readonly size='15' value='$fdt1' class='inputbox' style='background-color:#ddd; width: 150px;'>&nbsp;";
        echo "<input type='hidden' id='tanggal1' value='$dt1'>";
        echo "<a href='#' onclick='showPilihTanggal(1, \"$dt1\")'>";
        echo "<img src='../images/ico/calendar.png' border='0'/>";
        echo "</a>&nbsp;&nbsp;s/d&nbsp;&nbsp;";
        echo "<input type='text' id='ftanggal2' onclick='showPilihTanggal(2, \"$dt2\")' readonly size='15' value='$fdt2' class='inputbox' style='background-color:#ddd; width: 150px;'>&nbsp;";
        echo "<input type='hidden' id='tanggal2' value='$dt2'>";
        echo "<a href='#' onclick='showPilihTanggal(2, \"$dt2\")'>";
        echo "<img src='../images/ico/calendar.png' border='0'/>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k87hc");
    }
}
?>