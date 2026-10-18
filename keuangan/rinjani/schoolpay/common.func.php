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
function GetVendorName2($db, $vendorId)
{
    $sql = "SELECT nama FROM jbsfina.vendor WHERE vendorid = '$vendorId'";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
        return $row[0];

    return "";
}

function GetVendorUserName2($db, $userId)
{
    $sql = "SELECT nama FROM jbsfina.userpos WHERE userid = '$userId'";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
        return $row[0];

    return "";
}

function GetTahunBukuName2($db, $idTahunBuku)
{
    $sql = "SELECT tahunbuku FROM jbsfina.tahunbuku WHERE replid = $idTahunBuku";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
        return $row[0];

    return "";
}

function GetKelasName2($db, $idKelas)
{
    $sql = "SELECT kelas FROM jbsakad.kelas WHERE replid = $idKelas";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
        return $row[0];

    return "";
}
?>
