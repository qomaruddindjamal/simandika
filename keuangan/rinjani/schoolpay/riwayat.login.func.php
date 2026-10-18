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
$yrNow = date('Y');
$mnNow = date('n');
$dyNow = date('j');

function ShowCbVendor($db)
{
    $sql = "SELECT vendorid, nama 
              FROM jbsfina.vendor
             WHERE aktif = 1
             ORDER BY nama";
    $res = $db->QueryDb($sql);

    echo "<select id='vendor' name='vendor' onchange='clearReport()' class='inputbox' style='width: 250px'>";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}

function ShowRiwayatLogin($showMenu)
{
    $db = new Db();
    try
    {
        $db->Open();

        $vendorId = $_REQUEST["vendorid"];
        $tanggal = $_REQUEST["tanggal"];

        $ls = explode("-", $tanggal);
        $thn = (int) $ls[0];
        $bln = (int) $ls[1];
        $tgl = (int) $ls[2];

        $sql = "SELECT v.nama AS vendor, u.nama AS petugas, DATE_FORMAT(ul.logtime, '%d-%b-%Y %H:%i') AS logtime, ul.localip, ul.device
                  FROM jbsfina.userposlog ul, jbsfina.vendor v, jbsfina.userpos u
                 WHERE ul.vendorid = v.vendorid
                   AND ul.userid = u.userid
                   AND YEAR(ul.logtime) = $thn 
                   AND MONTH(ul.logtime) = $bln
                   AND DAY(ul.logtime) = $tgl
                   AND v.vendorid = '$vendorId'
                 ORDER BY logtime DESC";

        $res = $db->QueryDb($sql);

        $no = 0;
        echo "<br>";
        if ($showMenu)
        {
            echo "<a href='#' onclick='cetakReport()'><img src='../images/ico/print.png' border='0'>&nbsp;cetak</a>&nbsp;&nbsp;";
            echo "<a href='#' onclick='excelReport()'><img src='../images/ico/excel.png' border='0'>&nbsp;excel</a>";
        }
        echo "<table id='table' border='1' cellpadding='5' cellspacing='0' class='tab' style='border-width: 1px;'>";
        echo "<tr style='height: 30px'>";
        echo "<td align='center' class='header' width='40'>No</td>";
        echo "<td align='left' class='header' width='150'>Waktu</td>";
        echo "<td align='left' class='header' width='180'>Petugas</td>";
        echo "<td align='left' class='header' width='180'>IP Address</td>";
        echo "<td align='left' class='header' width='220'>Perangkat</td>";
        echo "</tr>";

        while($row = mysqli_fetch_array($res))
        {
            $no += 1;

            echo "<tr style='height: 30px'>";
            echo "<td align='center' class='numberColumn'>$no</td>";
            echo "<td align='left'>$row[logtime]</td>";
            echo "<td align='left'>$row[petugas]</td>";
            echo "<td align='left'>$row[localip]</td>";
            echo "<td align='left'>$row[device]</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kmsmx");
    }
    finally
    {
        $db->Close();
    }


}
?>