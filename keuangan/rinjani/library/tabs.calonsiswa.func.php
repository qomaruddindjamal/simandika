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
require_once (__DIR__ . "/../include/config.php");
require_once (__DIR__ . "/../include/db.onfunc.php");
require_once (__DIR__ . "/msg.php");

function ShowSelectProsesCalonSiswa()
{
    global $departemen, $idProses, $proses;

    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT replid, proses, aktif 
                  FROM jbsakad.prosespenerimaansiswa 
                 WHERE departemen = '$departemen' 
                 ORDER BY aktif DESC, replid DESC";
        $res = $db->QueryDb($sql);

        echo "<select id='tabcsiswa_proses' onChange='tabcsiswa_onProsesChange()' class='inputbox' style='width:200px'>";
        while($row = mysqli_fetch_row($res))
        {
            if ($idProses == "")
            {
                $idProses = $row[0];
                $proses = $row[1];
            }
            $sel = $idProses == $row[0] ? "selected" : "";
            echo "<option value='$row[0]' $sel>$row[1]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "ka20u");
    }
    finally
    {
        $db->Close();
    }
}

function ShowSelectKelompokCalonSiswa()
{
    global $idProses, $idKelompok, $kelompok;

    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT replid, kelompok 
                  FROM jbsakad.kelompokcalonsiswa 
                 WHERE idproses = '$idProses' 
                 ORDER BY kelompok";
        $res = $db->QueryDb($sql);

        echo "<select id='tabcsiswa_kelompok' onChange='tabcsiswa_onKelompokChange()' class='inputbox' style='width:200px'>";
        while($row = mysqli_fetch_row($res))
        {
            if ($idKelompok == "")
            {
                $idKelompok = $row[0];
                $kelompok = $row[1];
            }
            $sel = $idKelompok == $row[0] ? "selected" : "";
            echo "<option value='$row[0]' $sel>$row[1]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "ka20u");
    }
    finally
    {
        $db->Close();
    }
}

function ColumnColor($urut, $urutBy)
{
    return ($urut == $urutBy) ? "#fffc00" : "#25f2ff";
}

function ShowDaftarCalonSiswa()
{
    global $departemen, $idKelompok, $kelompok, $idProses, $proses, $urut;

    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT cs.nopendaftaran, cs.nama, cs.replid 
                  FROM jbsakad.calonsiswa cs, jbsakad.kelompokcalonsiswa k 
                 WHERE cs.idkelompok = k.replid
                   AND cs.idkelompok = $idKelompok 
                   AND cs.aktif = 1 
                 ORDER BY $urut ASC";
        $res = $db->QueryDb($sql);

        if (mysqli_num_rows($res) == 0)
        {
            echo "<br><br><i>Tidak ditemukan data calon siswa</i>";
            return;
        }

        echo "<table id='tabcsiswa_table_pilih' class='tab' border='1' style='border-collapse:collapse' width='100%' align='center'>";
        echo "<tr align='center'>";
        echo "<td class='header-sm' width='10%'>No</td>";
        $color = ColumnColor($urut, "cs.nopendaftaran");
        echo "<td class='header-sm' width='40%' style='cursor: pointer; color: $color' onclick=\"tabcsiswa_changeUrut('daftar', 'cs.nopendaftaran')\">";
        echo "No Pendaftaran";
        echo "</td>";
        $color = ColumnColor($urut, "cs.nama");
        echo "<td class='header-sm' width='*' style='cursor: pointer; color: $color' onclick=\"tabcsiswa_changeUrut('daftar', 'cs.nama')\">";
        echo "<span >Nama</span>";
        echo "</td>";
        echo "</tr>";

        $cnt = 0;
        while($row = mysqli_fetch_row($res))
        {
            $cnt += 1;

            $data = new stdClass();
            $data->Kelompok = "calonsiswa";
            $data->NIC = $row[0];
            $data->Nama = $row[1];
            $data->Replid = $row[2];
            $data->Departemen = $departemen;
            $data->IdProses = $idProses;
            $data->Proses = $proses;
            $data->IdKelompok = $idKelompok;
            $data->Kelompok = $kelompok;

            $json64 = base64_encode(json_encode($data));

            //echo "<tr style='cursor: pointer' onclick='tabcsiswa_pilihCalonSiswa(\"$row[0]\", \"$row[1]\")'>";
            echo "<tr style='cursor: pointer' onclick='tabcsiswa_pilihCalonSiswa(\"calonsiswa\", \"$json64\")'>";
            echo "<td class='numberColumn' align='center'>$cnt</td>";
            echo "<td colspan='2'>";
            echo "<span style='color: blue'>$row[0]</span>&nbsp;&nbsp;$row[1]";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";

    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kww5w");
    }
    finally
    {
        $db->Close();
    }
}

