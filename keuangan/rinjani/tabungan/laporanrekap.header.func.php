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
function ShowSelectPetugas($db)
{
    $sql = "SELECT p.nip, p.nama
              FROM jbsuser.hakakses h, jbssdm.pegawai p, jbsuser.login l
             WHERE h.modul = 'KEUANGAN'
               AND h.login = l.login
               AND l.login = p.nip
             ORDER BY p.nama";
    $res = $db->QueryDb($sql);

    echo "<select name='petugas' id='petugas' class='inputbox' style='width:160px;' onchange='change_sel()' >";
    echo "<option value='ALL'>(Semua Petugas)</option>";
    echo "<option value='landlord'>Administrator JIBAS</option>";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}
?>