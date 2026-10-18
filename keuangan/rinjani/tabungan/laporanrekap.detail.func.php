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
$nRowPerPage = 10;

function ShowRincianRekapitulasiTabungan()
{
    global $nRowPerPage;
    global $idpetugas, $jenis, $kelompok, $idtabungan, $tanggal1, $tanggal2, $page, $ndata;

    $db = new Db();
    try
    {
        $db->Open();

        if ($idpetugas == "ALL")
        {
            $sql_idpetugas = "";
            $namapetugas = "Semua Petugas";
        }
        elseif ($idpetugas == "landlord")
        {
            $sql_idpetugas = " AND t.petugas = 'landlord'";
            $namapetugas = "Administrator JIBAS";
        }
        else
        {
            $sql_idpetugas = " AND t.petugas = '$idpetugas'";
            $sql = "SELECT nama
                      FROM jbssdm.pegawai
                     WHERE nip = '$idpetugas'";
            $namapetugas = $db->FetchSingle($sql, "");
        }

        $select = $jenis == "SETORAN" ? "t.kredit AS jumlah" : "t.debet AS jumlah";
        if ($kelompok == "siswa")
        {
            $sql = "SELECT t.nis AS userid, s.nama, $select, t.petugas, t.keterangan, t.tanggal
                      FROM jbsfina.tabungan t, jbsakad.siswa s 
                     WHERE t.nis = s.nis
                       AND idtabungan = '$idtabungan'
                       AND tanggal BETWEEN '$tanggal1' AND '$tanggal2'";
        }
        else
        {
            $sql = "SELECT t.nip AS userid, p.nama, $select, t.petugas, t.keterangan, t.tanggal
                      FROM jbsfina.tabunganp t, jbssdm.pegawai p 
                     WHERE t.nip = p.nip
                       AND idtabungan = '$idtabungan'
                       AND tanggal BETWEEN '$tanggal1' AND '$tanggal2'";
        }

        $sql .=  $sql_idpetugas;
        if ($jenis == "SETORAN")
            $sql .= " AND t.kredit <> 0";
        else
            $sql .= " AND t.debet <> 0";

        $sql .= " ORDER BY t.tanggal";

        $startIndex = ($page - 1) * $nRowPerPage;
        $sql .= " LIMIT $startIndex, $nRowPerPage";

        echo "<table border='1' id='tabDetail' cellpadding='2' cellspacing='0' width='99%' class='tab' style='border-width: 1px; border-collapse: collapse;'>";
        echo "<tr height='25'>";
        echo "<td class='header' width='5%' align='center'>No</td>";
        echo "<td class='header' width='12%' align='center'>Tanggal</td>";
        echo "<td class='header' width='25%' align='center'>Siswa</td>";
        echo "<td class='header' width='20%' align='center'>$jenis</td>";
        echo "<td class='header' width='20%' align='center'>Petugas</td>";
        echo "<td class='header' width='*' align='center'>Keterangan</td>";
        echo "</tr>";

        $no = $ndata - $startIndex + 1;
        $res = $db->QueryDb($sql);
        while($row = mysqli_fetch_array($res))
        {
            $no -= 1;

            echo "<tr style='height: 25px'>";
            echo "<td align='center' class='numberColumn'>$no</td>";
            echo "<td align='left'>$row[tanggal]</td>";
            echo "<td align='left'>$row[userid]  -  $row[nama]</td>";
            echo "<td align='right'>" . FormatRupiah($row['jumlah']) . "</td>";
            echo "<td align='left'>$namapetugas</td>";
            echo "<td align='left'>$row[keterangan]</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kfcq8");
    }
    finally
    {
        $db->Close();
    }
}
?>
