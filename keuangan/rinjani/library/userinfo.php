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
require_once ("msg.php");

class UserInfo
{
    public static $DefaultFoto = "/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAA8AAD/4QMvaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjYtYzA2NyA3OS4xNTc3NDcsIDIwMTUvMDMvMzAtMjM6NDA6NDIgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGOTE1RkQ1MzdGM0IxMUYwQTA5N0I4NTc2NDBDQTBBOSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGOTE1RkQ1NDdGM0IxMUYwQTA5N0I4NTc2NDBDQTBBOSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkY5MTVGRDUxN0YzQjExRjBBMDk3Qjg1NzY0MENBMEE5IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkY5MTVGRDUyN0YzQjExRjBBMDk3Qjg1NzY0MENBMEE5Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQABgQEBAUEBgUFBgkGBQYJ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";

    public static function Siswa($db, $nis)
    {
        try
        {
            $sql = "SELECT s.nis, s.nama, k.replid AS idkelas, k.kelas, t.replid AS idtingkat, t.tingkat, t.departemen,
                           s.idangkatan, a.angkatan,  IF(s.foto IS NULL, 0, 1) AS fotoexist, 
                           IF(s.foto IS NULL, '', TO_BASE64(s.foto)) as foto64
                      FROM jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t, jbsakad.angkatan a
                     WHERE s.idkelas = k.replid
                       AND k.idtingkat = t.replid
                       AND s.idangkatan = a.replid
                       AND s.nis = '$nis'";
            $res = $db->QueryDb($sql);
            if (mysqli_num_rows($res) == 0)
            {
                $obj = new stdClass();
                $obj->Exist = false;
                $obj->Error = "";

                return $obj;
            }

            $row = mysqli_fetch_array($res);

            $obj = new stdClass();
            $obj->Exist = true;
            $obj->Error = "";
            $obj->UserCol = "nis";
            $obj->NIS = $row["nis"];
            $obj->Nama = $row["nama"];
            $obj->IdAngkatan = $row["idangkatan"];
            $obj->Angkatan = $row["angkatan"];
            $obj->IdKelas = $row["idkelas"];
            $obj->Kelas = $row["kelas"];
            $obj->IdTingkat = $row["idtingkat"];
            $obj->Tingkat = $row["tingkat"];
            $obj->Departemen = $row["departemen"];
            $obj->FotoExist = $row["fotoexist"];
            $obj->Foto64 = $row["foto64"];

            return $obj;
        }
        catch (Exception $ex)
        {
            $obj = new stdClass();
            $obj->Exist = false;
            $obj->Error = $ex->getMessage();

            return $obj;
        }
    }

    public static function CalonSiswa($db, $nic)
    {
        try
        {
            $sql = "SELECT cs.replid AS idcalon, cs.nopendaftaran, cs.nama, k.replid AS idkelompok, k.kelompok, 
                           p.replid AS idproses, p.proses, p.departemen,
                           IF(cs.foto IS NULL, 0, 1) AS fotoexist, IF(cs.foto IS NULL, '', TO_BASE64(cs.foto)) as foto64
                      FROM jbsakad.calonsiswa cs, jbsakad.kelompokcalonsiswa k, jbsakad.prosespenerimaansiswa p
                     WHERE cs.idkelompok = k.replid
                       AND k.idproses = p.replid
                       AND cs.nopendaftaran = '$nic'";
            $res = $db->QueryDb($sql);
            if (mysqli_num_rows($res) == 0)
            {
                $obj = new stdClass();
                $obj->Exist = false;
                $obj->Error = "";

                return $obj;
            }

            $row = mysqli_fetch_array($res);

            $obj = new stdClass();
            $obj->Exist = true;
            $obj->Error = "";
            $obj->UserCol = "nic";
            $obj->IdCalonSiswa = $row["idcalon"];
            $obj->NIC = $row["nopendaftaran"];
            $obj->Nama = $row["nama"];
            $obj->IdKelompok = $row["idkelompok"];
            $obj->Kelompok = $row["kelompok"];
            $obj->IdProses = $row["idproses"];
            $obj->Proses = $row["proses"];
            $obj->Departemen = $row["departemen"];
            $obj->FotoExist = $row["fotoexist"];
            $obj->Foto64 = $row["foto64"];

            return $obj;
        }
        catch (Exception $ex)
        {
            $obj = new stdClass();
            $obj->Exist = false;
            $obj->Error = $ex->getMessage();

            return $obj;
        }
    }

