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
function ShowSelectDepartemenTabunganPegawai($db)
{
    global $departemen;

    try
    {
        echo "<select name='departemen' id='departemen' onChange='onChangeDept()' class='inputbox' style='width:200px'>";
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
        echo Msg::InfoError($ex->getMessage(), "kncy0");
    }
}

function ShowTahunBukuTabunganPegawai($db)
{
    global $departemen;

    $idTahunBuku = 0;
    $tahunBuku = "";
    $sql = "SELECT replid, tahunbuku 
              FROM tahunbuku 
             WHERE departemen = '$departemen'
               AND aktif = 1";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        $idTahunBuku = $row[0];
        $tahunBuku = $row[1];
    }
    echo "<input type='text' id='tahunbuku' class='inputbox' style='width: 180px; background-color: #ededed' readonly value='$tahunBuku'>";
    echo "<input type='hidden' id='idtahunbuku' value='$idTahunBuku'>";
}

function ShowSelectTabunganPegawai($db)
{
    global $departemen;

    $sql = "SELECT replid, nama, info2
              FROM jbsfina.datatabunganp
             WHERE departemen = '$departemen'
               AND aktif = 1
             ORDER BY nama";
    $res = $db->QueryDb($sql);
    echo "<select name='tabungan' id='tabungan' onChange='showBlankPage()' class='inputbox' style='width:490px'>";
    while($row = mysqli_fetch_row($res))
    {
        $ls = [ $row[0], $row[1], $row[2] ];
        $base64 = base64_encode(json_encode($ls));
        echo "<option value='$base64'>$row[1]</option>";
    }
    echo "</select>";
}

?>