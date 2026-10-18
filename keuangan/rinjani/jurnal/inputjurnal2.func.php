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
function ShowSelectDepartemenInputJurnal($db)
{
    global $departemen;

    try
    {
        echo "<select name='departemen' id='departemen' onChange='change_dep()' class='inputbox' style='width:200px'>";
        $dep = getDepartemen($db, getAccess());
        foreach($dep as $value)
        {
            if ($departemen == "") $departemen = $value;
            $sel = $departemen == $value ? "selected" : "";
            echo "<option value='$value' $sel>$value</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "krt72");
    }
}

function ShowTahunBukuInputJurnal($db)
{
    global $departemen;

    try
    {
        $idTahunBuku = 0;
        $tahunBuku = "";

        $sql = "SELECT replid, tahunbuku 
                  FROM jbsfina.tahunbuku 
                 WHERE aktif = 1 
                   AND departemen = '$departemen'";
        $res = $db->QueryDb($sql);
        if ($row = mysqli_fetch_row($res))
        {
            $idTahunBuku = $row[0];
            $tahunBuku = $row[1];
        }

        echo "<input type='text' id='tahunbuku' style='width: 180px; background-color: #efefef' readonly class='inputbox' value='$tahunBuku'>";
        echo "<input type='hidden' id='idtahunbuku' value='$idTahunBuku'>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "km119");
    }
}

function SimpanInputJurnal()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idTahunBuku = RequestData("idtahunbuku", 0);
        $tanggal = RequestData("tgljurnal", date("Y-m-d"));
        $keperluan = RequestData("keperluan", "");
        $keterangan = RequestData("keterangan", "");
        $nData = RequestData("ndata", 0);

        $idPetugas = getIdUser();
        $petugas = getUserName();
        $idPetugasValue = $idPetugas == "landlord" ? "NULL" : "'$idPetugas'";

        if ($idTahunBuku == 0)
            return json_encode([-1, "Tahun buku tidak tersedia /kfeer"]);

        if ($nData == 0)
            return json_encode([-1, "Data jurnal tidak tersedia /kbth8"]);

        $sql = "SELECT awalan, cacah 
                  FROM jbsfina.tahunbuku 
                 WHERE replid = '$idTahunBuku'";
        $result = $db->QueryDb($sql);
        if (mysqli_num_rows($result) == 0)
            return json_encode([-1, "Tahun buku tidak ditemukan /kyh18"]);

        $row = mysqli_fetch_row($result);
        $awalan = $row[0];
        $cacah = $row[1];

        $cacah += 1;
        $nokas = $awalan . rpad($cacah, "0", 6);

        $db->BeginTrans();

        $sql = "INSERT INTO jbsfina.jurnal 
			       SET tanggal = '$tanggal', transaksi = '$keperluan', idpetugas = $idPetugasValue, petugas='$petugas', 
			   	       nokas = '$nokas', idtahunbuku = '$idTahunBuku', keterangan='$keterangan', sumber='jurnalumum'";
        $db->ExecuteNonQuery($sql);

        $idJurnal = $db->InsertId();

        for($i = 1; $i <= $nData; $i++)
        {
            $koderek = RequestData("koderek$i", "");
            $debet = RequestData("debet$i", 0);
            $kredit = RequestData("kredit$i", 0);

            if (strlen(trim($koderek)) == 0 && $debet == 0 && $kredit == 0)
                continue;

            $sql = "INSERT INTO jbsfina.jurnaldetail 
                       SET idjurnal = '$idJurnal', koderek = '$koderek', debet = '$debet', kredit = '$kredit'";
            $db->ExecuteNonQuery($sql);
        }

        $sql = "UPDATE jbsfina.tahunbuku 
                   SET cacah = cacah + 1 
                 WHERE replid = '$idTahunBuku'";
        $db->ExecuteNonQuery($sql);

        $db->CommitTrans();
        //$db->RollbackTrans();

        $_SESSION["state"] = "InputJurnalSuccess";

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kx5kx")]);
    }
    finally
    {
        $db->Close();
    }
}
?>
