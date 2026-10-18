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
require_once ("../include/config.php");
require_once ("../include/db.onfunc.php");
require_once ("../library/departemen.php");
require_once ("../library/msg.php");

function ShowSelectDepartemen($db)
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
        echo Msg::InfoError($ex->getMessage(), "krt72");
    }
}

function ShowTahunBuku($db)
{
    global $departemen;

    try
    {
        $sql = "SELECT * 
                  FROM tahunbuku 
                 WHERE departemen='$departemen' 
                 ORDER BY replid";
        $res =  $db->QueryDb($sql);
        if (mysqli_num_rows($res) == 0)
        {
            echo "<table width='100%' border='0' align='center'>";
            echo "<tr>";
            echo "<td align='center' valign='middle' height='200'>";
            echo "<font size = '2' color ='red'><b>Tidak ditemukan adanya data.";
            echo "<br />Klik &nbsp;<a href='JavaScript:tambah()' ><font size = '2' color ='green'>di sini</font></a>&nbsp;untuk membuat tahun buku baru.";
            echo "</b></font>";
            echo "</td>";
            echo "</tr>";
            echo "</table>";

            return;
        }

        echo "<table id='table' class='tab' border='1' style='border-collapse:collapse' width='95%' align='center'>";
        echo "<tr height='30' align='center'>";
        echo "<td class='header' width='5%'>No</td>";
        echo "<td class='header' width='12%'>Tahun Buku</td>";
        echo "<td class='header' width='15%'>Tanggal Mulai</td>";
        echo "<td class='header' width='15%'>Awalan Kuitansi</td>";
        echo "<td class='header' width='40%'>Keterangan</td>";
        echo "<td class='header colButton' width='12%'>&nbsp;</td>";
        echo "</tr>";

        $cnt = 0;
        while($row = mysqli_fetch_array($res))
        {
            $cnt += 1;
            $tglMulai = LongDateFormat($row["tanggalmulai"]);

            echo "<tr style='height: 25px;'>";
            echo "<td align='center' class='numberColumn'>$cnt</td>";
            echo "<td align='center'>$row[tahunbuku]</td>";
            echo "<td align='center'>$tglMulai</td>";
            echo "<td align='center'>$row[awalan]</td>";
            echo "<td>$row[keterangan]</td>";
            echo "<td class='colButton' align='center'>";

            if ($row["aktif"] == 1)
                echo "<a href='#' onClick='ubah($row[replid])'><img src='../images/ico/ubah.png' border='0'/></a>";
            else
                echo "&nbsp;";

            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k77dd");
    }
}
?>