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
function ShowSelectDepartemenPenerimaanLain($db)
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

function ShowSelectPenerimaanLain($db)
{
    global $departemen;

    $sql = "SELECT replid, nama 
              FROM jbsfina.datapenerimaan 
             WHERE aktif = 1 
               AND idkategori = 'LNN' 
               AND departemen = '$departemen' 
             ORDER BY nama";
    $res = $db->QueryDb($sql);

    echo "<select id='penerimaan' style='width:180px' class='inputbox' onchange='showBlankPage();'>";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}
?>