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
function ShowDaftarPegawai()
{
    global $bagian, $urut;

    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT p.nip, p.nama, p.bagian, p.replid 
                  FROM jbssdm.pegawai p
                 WHERE p.aktif = 1";
        if ($bagian != "ALL")
            $sql .= " AND p.bagian = '$bagian'";
        $sql .= " ORDER BY $urut";

        $res = $db->QueryDb($sql);

        if (mysqli_num_rows($res) == 0)
        {
            echo "<br><br><i>Tidak ditemukan data pegawai</i>";
            return;
        }

        echo "<table id='tabpegawai_table_pilih' class='tab' border='1' style='border-collapse:collapse' width='100%' align='center'>";
        echo "<tr align='center'>";
        echo "<td class='header-sm' width='10%'>No</td>";
        echo "<td class='header-sm' width='40%'>";
        echo "<span style='cursor: pointer' onclick=\"tabpegawai_changeUrut('daftar', 'p.nip')\">NIP</span>";
        echo "</td>";
        echo "<td class='header-sm' width='*'>";
        echo "<span style='cursor: pointer' onclick=\"tabpegawai_changeUrut('daftar', 'p.nama')\">Nama</span>";
        echo "</td>";
        echo "</tr>";

        $cnt = 0;
        while($row = mysqli_fetch_row($res))
        {
            $cnt += 1;

            $data = new stdClass();
            $data->NIP = $row[0];
            $data->Nama = $row[1];
            $data->Replid = $row[3];
            $data->Bagian = $row[2];
            $data->Kelompok = "pegawai";

            $json64 = base64_encode(json_encode($data));

            echo "<tr style='cursor: pointer' onclick='tabpegawai_pilihPegawai(\"pegawai\", \"$json64\")'>";
            echo "<td class='numberColumn' align='center'>$cnt</td>";
            echo "<td colspan='2'><span style='color: blue'>$row[0]</span>&nbsp;&nbsp;&nbsp;$row[1]<br>";
            echo "<span style='color: #666'> bagian: $row[2]</span>";
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

function ShowCariPegawai()
{
    $db = new Db();
    try
    {
        $db->Open();

        $searchBy = $_REQUEST["searchby"];
        $search = $_REQUEST["search"];
        $urut = $_REQUEST["urut"];

        $sql = "SELECT p.replid, p.nip, p.nama, p.bagian
                  FROM jbssdm.pegawai p 
                 WHERE $searchBy LIKE '%$search%' 
                 ORDER BY $urut ASC";
        $res = $db->QueryDb($sql);

        if (mysqli_num_rows($res) == 0)
        {
            echo "<br><br><i>Tidak ditemukan data pegawai</i>";
            return;
        }

        echo "<table id='tabpegawai_table_cari' class='tab' border='1' style='border-collapse:collapse' width='100%' align='center'>";
        echo "<tr style='height: 15px;' align='center'>";
        echo "<td class='header-sm' width='10%'>No</td>";
        echo "<td class='header-sm' width='40%'>";
        echo "<span style='cursor: pointer' onclick=\"tabpegawai_changeUrut('cari', 'p.nip')\">NIP</span>";
        echo "</td>";
        echo "<td class='header-sm' width='*'>";
        echo "<span style='cursor: pointer' onclick=\"tabpegawai_changeUrut('cari', 'p.nama')\">Nama</span>";
        echo "</td>";
        echo "</tr>";

        $cnt = 0;
        while($row = mysqli_fetch_array($res))
        {
            $cnt += 1;

            $data = new stdClass();
            $data->NIP = $row['nip'];
            $data->Nama = $row['nama'];
            $data->Replid = $row['replid'];
            $data->Bagian = $row['bagian'];
            $data->Kelompok = "pegawai";

            $json64 = base64_encode(json_encode($data));

            echo "<tr style='cursor: pointer' onclick='tabpegawai_pilihPegawai(\"pegawai\", \"$json64\")'>";
            echo "<td class='numberColumn' align='center'>$cnt</td>";
            echo "<td colspan='2'>";
            echo "<span><span style='color: blue'>$row[nip]</span>&nbsp;&nbsp;&nbsp;$row[nama]</span><br>";
            echo "<span style='color: #666'> bagian: $row[bagian]</span>";
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