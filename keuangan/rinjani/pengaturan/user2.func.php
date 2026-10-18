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
    global $nip, $nama, $departemen, $status_user, $keterangan;

    $sql = "SELECT h.login, h.tingkat, h.departemen, h.keterangan, p.nama 
              FROM jbsuser.hakakses h, jbssdm.pegawai p, jbsuser.login l 
             WHERE h.modul = 'KEUANGAN' 
               AND h.login = l.login 
               AND l.login = p.nip 
               AND h.replid = $idUser";
    $res = $db->QueryDb($sql);
    if (mysqli_num_rows($res) == 0)
        return false;

    $row = mysqli_fetch_array($res);
    $nip = $row['login'];
    $nama = $row['nama'];
    $departemen = $row['departemen'];
    $status_user = $row['tingkat'];
    $keterangan = $row['keterangan'];

    return true;
}

function HapusPengguna()
{
    $db = new Db();
    try
    {
        $db->Open();

        $iduser = RequestData("iduser", 0);

        $sql = "DELETE FROM jbsuser.hakakses 
                 WHERE replid = $iduser";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kvyeb")]);
    }
    finally
    {
        $db->Close();
    }
}
?>
