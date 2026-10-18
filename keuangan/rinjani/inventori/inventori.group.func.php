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
function getNSubDir($db, $idroot)
{
    $sql = "SELECT count(*) 
              FROM jbsfina.kelompokbarang 
             WHERE idgroup = '$idroot'";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);
    return $row[0];
}

function HapusKategori()
{
    $db = new Db();
    try
    {
        $db->Open();

        $id = RequestData("id", 0);

        $sql = "DELETE FROM jbsfina.groupbarang
                 WHERE replid = $id";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $lastError = $db->LastError();
        $errNo = $lastError[0];
        if ($errNo == 1451)
        {
            return json_encode([-99, "Tidak dapat menghapus data ini karena sudah digunakan" ]);
        }
        else
        {
            return json_encode([-99, Msg::InfoError($ex->getMessage(), "kr50g")]);
        }
    }
    finally
    {
        $db->Close();
    }
}

function HapusKelompok()
{
    $db = new Db();
    try
    {
        $db->Open();

        $id = RequestData("id", 0);

        $sql = "DELETE FROM jbsfina.kelompokbarang
                 WHERE replid = $id";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $lastError = $db->LastError();
        $errNo = $lastError[0];
        if ($errNo == 1451)
        {
            return json_encode([-99, "Tidak dapat menghapus data ini karena sudah digunakan" ]);
        }
        else
        {
            return json_encode([-99, Msg::InfoError($ex->getMessage(), "kfxqz")]);
        }
    }
    finally
    {
        $db->Close();
    }
}
?>