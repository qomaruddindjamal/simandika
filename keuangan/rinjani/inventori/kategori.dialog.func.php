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
              FROM jbsfina.groupbarang 
             WHERE replid = $id";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_array($res))
    {
        $nama = $row['namagroup'];
        $keterangan = $row['keterangan'];
    }
}

function SimpanKategoriBaru()
{
    $db = new Db();
    try
    {
        $db->Open();

        $nama = RequestData("nama", "");
        $keterangan = RequestData("keterangan", "");

        $sql = "SELECT COUNT(replid)
                  FROM jbsfina.groupbarang
                 WHERE namagroup = '$nama'";
        $nData = $db->FetchSingle($sql, 0);
        if ($nData > 0)
            return json_encode([-1, "Kategori $nama sudah digunakan, pilih nama kategori lain"]);

        $sql = "INSERT INTO jbsfina.groupbarang 
                   SET namagroup = '$nama', keterangan = '$keterangan'";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kpama")]);
    }
    finally
    {
        $db->Close();
    }
}

function SimpanKategoriEdit()
{
    $db = new Db();
    try
    {
        $db->Open();

        $id = RequestData("id", 0);
        $nama = RequestData("nama", "");
        $keterangan = RequestData("keterangan", "");

        $sql = "SELECT COUNT(replid)
                  FROM jbsfina.groupbarang
                 WHERE namagroup = '$nama'
                   AND replid <> $id ";
        $nData = $db->FetchSingle($sql, 0);
        if ($nData > 0)
            return json_encode([-1, "Kategori $nama sudah digunakan, pilih nama kategori lain"]);

        $sql = "UPDATE jbsfina.groupbarang 
                   SET namagroup = '$nama', keterangan = '$keterangan'
                 WHERE replid = $id";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kx6s4")]);
    }
    finally
    {
        $db->Close();
    }
}
?>
