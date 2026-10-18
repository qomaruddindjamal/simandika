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
function GantiPassword()
{
    $db = new Db();
    try
    {
        $db->Open();
        $db->BeginTrans();

        $nip = RequestData("nip", "");
        $login = strtolower(RequestData("login", ""));
        $passwordlama = RequestData("passwordlama", "");
        $passwordbaru = RequestData("passwordbaru", "");
        $konfirmasi = RequestData("konfirmasi", "");

        if ($login == "" || $passwordlama == "" || $passwordbaru == "" || $konfirmasi == "")
            return json_encode([-1, "Invalid Data /kc4m8"]);

        if ($passwordbaru != $konfirmasi)
            return json_encode([-1, "Invalid Data /kaj5m"]);

        if ($login == "landlord")
        {
            $sql = "SELECT COUNT(replid)
                      FROM jbsuser.landlord
                     WHERE password = md5('$passwordlama')";
            $nData = $db->FetchSingle($sql, 0);
            if ($nData == 0)
                return json_encode([-1, "Password lama tidak sesuai"]);

            $sql = "UPDATE jbsuser.landlord 
                       SET password = md5('$passwordbaru')";
            $db->QueryDb($sql);
        }
        else
        {
            $sql = "SELECT COUNT(replid) 
                      FROM jbsuser.login 
                     WHERE password = md5('$passwordlama') 
                       AND login = '$nip'";
            $nData = $db->FetchSingle($sql, 0);
            if ($nData == 0)
                return json_encode([-1, "Password lama tidak sesuai"]);

            $sql = "UPDATE jbsuser.login 
                       SET password = md5('$passwordbaru') 
                     WHERE login = '$nip'";
            $db->QueryDb($sql);
        }

        $db->CommitTrans();

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kfc52")]);
    }
    finally
    {
        $db->Close();
    }
}
?>
