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
require_once('../include/sessioninfo.php');
require_once('../library/common.func.php');
require_once('../include/config.php');
require_once('../include/db.onpage.php');

$json = "{\"status\":\"-1\", \"message\":\"EMPTY\", \"userid\":\"EMPTY\"}";

$idkategori = $_REQUEST['idkategori'];
$departemen = $_REQUEST['departemen'];
$kode = trim($_REQUEST['kode']);

OpenDb();

if ($idkategori == "JTT" || $idkategori == "SKR" || $idkategori == "TABS")
{
    $sql = "SELECT s.replid, a.departemen, s.nama 
              FROM jbsakad.siswa s, jbsakad.angkatan a 
             WHERE s.nis = '$kode' 
               AND s.aktif = 1
               AND s.idangkatan = a.replid";
    $res = QueryDb($sql);
    if (mysqli_num_rows($res) > 0)
    {
        $row = mysqli_fetch_row($res);
        $userDept = $row[1];

        $obj = new stdClass();
        $obj->userid = strtoupper($kode);
        $obj->userreplid = $row[0];
        $obj->username = $row[2];
        $obj->usercol = "nis";
        if ($departemen == $userDept)
        {
            $obj->status = 1;
            $obj->message = "";
            //$json = "{\"status\":\"1\", \"message\":\"\", \"userid\":\"$userId\"}";
        }
        else
        {
            $obj->status = -1;
            $obj->message = "siswa tidak dapat melakukan transaksi di $departemen";
            //$json = "{\"status\":\"-1\", \"message\":\"siswa tidak dapat melakukan transaksi di $departemen\", \"userid\":\"\"}";
        }
        $json = json_encode($obj);
    }
    else
    {
        $obj = new stdClass();
        $obj->userid = "";
        $obj->userreplid = "";
        $obj->username = "";
        $obj->usercol = "";
        $obj->status = -1;
        $obj->message = "tidak ditemukan data siswa";
        $json = json_encode($obj);
        //$json = "{\"status\":\"-1\", \"message\":\"tidak ditemukan data siswa\", \"userid\":\"\"}";
    }
}
else if ($idkategori == "TABP")
{
    $sql = "SELECT p.replid, p.bagian, p.nama 
              FROM jbssdm.pegawai p 
             WHERE p.nip = '$kode' 
               AND p.aktif = 1";
    $res = QueryDb($sql);
    if (mysqli_num_rows($res) > 0)
    {
        $row = mysqli_fetch_row($res);

        $obj = new stdClass();
        $obj->userid = strtoupper($kode);
        $obj->userreplid = $row[0];
        $obj->username = $row[2];
        $obj->usercol = "nip";
        $obj->status = 1;
        $obj->message = "";

        $json = json_encode($obj);
    }
    else
    {
        $obj = new stdClass();
        $obj->userid = "";
        $obj->userreplid = "";
        $obj->username = "";
        $obj->usercol = "";
        $obj->status = -1;
        $obj->message = "tidak ditemukan data pegawai";

        $json = json_encode($obj);
    }
}
else
{
    $sql = "SELECT cs.replid, p.departemen, cs.nama 
              FROM jbsakad.calonsiswa cs, jbsakad.prosespenerimaansiswa p 
             WHERE cs.nopendaftaran = '$kode'
               AND cs.aktif = 1
               AND cs.idproses = p.replid";
    $res = QueryDb($sql);
    if (mysqli_num_rows($res) > 0)
    {
        $row = mysqli_fetch_row($res);
        $userDept = $row[1];

        $obj = new stdClass();
        $obj->userid = strtoupper($kode);
        $obj->userreplid = $row[0];
        $obj->username = $row[2];
        $obj->usercol = "nic";
        if ($departemen == $userDept)
        {
            //$json = "{\"status\":\"1\", \"message\":\"\", \"userid\":\"$userId\"}";
            $obj->status = 1;
            $obj->message = "";
        }
        else
        {
            //$json = "{\"status\":\"-1\", \"message\":\"calon siswa tidak dapat melakukan transaksi di $departemen\", \"userid\":\"\"}";
            $obj->status = -1;
            $obj->message = "calon siswa tidak dapat melakukan transaksi di $departemen";
        }

        $json = json_encode($obj);
    }
    else
    {
        //$json = "{\"status\":\"-1\", \"message\":\"tidak ditemukan data calon siswa\", \"userid\":\"\"}";
        $obj = new stdClass();
        $obj->userid = "";
        $obj->userreplid = "";
        $obj->usercol = "";
        $obj->username = "";
        $obj->status = -1;
        $obj->message = "tidak ditemukan data calon siswa";

        $json = json_encode($obj);
    }
}
CloseDb();

echo $json;
?>