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
function SimpanJenisPenerimaan()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idJenis = $_REQUEST["idjenis"];
        $idKategori = $_REQUEST["idkategori"];
        $departemen = $_REQUEST["departemen"];
        $nama = SafeInput($_REQUEST["nama"]);

        if ($idJenis == 0)
        {
            $sql = "SELECT COUNT(replid)
                      FROM jbsfina.datapenerimaan
                     WHERE nama = '$nama'
                       AND departemen = '$departemen'";
        }
        else
        {
            $sql = "SELECT COUNT(replid)
                      FROM jbsfina.datapenerimaan
                     WHERE nama = '$nama'
                       AND departemen = '$departemen'
                       AND replid <> $idJenis";
        }

        $nData = $db->ExecuteScalar($sql, 0);
        if ($nData != 0)
        {
            return json_encode([-1, "Nama $nama sudah digunakan"]);
        }

        $rekKas = $_REQUEST["rekkas"];
        $rekPendapatan = $_REQUEST["rekpendapatan"];
        $rekPiutang = isset($_REQUEST["rekpiutang"]) ? "'". $_REQUEST["rekpiutang"] . "'" : "NULL";
        $rekDiskon = isset($_REQUEST["rekdiskon"]) ? "'". $_REQUEST["rekdiskon"] . "'" : "NULL";
        $keterangan = SafeInput($_REQUEST["keterangan"]);
        $sendNotif = $_REQUEST["sendnotif"];

        if ($idJenis == 0)
        {
            $sql = "INSERT INTO jbsfina.datapenerimaan
                       SET nama = '$nama', idkategori = '$idKategori', departemen = '$departemen',
                           rekkas = '$rekKas', rekpendapatan = '$rekPendapatan', 
                           rekpiutang = $rekPiutang, info1 = $rekDiskon, 
                           aktif = 1, info2 = $sendNotif, keterangan = '$keterangan'";
        }
        else
        {
            $sql = "UPDATE jbsfina.datapenerimaan
                       SET nama = '$nama', rekkas = '$rekKas', rekpendapatan = '$rekPendapatan',
                           rekpiutang = $rekPiutang, info1 = $rekDiskon, info2 = $sendNotif, keterangan = '$keterangan'
                     WHERE replid = $idJenis";
        }

        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "ky163")]);
    }
    finally
    {
        $db->Close();
    }
}


function LoadValues($db, $idJenis)
{
    global $nama, $rekKas, $rekPendapatan, $rekPiutang, $rekDiskon, $aktif, $keterangan, $sendNotif;
    global $namaRekKas, $namaRekPendapatan, $namaRekPiutang, $namaRekDiskon;

    try
    {
        $sql = "SELECT nama, rekkas, rekpendapatan, 
                       IFNULL(rekpiutang, '') AS rekpiutang, IFNULL(info1, '') AS rekdiskon,
                       keterangan, IFNULL(info2, '0') AS sendsms
                  FROM jbsfina.datapenerimaan
                 WHERE replid = $idJenis";

        $res = $db->QueryDb($sql);
        if ($row = mysqli_fetch_array($res))
        {
            $nama = $row["nama"];
            $rekKas = $row["rekkas"];
            $rekPendapatan = $row["rekpendapatan"];
            $rekPiutang = $row["rekpiutang"];
            $rekDiskon = $row["rekdiskon"];
            $aktif = $row["aktif"];
            $keterangan = $row["keterangan"];
            $sendNotif = $row["sendsms"];

            $sql = "SELECT nama FROM rekakun WHERE kode = '$rekKas'";
            $result = $db->QueryDb($sql);
            $row2 = mysqli_fetch_row($result);
            $namaRekKas = $row2[0];

            $sql = "SELECT nama FROM rekakun WHERE kode = '$rekPendapatan'";
            $result = $db->QueryDb($sql);
            $row2 = mysqli_fetch_row($result);
            $namaRekPendapatan = $row2[0];

            if ($rekPiutang != "")
            {
                $sql = "SELECT nama FROM rekakun WHERE kode = '$rekPiutang'";
                $result = $db->QueryDb($sql);
                $row2 = mysqli_fetch_row($result);
                $namaRekPiutang = $row2[0];
            }

            if ($rekDiskon != "")
            {
                $sql = "SELECT nama FROM rekakun WHERE kode = '$rekDiskon'";
                $result = $db->QueryDb($sql);
                $row2 = mysqli_fetch_row($result);
                $namaRekDiskon = $row2[0];
            }
        }
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "ks6et");
        exit();
    }
}
?>
