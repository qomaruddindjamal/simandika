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
function LoadValues($db)
{
    global $id, $nama, $keterangan;

    $sql = "SELECT * 
              FROM jbsfina.kelompokbarang 
             WHERE replid = $id";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_array($res))
    {
        $nama = $row['kelompok'];
        $keterangan = $row['keterangan'];
    }
}

function SimpanKelompokBaru()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idgroup = RequestData("idgroup", 0);
        $nama = RequestData("nama", "");
        $keterangan = RequestData("keterangan", "");

        $sql = "SELECT COUNT(replid)
                  FROM jbsfina.kelompokbarang
                 WHERE kelompok = '$nama'
                   AND idgroup = $idgroup ";
        $nData = $db->FetchSingle($sql, 0);
        if ($nData > 0)
            return json_encode([-1, "Kelompok $nama sudah digunakan, pilih nama kelompok lain"]);

        $sql = "INSERT INTO jbsfina.kelompokbarang 
                   SET idgroup = $idgroup, kelompok = '$nama', keterangan = '$keterangan'";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kuaz4")]);
    }
    finally
    {
        $db->Close();
    }
}

function SimpanKelompokEdit()
{
    $db = new Db();
    try
    {
        $db->Open();

        $id = RequestData("id", 0);
        $idgroup = RequestData("idgroup", 0);
        $nama = RequestData("nama", "");
        $keterangan = RequestData("keterangan", "");

        $sql = "SELECT COUNT(replid)
                  FROM jbsfina.kelompokbarang
                 WHERE kelompok = '$nama'
                   AND idgroup = '$idgroup'
                   AND replid <> $id ";
        $nData = $db->FetchSingle($sql, 0);
        if ($nData > 0)
            return json_encode([-1, "Kelompok $nama sudah digunakan, pilih nama kelompok lain"]);

        $sql = "UPDATE jbsfina.kelompokbarang 
                   SET kelompok = '$nama', keterangan = '$keterangan'
                 WHERE replid = $id";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "ktb4m")]);
    }
    finally
    {
        $db->Close();
    }
}

?>