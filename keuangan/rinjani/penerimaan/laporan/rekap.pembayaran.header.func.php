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
function ShowSelectDepartemenRekapPenerimaan($db)
{
    global $departemen;

    $dep = getDepartemen($db, getAccess());

    echo "<select name='departemen' id='departemen' style='width:180px' class='inputbox' onchange='onChangeDept();'>";
    echo "<option value='ALL'>(Semua Departemen)</option>";
    foreach($dep as $value)
    {
        echo "<option value='$value'>$value</option>";
    }
    echo "</select>";
}


function ShowSelectKategoriRekapPenerimaan($db)
{
    global $idKategori;

    $sql = "SELECT kode, kategori 
              FROM jbsfina.kategoripenerimaan 
             ORDER BY urutan";
    $res = $db->QueryDb($sql);

    echo "<select id='kategori' onChange='onSelectionChange()' class='inputbox' style='width:150px'>";
    while($row = mysqli_fetch_row($res))
    {
        if ($idKategori == "")
            $idKategori = $row[0];

        $sel = $idKategori == $row[0] ? "selected" : "";
        echo "<option value='$row[0]' $sel>$row[1]</option>";
    }
    echo "</select>";
}

function ShowSelectPetugasRekapPenerimaan($db)
{
    echo "<select  class='inputbox' id='petugas' style='width:150px;' onchange='onSelectionChange()' >";
    echo "<option value='ALL'>(Semua Petugas)</option>";
    echo "<option value='landlord'>Administrator JIBAS</option>";

	$sql = "SELECT p.nip, p.nama
              FROM jbsuser.hakakses h, jbssdm.pegawai p, jbsuser.login l
             WHERE h.modul = 'KEUANGAN'
               AND h.login = l.login
               AND l.login = p.nip
             ORDER BY p.nama";
    $res = $db->QueryDb($sql);
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}
?>
