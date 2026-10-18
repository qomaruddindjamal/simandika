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

function ShowDaftarSiswaContainer()
{
    global $nRowPerPage;
    global $idpenerimaan, $idtahunbuku, $page, $urut;

    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT COUNT(b.replid) 
                  FROM jbsfina.besarjtt b
                 WHERE b.idpenerimaan = '$idpenerimaan'
                   AND b.info2 = '$idtahunbuku'
                   AND b.lunas = 0";
        $nData = $db->FetchSingle($sql, 0);
        if ($nData == 0)
        {
            echo "<br><br>";
            echo "<span style='color: maroon'>Tidak ditemukan data siswa yang belum melunasi tunggakan</span>";
            return;
        }

        $nPage = ceil($nData / $nRowPerPage);

        echo "<div id='dvDaftarSiswa'>";
        ShowDaftarSiswaTable($db);
        echo "</div>";

        echo "<input type='hidden' id='ndata' value='$nData'>";
        echo "<input type='hidden' id='npage' value='$nPage'>";

        echo "Halaman ";
        echo "<input type='button' class='but' value=' < ' style='width: 30px; height: 25px' onclick='onPrevPageSiswa()'> ";
        echo "<select id='page' onchange='onChangePageSiswa()' style='width: 60px' class='inputbox'>";
        for($i = 1; $i <= $nPage; $i++)
        {
            echo "<option value='$i'>$i</option>";
        }
        echo "</select>";
        echo "<input type='button' class='but' value=' > ' style='width: 30px; height: 25px' onclick='onNextPageSiswa()'> ";
        echo " dari $nPage, jumlah $nData data";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k6gxj");
    }
    finally
    {
        $db->Close();
    }
}

function ShowDaftarSiswaTable($db)
{
    global $nRowPerPage;
    global $idpenerimaan, $idtahunbuku, $page, $urut;

    $startIndex = ($page - 1) * $nRowPerPage;

    $sql = "SELECT DISTINCT b.nis, s.nama 
              FROM jbsfina.besarjtt b, jbsakad.siswa s
             WHERE b.idpenerimaan = '$idpenerimaan' 
               AND b.info2 = '$idtahunbuku'
               AND b.lunas = 0
               AND b.nis = s.nis
             ORDER BY $urut
             LIMIT $startIndex, $nRowPerPage";
    $res = $db->QueryDb($sql);

    echo "<table id='tabsiswa_daftar' class='tab' border='1' style='border-collapse:collapse' width='100%' align='center'>";
    echo "<tr align='center'>";
    echo "<td class='header-sm' width='10%'>No</td>";
    $color = ColumnColor($urut, "s.nis");
    echo "<td class='header-sm' width='40%' style='cursor: pointer; color: $color' onclick=\"tabsiswa_changeUrut('s.nis')\">";
    echo "NIS";
    echo "</td>";
    $color = ColumnColor($urut, "s.nama");
    echo "<td class='header-sm' width='*' style='cursor: pointer; color: $color' onclick=\"tabsiswa_changeUrut('s.nama')\">";
    echo "Nama";
    echo "</td>";
    echo "</tr>";

    $cnt = ($page - 1) * $nRowPerPage;
    while($row = mysqli_fetch_row($res))
    {
        $cnt += 1;

        $nis = $row[0];
        $nama = $row[1];

        echo "<tr style='cursor: pointer' onclick='tabsiswa_pilih(\"$nis\", \"$nama\")'>";
        echo "<td class='numberColumn' align='center'>$cnt</td>";
        echo "<td colspan='2'><span style='color: blue'>$nis</span>&nbsp;&nbsp;&nbsp;$nama";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

function ShowDaftarCalonSiswaContainer()
{
    global $nRowPerPage;
    global $idpenerimaan, $idtahunbuku, $page, $urut;

    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT COUNT(b.replid) 
                  FROM jbsfina.besarjttcalon b
                 WHERE b.idpenerimaan = '$idpenerimaan'
                   AND b.info2 = '$idtahunbuku'
                   AND b.lunas = 0";

        $nData = $db->FetchSingle($sql, 0);
        if ($nData == 0)
        {
            echo "<br><br>";
            echo "<span style='color: maroon'>Tidak ditemukan data calon siswa yang belum melunasi tunggakan</span>";
            return;
        }

        $nPage = ceil($nData / $nRowPerPage);

        echo "<div id='dvDaftarCalon'>";
        ShowDaftarCalonSiswaTable($db);
        echo "</div>";

        echo "<input type='hidden' id='ndata' value='$nData'>";
        echo "<input type='hidden' id='npage' value='$nPage'>";

        echo "Halaman ";
        echo "<input type='button' class='but' value=' < ' style='width: 30px; height: 25px' onclick='onPrevPageCalonSiswa()'> ";
        echo "<select id='page' onchange='onChangePageCalonSiswa()' style='width: 60px' class='inputbox'>";
        for($i = 1; $i <= $nPage; $i++)
        {
            echo "<option value='$i'>$i</option>";
        }
        echo "</select>";
        echo "<input type='button' class='but' value=' > ' style='width: 30px; height: 25px' onclick='onNextPageCalonSiswa()'> ";
        echo " dari $nPage, jumlah $nData data";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k431x");
    }
    finally
    {
        $db->Close();
    }
}

function ShowDaftarCalonSiswaTable($db)
{
    global $nRowPerPage;
    global $idpenerimaan, $idtahunbuku, $page, $urut;

    $startIndex = ($page - 1) * $nRowPerPage;

    $sql = "SELECT DISTINCT cs.nopendaftaran, cs.nama, cs.replid 
              FROM jbsfina.besarjttcalon b, jbsakad.calonsiswa cs
             WHERE b.idpenerimaan = '$idpenerimaan' 
               AND b.info2 = '$idtahunbuku'
               AND b.lunas = 0
               AND b.idcalon = cs.replid
             ORDER BY $urut
             LIMIT $startIndex, $nRowPerPage";
    
    $res = $db->QueryDb($sql);

    echo "<table id='tabsiswa_daftar' class='tab' border='1' style='border-collapse:collapse' width='100%' align='center'>";
    echo "<tr align='center'>";
    echo "<td class='header-sm' width='10%'>No</td>";
    $color = ColumnColor($urut, "cs.nopendaftaran");
    echo "<td class='header-sm' width='40%' style='cursor: pointer; color: $color' onclick=\"tabcalon_changeUrut('cs.nopendaftaran')\">";
    echo "NIS";
    echo "</td>";
    $color = ColumnColor($urut, "cs.nama");
    echo "<td class='header-sm' width='*' style='cursor: pointer; color: $color' onclick=\"tabcalon_changeUrut('cs.nama')\">";
    echo "Nama";
    echo "</td>";
    echo "</tr>";

    $cnt = ($page - 1) * $nRowPerPage;
    while($row = mysqli_fetch_row($res))
    {
        $cnt += 1;

        $nic = $row[0];
        $nama = $row[1];
        $idcalon = $row[2];

        echo "<tr style='cursor: pointer' onclick='tabcalon_pilih(\"$nic\", \"$nama\", \"$idcalon\")'>";
        echo "<td class='numberColumn' align='center'>$cnt</td>";
        echo "<td colspan='2'><span style='color: blue'>$nic</span>&nbsp;&nbsp;&nbsp;$nama";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

function ColumnColor($urut, $urutBy)
{
    return ($urut == $urutBy) ? "#fffc00" : "#25f2ff";
}
?>
