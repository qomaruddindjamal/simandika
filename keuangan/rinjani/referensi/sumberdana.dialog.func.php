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
function FetchSumberDana($db, $idSumberDana)
{
    global $kode, $nama, $urutan, $kelompok, $keterangan;

    try
    {
        $sql = "SELECT *
                  FROM jbsfina.sumberdana
                 WHERE id = $idSumberDana";
        $res = $db->QueryDb($sql);
        if ($row = mysqli_fetch_array($res))
        {
            $kode = $row['kode'];
            $nama = $row['nama'];
            $urutan = $row['urutan'];
            $kelompok = $row['kelompok'];
            $keterangan = $row['keterangan'];
        }
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kmrb5");
    }
}

function ShowSelectKelompokSumberDana()
{
    global $kelompok;

    echo "<select id='kelompok' class='inputbox' style='width: 180px'>";
    $sel = $kelompok == "BANK" ? "selected" : "";
    echo "<option value='BANK' $sel>Bank</option>";
    $sel = $kelompok == "EWALLET" ? "selected" : "";
    echo "<option value='EWALLET' $sel>e-Wallet</option>";
    $sel = $kelompok == "PG" ? "selected" : "";
    echo "<option value='PG' $sel>Payment Gateway</option>";
    $sel = $kelompok == "OTHER" ? "selected" : "";
    echo "<option value='OTHER' $sel>Lainnya</option>";
    echo "</select>";
}

function SimpanSumberDana()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idSumberDana = RequestData("idsumberdana", 0);
        $kode = RequestData("kode", "");
        $nama = RequestData("nama", "");
        $urutan = RequestData("urutan", 0);
        $kelompok = RequestData("kelompok", "");
        $keterangan = RequestData("keterangan", "");

        $sql = "SELECT COUNT(id)
                  FROM jbsfina.sumberdana
                 WHERE kode = '$kode'
                   AND id <> $idSumberDana";
        //Logger::LogOnce($sql);
        $nSame = $db->ExecuteScalar($sql, 0);
        if ($nSame > 0)
            return json_encode([-1, "Kode $kode sudah digunakan"]);

        if ($idSumberDana == 0)
        {
            $sql = "INSERT INTO jbsfina.sumberdana
                       SET kode = '$kode', nama = '$nama', urutan = '$urutan', kelompok = '$kelompok',
                           keterangan = '$keterangan', aktif = 1";
        }
        else
        {
            $sql = "UPDATE jbsfina.sumberdana
                       SET kode = '$kode', nama = '$nama', urutan = '$urutan', kelompok = '$kelompok',
                           keterangan = '$keterangan'
                     WHERE id = $idSumberDana";
        }
        //Logger::LogOnce($sql);
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kx9gj")]);
    }
    finally
    {
        $db->Close();
    }
}
?>