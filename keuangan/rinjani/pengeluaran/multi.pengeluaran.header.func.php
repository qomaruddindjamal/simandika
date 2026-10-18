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

    echo "<select name='departemen' id='departemen' style='width:180px' class='inputbox' onchange='change_dep();'>";
    foreach($dep as $value)
    {
        if ($departemen == "")
            $departemen = $value;
        echo "<option value='$value' " . StringIsSelected($value, $departemen) . ">$value</option>";
    }
    echo "</select>";
}

function ShowAccYear($db)
{
    global $departemen;

    $sql = "SELECT replid AS id, tahunbuku
              FROM jbsfina.tahunbuku
             WHERE aktif = 1
               AND departemen='$departemen'";
    $result = $db->QueryDb($sql);
    if (mysqli_num_rows($result) > 0)
    {
        $row = mysqli_fetch_array($result);
        echo "<input type='text' name='tahunbuku' id='tahunbuku' readonly class='inputbox' style='background-color:#dedede; width: 150px;' value='$row[tahunbuku]'>";
        echo "<input type='hidden' name='idtahunbuku' id='idtahunbuku' value='$row[id]'>";
    }
    else
    {
        echo "<input type='text' name='tahunbuku' id='tahunbuku' readonly class='inputbox' style='background-color:#dedede; width: 150px;' value=''>";
        echo "<input type='hidden' name='idtahunbuku' id='idtahunbuku' value='0'>";
    }
}
?>