function ShowCariCalonSiswa()
{
    $db = new Db();
    try
    {
        $db->Open();

        $searchBy = $_REQUEST["searchby"];
        $search = $_REQUEST["search"];
        $departemen = $_REQUEST["departemen"];
        $urut = $_REQUEST["urut"];

        $sql = "SELECT cs.nopendaftaran, cs.nama, k.kelompok, p.departemen, p.proses,
                       cs.replid, p.replid AS idproses, k.replid AS idkelompok 
                  FROM jbsakad.calonsiswa cs, jbsakad.kelompokcalonsiswa k, jbsakad.prosespenerimaansiswa p 
                 WHERE $searchBy LIKE '%$search%' 
                   AND k.replid = cs.idkelompok 
                   AND cs.aktif = 1 
                   AND k.idproses = p.replid
                   AND p.departemen = '$departemen' 
                 ORDER BY $urut ASC";
        $res = $db->QueryDb($sql);

        if (mysqli_num_rows($res) == 0)
        {
            echo "<br><br><i>Tidak ditemukan data calon siswa</i>";
            return;
        }

        echo "<table id='tabcsiswa_table_cari' class='tab' border='1' style='border-collapse:collapse' width='100%' align='center'>";
        echo "<tr style='height: 15px;' align='center'>";
        echo "<td class='header' width='10%'>No</td>";
        $color = ColumnColor($urut, "cs.nopendaftaran");
        echo "<td class='header' width='40%' style='cursor: pointer; color: $color;' onclick=\"tabcsiswa_changeUrut('cari', 'cs.nopendaftaran')\">";
        echo "No Pendaftaran";
        echo "</td>";
        $color = ColumnColor($urut, "cs.nama");
        echo "<td class='header' width='*' style='cursor: pointer; color: $color;' onclick=\"tabcsiswa_changeUrut('cari', 'cs.nama')\">";
        echo "Nama";
        echo "</td>";
        echo "</tr>";

        $cnt = 0;
        while($row = mysqli_fetch_array($res))
        {
            $cnt += 1;

            $data = new stdClass();
            $data->Kelompok = "calonsiswa";
            $data->NIC = $row['nopendaftaran'];
            $data->Nama = $row['nama'];
            $data->Replid = $row['replid'];
            $data->Departemen = $departemen;
            $data->IdProses = $row['idproses'];
            $data->Proses = $row['proses'];
            $data->IdKelompok = $row['idkelompok'];
            $data->Kelompok = $row['kelompok'];

            $json64 = base64_encode(json_encode($data));

            //echo "<tr style='cursor: pointer' onclick='tabcsiswa_pilihCalonSiswa(\"$row[0]\", \"$row[1]\")'>";
            echo "<tr style='cursor: pointer' onclick='tabcsiswa_pilihCalonSiswa(\"calonsiswa\", \"$json64\")'>";
            echo "<td class='numberColumn' align='center'>$cnt</td>";
            echo "<td colspan='2' align='left'>";
            echo "<span style='color: blue'>$row[nopendaftaran]</span>&nbsp;&nbsp;$row[nama]<br>";
            echo "<span style='color: #999'>$row[proses] - $row[kelompok]</span>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";

    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k8d3t");
    }
    finally
    {
        $db->Close();
    }
}
?>