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

function LoadValues($db)
{
    global $idTahunBuku;
    global $departemen, $tahunBuku, $tglMulai, $awalan, $keterangan;

    try
    {
        $sql = "SELECT * 
                  FROM jbsfina.tahunbuku
                 WHERE replid = $idTahunBuku";
        $res = $db->QueryDb($sql);
        if ($row = mysqli_fetch_array($res))
        {
            $departemen = $row["departemen"];
            $tahunBuku = $row["tahunbuku"];
            $tglMulai = $row["tanggalmulai"];
            $awalan = $row["awalan"];
            $keterangan = $row["keterangan"];
        }
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k8et5");
    }
}

function SimpanTahunBuku()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idTahunBuku = $_REQUEST["idtahunbuku"];
        $departemen = $_REQUEST["departemen"];
        $tahunBuku = SafeInput($_REQUEST["tahunbuku"]);
        $tglMulai = $_REQUEST["tglmulai"];
        $awalan = SafeInput($_REQUEST["awalan"]);
        $keterangan = SafeInput($_REQUEST["keterangan"]);

        $sql = "SELECT COUNT(replid)
                  FROM jbsfina.tahunbuku
                 WHERE tahunbuku = '$tahunBuku'
                   AND departemen = '$departemen'
                   AND replid <> $idTahunBuku";
        $nData = $db->ExecuteScalar($sql, 0);
        if ($nData > 0)
            return json_encode([-1, "Tahun buku $tahunBuku sudah digunakan"]);

        $sql = "SELECT COUNT(replid)
                  FROM jbsfina.tahunbuku
                 WHERE awalan = '$awalan'
                   AND departemen = '$departemen'
                   AND replid <> $idTahunBuku";
        $nData = $db->ExecuteScalar($sql, 0);
        if ($nData > 0)
            return json_encode([-1, "Awalan $awalan sudah digunakan"]);

        if ($idTahunBuku == 0)
        {
            $sql = "INSERT INTO jbsfina.tahunbuku 
                       SET tahunbuku = '$tahunBuku', tanggalmulai = '$tglMulai', awalan = '$awalan', 
                           aktif = 1, keterangan = '$keterangan', departemen = '$departemen'";
        }
        else
        {
            $sql = "UPDATE jbsfina.tahunbuku
                       SET tahunbuku = '$tahunBuku', tanggalmulai = '$tglMulai',
                           awalan = '$awalan', keterangan = '$keterangan'
                     WHERE replid = $idTahunBuku";
        }
        //Logger::LogOnce($sql);
        $db->ExecuteNonQuery($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "")]);
    }
    finally
    {
        $db->Close();
    }
}
?>