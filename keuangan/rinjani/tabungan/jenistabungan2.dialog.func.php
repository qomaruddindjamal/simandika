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
function LoadValues($db, $idJenis)
{
    global $nama, $rekKas, $namaRekKas, $rekUtang, $namaRekUtang, $keterangan, $isUsed, $sendNotif;

    try
    {
        $sql = "SELECT nama, rekkas, rekutang, keterangan, info2
                  FROM jbsfina.datatabungan
                 WHERE replid = $idJenis";

        $res = $db->QueryDb($sql);
        if ($row = mysqli_fetch_array($res))
        {
            $nama = $row["nama"];
            $rekKas = $row["rekkas"];
            $rekUtang = $row["rekutang"];
            $keterangan = $row["keterangan"];
            $sendNotif = (int) $row["info2"];

            $sql = "SELECT nama FROM jbsfina.rekakun WHERE kode = '$rekKas'";
            $result = $db->QueryDb($sql);
            $row2 = mysqli_fetch_row($result);
            $namaRekKas = $row2[0];

            $sql = "SELECT nama FROM jbsfina.rekakun WHERE kode = '$rekUtang'";
            $result = $db->QueryDb($sql);
            $row2 = mysqli_fetch_row($result);
            $namaRekUtang = $row2[0];
        }

        $sql = "SELECT EXISTS(SELECT replid FROM jbsfina.tabungan WHERE idtabungan = '$idJenis' LIMIT 1)";
        $isUsed = (1 == (int) $db->FetchSingle($sql, 0));
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kfcq8");
        exit();
    }
}

function SimpanJenisTabungan()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idJenis = $_REQUEST["idjenis"];
        $departemen = $_REQUEST["departemen"];
        $nama = SafeInput($_REQUEST["nama"]);
        $sendNotif = $_REQUEST["sendnotif"];

        if ($idJenis == 0)
        {
            $sql = "SELECT COUNT(replid)
                      FROM jbsfina.datatabungan
                     WHERE nama = '$nama'
                       AND departemen = '$departemen'";
        }
        else
        {
            $sql = "SELECT COUNT(replid)
                      FROM jbsfina.datatabungan
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
        $rekUtang = $_REQUEST["rekutang"];
        $keterangan = SafeInput($_REQUEST["keterangan"]);

        if ($idJenis == 0)
        {
            $sql = "INSERT INTO jbsfina.datatabungan
                       SET nama = '$nama', departemen = '$departemen', aktif = 1,
                           rekkas = '$rekKas', rekutang = '$rekUtang', 
                           keterangan = '$keterangan', info2 = '$sendNotif'";
        }
        else
        {
            $sql = "UPDATE jbsfina.datatabungan
                       SET nama = '$nama', rekkas = '$rekKas', rekutang = '$rekUtang',  
                           keterangan = '$keterangan', info2 = '$sendNotif'
                     WHERE replid = $idJenis";
        }

        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k5y65")]);
    }
    finally
    {
        $db->Close();
    }
}
?>