    public static function Pegawai($db, $nip)
    {
        try
        {
            $sql = "SELECT p.nip, p.nama, p.bagian,
                           IF(p.foto IS NULL, 0, 1) AS fotoexist, 
                           IF(p.foto IS NULL, '', TO_BASE64(p.foto)) as foto64
                      FROM jbssdm.pegawai p
                     WHERE p.nip = '$nip'";
            $res = $db->QueryDb($sql);
            if (mysqli_num_rows($res) == 0)
            {
                $obj = new stdClass();
                $obj->Exist = false;
                $obj->Error = "";

                return $obj;
            }

            $row = mysqli_fetch_array($res);

            $obj = new stdClass();
            $obj->Exist = true;
            $obj->Error = "";
            $obj->UserCol = "nip";
            $obj->NIP = $row["nip"];
            $obj->Nama = $row["nama"];
            $obj->Bagian = $row["bagian"];
            $obj->FotoExist = $row["fotoexist"];
            $obj->Foto64 = $row["foto64"];

            return $obj;
        }
        catch (Exception $ex)
        {
            $obj = new stdClass();
            $obj->Exist = false;
            $obj->Error = $ex->getMessage();

            return $obj;
        }
    }

    public static function ShowPegawaiAvatar($userInfo)
    {
        echo "<table border='0' cellpadding='0' cellspacing='0' width='500'>";
        echo "<tr>";
        echo "<td width='100'>";

        $userFoto = $userInfo->FotoExist ? $userInfo->Foto64 : UserInfo::$DefaultFoto;
        echo "<img style='width: 80px; height: 80px;' class='avatar-circle' src='data:image/jpg;base64,$userFoto'>";

        echo "</td>";
        echo "<td width='400'>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 24px; color: #333; font-weight: bold\">";
        echo $userInfo->Nama;
        echo "</span><br>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 18px; color: #333;\">";
        echo $userInfo->NIP;
        echo "</span><br>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 12px; color: #666;\">";
        echo $userInfo->Bagian;
        echo "</span>&nbsp;&nbsp;";
        echo "<img src='../../images/ico/lihat.png' title='informasi pegawai' class='hide-in-report' style='cursor: pointer' onclick='showInfoPegawai()'>";
        echo "</td>";
        echo "</table>";
    }

    public static function ShowSiswaAvatar($userInfo)
    {
        echo "<table border='0' cellpadding='0' cellspacing='0' width='500'>";
        echo "<tr>";
        echo "<td width='100'>";

        $userFoto = $userInfo->FotoExist ? $userInfo->Foto64 : UserInfo::$DefaultFoto;
        echo "<img style='width: 80px; height: 80px;' class='avatar-circle' src='data:image/jpg;base64,$userFoto'>";

        echo "</td>";
        echo "<td width='400'>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 24px; color: #333; font-weight: bold\">";
        echo $userInfo->Nama;
        echo "</span><br>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 18px; color: #333;\">";
        echo $userInfo->NIS;
        echo "</span><br>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 12px; color: #666;\">";
        echo $userInfo->Departemen . "  |  " . $userInfo->Angkatan . "  |  " . $userInfo->Tingkat . "  |  " . $userInfo->Kelas;
        echo "</span>&nbsp;&nbsp;";
        echo "<img src='../../images/ico/lihat.png' title='informasi siswa' class='hide-in-report' style='cursor: pointer' onclick='showInfoSiswa()'>";
        echo "</td>";
        echo "</table>";
    }

