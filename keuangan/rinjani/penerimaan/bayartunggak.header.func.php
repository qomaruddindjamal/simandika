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
function ShowSelectDept($db)
{
    global $departemen;

    $dep = getDepartemen($db, getAccess());

    echo "<select name='departemen' id='departemen' style='width:180px' class='inputbox' onchange='onChangeDept()'>";
    foreach($dep as $value)
    {
        if ($departemen == "")
            $departemen = $value;
        echo "<option value='$value' " . StringIsSelected($value, $departemen) . ">$value</option>";
    }
    echo "</select>";
}

function ShowPrevAccYear($db)
{
    global $departemen;

    $sql = "SELECT replid, tahunbuku
              FROM jbsfina.tahunbuku
             WHERE aktif <> 1
               AND departemen = '$departemen'
             ORDER BY replid DESC";
    $res = $db->QueryDb($sql);
    echo "<select id='tahunbuku' style='width:180px' class='inputbox' onchange='showBlankPage()'>";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}

function ShowSelectKategoriPenerimaan($db)
{
    global $idkategori;

    $sql = "SELECT kode, kategori 
              FROM jbsfina.kategoripenerimaan 
             WHERE kode IN ('JTT','CSWJB') 
            ORDER BY urutan";
    $res = $db->QueryDb($sql);
    echo "<select id='kategori' style='width:180px' class='inputbox' onchange='onChangeKategori()'>";
    while($row = mysqli_fetch_row($res))
    {
        if ($idkategori == "")
            $idkategori = $row[0];

        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}

function ShowSelectJenisPenerimaan($db)
{
    global $idkategori, $departemen;

    $sql = "SELECT replid, nama 
              FROM jbsfina.datapenerimaan 
             WHERE aktif = 1 
               AND idkategori = '$idkategori' 
               AND departemen = '$departemen' 
             ORDER BY nama DESC ";
    $res = $db->QueryDb($sql);
    echo "<select id='penerimaan' style='width:260px' class='inputbox' onchange='showBlankPage()'>";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}
?>