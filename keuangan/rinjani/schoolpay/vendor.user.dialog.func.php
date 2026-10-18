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
function GetVendorName($db, $vendorId)
{
    $vendorName = "";

    $sql = "SELECT nama FROM jbsfina.vendor WHERE vendorid = '$vendorId'";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        $vendorName = $row[0];
    }

    return $vendorName;
}

function ShowSelectPetugas($db, $vendorId)
{
    $sb = new StringBuilder();
    $sb->AppendLine("<select id='petugas' name='petugas' class='inputbox' style='width: 300px'>");

    $sql = "SELECT u.userid, u.nama
              FROM jbsfina.userpos u
             WHERE u.aktif = 1
               AND NOT u.userid IN (SELECT vu.userid FROM jbsfina.vendoruser vu WHERE vu.vendorid = '$vendorId')
             ORDER BY u.nama";
    $res = $db->QueryDb($sql);
    while($row = mysqli_fetch_row($res))
    {
        $sb->AppendLine("<option value='$row[0]'>$row[1]</option>");
    }
    $sb->AppendLine("</select>");

    echo $sb->ToString();
}

function createJsonReturn($status, $message)
{
    $ret = array($status, $message);
    return json_encode($ret);
}

function TambahVendorUser()
{
    $db = new Db();
    try
    {
        $db->Open();

        $vendorId = $_REQUEST["vendorid"];
        $userId = $_REQUEST["userid"];
        $tingkat = $_REQUEST["tingkat"];

        $sql = "SELECT COUNT(replid) 
                  FROM jbsfina.vendoruser
                 WHERE vendorid = '$vendorId'
                   AND userid = '$userId'";
        $nData = $db->FetchSingle($sql, 0);
        if ($nData != 0)
            return json_encode([-1, "User id $userId sudah terdaftar sebagai petugas" ]);

        $sql = "INSERT INTO jbsfina.vendoruser
                   SET vendorid = '$vendorId', userid = '$userId', tingkat = $tingkat";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k8fyb")]);
    }
    finally
    {
        $db->Close();
    }
}
?>