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
require_once ("../library/msg.php");

function LoadValues($db, $idRekAkun)
{
    global $kode, $nama, $keterangan;

    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT kode, nama, keterangan
                  FROM jbsfina.rekakun
                 WHERE replid = $idRekAkun";
        $res = $db->QueryDb($sql);

        if ($row = mysqli_fetch_array($res))
        {
            $kode = $row["kode"];
            $nama = $row["nama"];
            $keterangan = $row["keterangan"];
        }
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "");
    }
    finally
    {
        $db->Close();
    }
}

function IsUsedAlready($db, $kode)
{
    $sql = "SELECT COUNT(replid)
              FROM jbsfina.jurnaldetail
             WHERE koderek = '$kode'
             LIMIT 1";
    $nData = $db->ExecuteScalar($sql, 0);
    return ($nData > 0) ? true : false;
}

function SimpanRekAkun()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idRekAkun = $_REQUEST["idrekakun"];
        $kategori = $_REQUEST["kategori"];
        $nama = SafeInput($_REQUEST["nama"]);
        $kode = SafeInput($_REQUEST["kode"]);
        $keterangan = SafeInput($_REQUEST["keterangan"]);

        $sql = "SELECT COUNT(replid)
                  FROM jbsfina.rekakun
                 WHERE kode = '$kode'";
        if ($idRekAkun != 0)
            $sql .= " AND replid <> $idRekAkun";

        $nData = $db->ExecuteScalar($sql, 0);
        if ($nData > 0)
            return json_encode([-1, "Kode $kode sudah digunakan"]);

        if ($idRekAkun == 0)
        {
            $sql = "INSERT INTO jbsfina.rekakun
                       SET kategori = '$kategori', kode = '$kode', 
                           nama = '$nama', keterangan = '$keterangan'";
        }
        else
        {
            $sql = "UPDATE jbsfina.rekakun
                       SET kode = '$kode', nama = '$nama', keterangan = '$keterangan'
                     WHERE replid = $idRekAkun";
        }

        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();

        return json_encode( [-99, Msg::InfoError($ex->getMessage(), "kqnep")] );
    }
    finally
    {
        $db->Close();
    }
}
?>
