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
function LoadValues($db, $idUser)
{
    global $nip, $nama, $status_user, $departemen, $keterangan;

    $sql = "SELECT h.login, h.tingkat, h.departemen, h.keterangan, p.nama 
              FROM jbsuser.hakakses h, jbssdm.pegawai p, jbsuser.login l 
             WHERE h.modul = 'KEUANGAN' 
               AND h.login = l.login 
               AND l.login = p.nip 
               AND h.replid = $idUser";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_array($res))
    {
        $nip = $row['login'];
        $nama = $row['nama'];
        $status_user = $row['tingkat'];
        $departemen = $row['departemen'];
        $keterangan = $row['keterangan'];
    }
}

function ShowSelectDepartemenDaftarPengguna($db)
{
    global $status_user, $orig_departemen;

    if ($status_user == 1 || $status_user == "")
    {
        echo "<select class='inputbox' style='width:165px;' id='departemen'>";
        echo "<option value='' selected='selected'>Semua</option>";
        echo "</select>";
        return;
    }

    $sql = "SELECT departemen 
              FROM jbsakad.departemen 
             WHERE aktif = 1 
             ORDER BY urutan ASC";
    $res = $db->QueryDb($sql);
    echo "<select class='inputbox' style='width:165px;' id='departemen'>";
    while($row = mysqli_fetch_array($res))
    {
        $sel = $orig_departemen == $row['departemen'] ? "selected" : "";
        echo  "<option value='$row[departemen]' $sel>$row[departemen]</option>";
    }
    echo "</select>";
}

function CheckUserHasLogin()
{
    $db = new Db();
    try
    {
        $db->Open();

        $nip = RequestData("nip", "");
        $sql = "SELECT COUNT(replid) 
                  FROM jbsuser.login 
                 WHERE login = '$nip'";
        $nData = $db->FetchSingle($sql, 0);

        if ($nData == 0)
            return json_encode([0, "DONT HAVE LOGIN YET"]);

        return json_encode([1, "ALREADY HAVE LOGIN"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kj5m8")]);
    }
    finally
    {
        $db->Close();
    }
}

function SimpanBaru()
{
    $db = new Db();
    try
    {
        $db->Open();
        $db->BeginTrans();

        $iduser = RequestData("iduser", 0);
        $haslogin = RequestData("haslogin", 0);
        $nip = RequestData("nip", "");
        $nama = RequestData("nama", "");
        $password = RequestData("password", "");
        $password = md5($password);
        $status_user = RequestData("status_user", 1);
        $departemen = RequestData("departemen", "");
        $keterangan = RequestData("keterangan", "");

        $sql = "SELECT COUNT(replid)
                  FROM jbsuser.login
                 WHERE login = '$nip'";
        $nData = $db->FetchSingle($sql, 0);
        if ($nData == 0)
        {
            $sql = "INSERT INTO jbsuser.login
                       SET login = '$nip', password = '$password', aktif = 1";
            $db->QueryDb($sql);
        }

        $sql = "SELECT COUNT(replid) 
                  FROM jbsuser.hakakses 
                 WHERE login = '$nip' 
                   AND tingkat = '$status_user' 
                   AND modul = 'KEUANGAN'";
        if ($status_user == 2)
            $sql .= " AND departemen = '$departemen'";

        $nData = $db->FetchSingle($sql, 0);
        if ($nData == 0)
        {
            $sql = "INSERT INTO jbsuser.hakakses 
                       SET login = '$nip', tingkat = $status_user, modul='KEUANGAN', keterangan = '$keterangan'";
            if ($status_user == 2)
                $sql .= ", departemen = '$departemen'";
            else
                $sql .= ", departemen = NULL";
            $db->QueryDb($sql);
        }

        $db->CommitTrans();

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kfc52")]);
    }
    finally
    {
        $db->Close();
    }
}

function SimpanEdit()
{
    $db = new Db();
    try
    {
        $db->Open();
        $db->BeginTrans();

        $iduser = RequestData("iduser", 0);
        $status_user = RequestData("status_user", 1);
        $departemen = RequestData("departemen", "");
        $keterangan = RequestData("keterangan", "");


        $sql = "UPDATE jbsuser.hakakses 
                   SET tingkat = $status_user, keterangan = '$keterangan'";
        if ($status_user == 2)
            $sql .= ", departemen = '$departemen'";
        else
            $sql .= ", departemen = NULL";
        $sql .= " WHERE replid = $iduser";
        $db->QueryDb($sql);

        $db->CommitTrans();

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kfc52")]);
    }
    finally
    {
        $db->Close();
    }
}
?>
