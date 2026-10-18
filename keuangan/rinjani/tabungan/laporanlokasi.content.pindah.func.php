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
function ShowSelectLokasiDanaTabunganPindah($db)
{
    global $kodeLokasi;

    try
    {
        $sql = "SELECT kode, nama
                  FROM jbsfina.lokasidana
                 WHERE aktif = 1
                   AND kode <> '$kodeLokasi'
                 ORDER BY urutan";
        $res = $db->QueryDb($sql);

        echo "<select id='lokasitujuan' class='inputbox' style='width:210px'>";
        while($row = mysqli_fetch_row($res))
        {
            echo "<option value='$row[0]'>$row[0] - $row[1]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k3ka1");
    }
}

function SimpanPindahLokasiDana()
{
    $db = new Db();
    try
    {
        $db->Open();

        $db->BeginTrans();

        $stIdList64 = RequestData("stidlist64", "");
        $stIdList = base64_decode($stIdList64);
        
        $kelompok = RequestData("kelompok", "");
        $lokasiAsal = RequestData("kodelokasi", "");
        $lokasiTujuan = RequestData("lokasitujuan", "");
        $keterangan = RequestData("keterangan", "");
        $alasan = RequestData("alasan", "");
        $saldo = RequestData("saldo", 0);
        $idTabungan = RequestData("idtabungan", "");

        $table = $kelompok == "siswa" ? "jbsfina.tabungan" : "jbsfina.tabunganp";
        if ($lokasiAsal == "***")
            $lokasiAsal = "NULL";
        else
            $lokasiAsal = "'$lokasiAsal'";

        $sql = "UPDATE $table
                   SET lokasidana = '$lokasiTujuan', alasan = '$alasan'
                 WHERE replid IN ($stIdList)";
        $db->QueryDb($sql);

        $petugas = getUserName();
        $sql = "INSERT INTO jbsfina.lokasidanamutasi
                   SET tanggal = NOW(),
                       petugas = '$petugas',
                       saldo = $saldo,
                       idtabungan = $idTabungan,
                       kelompok = '$kelompok',
                       lokasiasal = $lokasiAsal,
                       lokasitujuan = '$lokasiTujuan',
                       stidlist = '$stIdList',
                       keterangan = '$keterangan',
                       alasan = '$alasan'";
        $db->QueryDb($sql);

        $db->CommitTrans();

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->RollbackTrans();

        return json_encode([-1, Msg::InfoError($ex->getMessage(), "kxv8h")]);
    }
    finally
    {
        $db->Close();
    }
}
?>