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
function ShowSelectDepartemenRekapTunggakan($db)
{
    global $departemen;

    $dep = getDepartemen($db, getAccess());

    echo "<select name='departemen' id='departemen' style='width:180px' class='inputbox' onchange='onChangeDept();'>";
    foreach($dep as $value)
    {
        if ($departemen == "")
            $departemen = $value;
        $sel = $departemen == $value ? "selected" : "";
        echo "<option value='$value' $sel>$value</option>";
    }
    echo "</select>";
}

function ShowSelectTingkatRekapTunggakan($db)
{
    global $departemen, $idTingkat;

    $sql = "SELECT replid, tingkat 
              FROM jbsakad.tingkat 
             WHERE departemen = '$departemen' 
               AND aktif = 1 
             ORDER BY urutan";
    $res = $db->QueryDb($sql);

    echo "<select id='tingkat' onChange='onTingkatChange()' class='inputbox' style='width:120px'>";
    while($row = mysqli_fetch_row($res))
    {
        if ($idTingkat == "")
            $idTingkat = $row[0];

        $sel = $idTingkat == $row[0] ? "selected" : "";
        echo "<option value='$row[0]' $sel>$row[1]</option>";
    }
    echo "</select>";
}

function ShowSelectKelasRekapTunggakan($db)
{
    global $idTingkat, $idKelas;

    $sql = "SELECT k.replid, k.kelas 
              FROM jbsakad.kelas k, jbsakad.tahunajaran ta, jbsakad.tingkat ti 
             WHERE k.idtahunajaran = ta.replid 
               AND k.idtingkat = ti.replid 
               AND ti.replid = '$idTingkat' 
               AND k.aktif = 1 
               AND ta.aktif = 1 
             ORDER BY k.kelas";
    $res = $db->QueryDb($sql);

    echo "<select id='kelas' onChange='showBlankPage()' class='inputbox' style='width:200px'>";
    echo "<option value='-1' selected>(Semua Kelas)</option>";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}

function ShowSelectTahunBukuRekapTunggakan($db)
{
    global $departemen;

    $sql = "SELECT replid, tahunbuku, aktif 
              FROM jbsfina.tahunbuku 
             WHERE departemen = '$departemen' 
             ORDER BY replid DESC";
    $res = $db->QueryDb($sql);

    echo "<select id='tahunbuku' onChange='showBlankPage()' class='inputbox' style='width:200px'>";
    while($row = mysqli_fetch_row($res))
    {
        $A = "";
        if ($row[2] == 1)
            $A = "(A)";

        echo "<option value='$row[0]'>$row[1] $A</option>";
    }
    echo "</select>";
}
?>