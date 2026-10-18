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
function SimpanEditJurnal()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idJurnal = RequestData("idjurnal", 0);
        $departemen = RequestData("departemen", "");
        $tanggal = RequestData("tgljurnal", date("Y-m-d"));
        $keperluan = RequestData("keperluan", "");
        $keterangan = RequestData("keterangan", "");
        $alasan = RequestData("alasan", "");
        $nData = RequestData("ndata", 0);

        if ($nData == 0)
            return json_encode([-1, "Data jurnal tidak tersedia /kbth8"]);

        $sql = "SELECT * FROM jbsfina.jurnal WHERE replid = $idJurnal";
        $res = $db->QueryDb($sql);
        if (mysqli_num_rows($res) == 0)
            return json_encode([-1, "Data jurnal tidak ditemukan /k4psx"]);

        $row = mysqli_fetch_array($res);
        $idtahunbuku = $row['idtahunbuku'];
        $nokas = $row['nokas'];
        $sumberjurnal = $row['sumber'];
        $tgljurnal = $row['tanggal'];
        $transaksijurnal = $row['transaksi'];
        $petugasjurnal = $row['petugas'];
        $ketjurnal = $row['keterangan'];
        $petugas = getUserName();

        $db->BeginTrans();

        $sql = "INSERT INTO jbsfina.auditinfo 
                   SET departemen='$departemen', sumber='$sumberjurnal', idsumber='$idJurnal', 
                       tanggal=now(), petugas='$petugas', alasan = '$alasan'";
        $db->QueryDb($sql);

        $idAudit = $db->InsertId();

        $sql = "INSERT INTO jbsfina.auditjurnal 
                   SET status = 0, idaudit = $idAudit, replid = $idJurnal, 
                       tanggal = '$tgljurnal', transaksi = '$transaksijurnal', petugas = '$petugasjurnal', 
                       nokas = '$nokas', idtahunbuku = '$idtahunbuku', keterangan = '$ketjurnal', sumber = '$sumberjurnal'";
        $db->QueryDb($sql);

        $sql = "SELECT * 
                  FROM jbsfina.jurnaldetail 
                 WHERE idjurnal = '$idJurnal'";
        $res = $db->QueryDb($sql);
        while($row = mysqli_fetch_array($res))
        {
            $sql = "INSERT INTO auditjurnaldetail 
                       SET status = 0, idaudit = $idAudit, idjurnal = $idJurnal, 
                           koderek = '$row[koderek]', debet = $row[debet],  kredit = $row[kredit]";
            $db->ExecuteNonQuery($sql);
        }

        $sql = "UPDATE jbsfina.jurnal 
                   SET tanggal = '$tanggal', transaksi = '$keperluan', petugas = '$petugas', 
                       idtahunbuku = '$idtahunbuku', keterangan = '$keterangan' 
                 WHERE replid = '$idJurnal'";
        $db->ExecuteNonQuery($sql);

        $sql = "INSERT INTO jbsfina.auditjurnal 
                   SET status = 1, idaudit = $idAudit, replid = $idJurnal, 
                       tanggal = '$tanggal', transaksi = '$keperluan', petugas = '$petugas', 
                       nokas = '$nokas', idtahunbuku = '$idtahunbuku', keterangan = '$keterangan', sumber='$sumberjurnal'";
        $db->ExecuteNonQuery($sql);

        $sql = "DELETE FROM jbsfina.jurnaldetail 
                 WHERE idjurnal = '$idJurnal'";
        $db->ExecuteNonQuery($sql);

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

            $sql = "INSERT INTO jbsfina.auditjurnaldetail 
                       SET status = 1, idaudit = '$idAudit', idjurnal = '$idJurnal', 
                           koderek = '$koderek', debet = '$debet', kredit = '$kredit'";
            $db->ExecuteNonQuery($sql);
        }

        $db->CommitTrans();
        //$db->RollbackTrans();

        $_SESSION["state"] = "EditJurnalSuccess";

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kgnnb")]);
    }
    finally
    {
        $db->Close();
    }
}
?>