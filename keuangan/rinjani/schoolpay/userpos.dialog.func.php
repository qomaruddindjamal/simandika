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
function LoadValue($db)
{
    global $userReplid;
    global $userId, $userName, $origPass, $keterangan;

    if ($userReplid == 0)
        return;

    $sql = "SELECT * FROM jbsfina.userpos WHERE replid = $userReplid";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_array($res))
    {
        $userId = $row["userid"];
        $userName = $row["nama"];

        $passLen = $row["passlength"];

        $origPass = $row["password"];
        $origPass = substr($origPass, 0, $passLen);

        $keterangan = $row["keterangan"];
    }
}

function SimpanPetugas()
{
    $db = new Db();
    try
    {
        $db->Open();

        $userReplid = $_REQUEST["userreplid"];
        $userId = SafeValue($_REQUEST["userid"]);
        $userName = SafeValue($_REQUEST["username"]);
        $origPass = SafeValue($_REQUEST["origpass"]);
        $password = SafeValue($_REQUEST["password"]);
        $keterangan = SafeValue($_REQUEST["keterangan"]);

        if ($userReplid == 0)
        {
            $sql = "SELECT COUNT(replid)
                      FROM jbsfina.userpos 
                     WHERE userid = '$userId'";
            $nData = $db->FetchSingle($sql, 0);
            if ($nData > 0)
                return json_encode([-1, "User Id $userId sudah digunakan. Pilih user id yang lain"]);

            $sql = "INSERT INTO jbsfina.userpos 
                       SET userid = '$userId', nama = '$userName', password = md5('$password'), 
                           passlength = LENGTH('$password'), keterangan = '$keterangan', aktif = 1";
            $db->QueryDb($sql);

            return json_encode([1, "OK"]);
        }

        $sql = "SELECT COUNT(replid)
                  FROM jbsfina.userpos 
                 WHERE userid = '$userId'
                   AND replid <> $userReplid";
        $nData = $db->FetchSingle($sql, 0);
        if ($nData > 0)
            return json_encode([-1, "User Id $userId sudah digunakan. Pilih user id yang lain"]);

        if ($origPass == $password)
        {
            $sql = "UPDATE jbsfina.userpos
                       SET userid = '$userId', nama = '$userName', keterangan = '$keterangan'
                     WHERE replid = $userReplid ";
        }
        else
        {
            $sql = "UPDATE jbsfina.userpos
                       SET userid = '$userId', nama = '$userName', keterangan = '$keterangan', 
                           password = md5('$password'), passlength = LENGTH('$password')
                     WHERE replid = $userReplid ";
        }
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k1k0n")]);
    }
    finally
    {
        $db->Close();
    }
}
?>