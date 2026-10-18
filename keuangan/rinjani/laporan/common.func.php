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
function ShowSelectDepartemenLaporan($db)
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

function ShowSelectTahunBukuLaporan($db)
{
    global $departemen, $dataTahunBuku64;

    $sql = "SELECT replid, tahunbuku, aktif, tanggalmulai 
              FROM tahunbuku 
             WHERE departemen = '$departemen' 
             ORDER BY replid DESC";
    $res = $db->QueryDb($sql);

    echo "<select id='tahunbuku' onchange='onTahunBukuChange()' class='inputbox' style='width:160px'>";
    while ($row = mysqli_fetch_row($res))
    {
        $A = "";
        if ($row[2] == 1)
            $A = "(A)";

        $ls = array($row[0], $row[1], $row[2], $row[3]);
        $data64 = base64_encode(json_encode($ls));

        if ($dataTahunBuku64 == "")
            $dataTahunBuku64 = $data64;

        echo  "<option value='$data64'>$row[1] $A</option>";
    }
    echo "</select>";
}

function ShowSelectKategoriAkunLaporan($db)
{
    $sql = "SELECT kategori 
              FROM jbsfina.katerekakun 
             ORDER BY kategori";
    $res = $db->QueryDb($sql);

    echo "<select id='kategori' onchange='onKategoriAkunChange()' class='inputbox' style='width: 320px'>";
    echo "<option value=\"ALL\">(Semua Kategori)</option>";
    while ($row = mysqli_fetch_row($res))
    {
        echo  "<option value='$row[0]'>$row[0]</option>";
    }
    echo "</select>";
}
?>