    public static function ShowCalonSiswaAvatar($userInfo)
    {
        echo "<table border='0' cellpadding='0' cellspacing='0' width='500'>";
        echo "<tr>";
        echo "<td width='100'>";

        $userFoto = $userInfo->FotoExist ? $userInfo->Foto64 : UserInfo::$DefaultFoto;
        echo "<img style='width: 80px; height: 80px;' class='avatar-circle' src='data:image/jpg;base64,$userFoto'>";

        echo "</td>";
        echo "<td width='400'>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 24px; color: #333; font-weight: bold\">";
        echo $userInfo->Nama;
        echo "</span><br>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 18px; color: #333;\">";
        echo $userInfo->NIC;
        echo "</span><br>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 12px; color: #666;\">";
        echo $userInfo->Departemen . "  |  " . $userInfo->Proses . "  |  " . $userInfo->Kelompok;
        echo "</span>&nbsp;&nbsp;";
        echo "<img src='../../images/ico/lihat.png' title='informasi calon siswa' class='hide-in-report' style='cursor: pointer' onclick='showInfoCalonSiswa()'>";
        echo "</td>";
        echo "</table>";
    }

    public static function ShowSimpleSiswaAvatar($db, $nis)
    {
        $sql = "SELECT s.nama,  IF(s.foto IS NULL, 0, 1) AS fotoexist, 
                       IF(s.foto IS NULL, '', TO_BASE64(s.foto)) as foto64
                  FROM jbsakad.siswa s
                 WHERE s.nis = '$nis'";
        $res = $db->QueryDb($sql);
        if (mysqli_num_rows($res) == 0)
        {
            echo "&nbsp;";
            return;
        }

        $row = mysqli_fetch_array($res);
        $nama = $row['nama'];
        $fotoExist = $row['fotoexist'];
        $foto64 = $row['foto64'];
        $userFoto = $fotoExist ? $foto64 : UserInfo::$DefaultFoto;

        echo "<table border='0' cellpadding='0' cellspacing='0' width='380'>";
        echo "<tr>";
        echo "<td width='65'>";
        echo "<img style='width: 50px; height: 50px;' class='avatar-circle' src='data:image/jpg;base64,$userFoto'>";
        echo "</td>";
        echo "<td width='300'>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 18px; color: #333; font-weight: bold\">";
        echo $nama;
        echo "</span><br>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 16px; color: #333;\">";
        echo $nis;
        echo "</span>";
        echo "</td>";
        echo "</table>";
    }

    public static function ShowSimpleCalonSiswaAvatar($db, $nic)
    {
        $sql = "SELECT cs.nopendaftaran, cs.nama, 
                       IF(cs.foto IS NULL, 0, 1) AS fotoexist, 
                       IF(cs.foto IS NULL, '', TO_BASE64(cs.foto)) as foto64
                  FROM jbsakad.calonsiswa cs
                 WHERE cs.nopendaftaran = '$nic'";
        $res = $db->QueryDb($sql);
        if (mysqli_num_rows($res) == 0)
        {
            echo "&nbsp;";
            return;
        }

        $row = mysqli_fetch_array($res);
        $nama = $row['nama'];
        $fotoExist = $row['fotoexist'];
        $foto64 = $row['foto64'];
        $userFoto = $fotoExist ? $foto64 : UserInfo::$DefaultFoto;

        echo "<table border='0' cellpadding='0' cellspacing='0' width='380'>";
        echo "<tr>";
        echo "<td width='65'>";
        echo "<img style='width: 50px; height: 50px;' class='avatar-circle' src='data:image/jpg;base64,$userFoto'>";
        echo "</td>";
        echo "<td width='300'>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 18px; color: #333; font-weight: bold\">";
        echo $nama;
        echo "</span><br>";
        echo "<span style=\"font-family: 'Segoe UI', serif; font-size: 16px; color: #333;\">";
        echo $nic;
        echo "</span>";
        echo "</td>";
        echo "</table>";
    }
}
?>
