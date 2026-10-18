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
function ShowSelectPengeluaran()
{
    global $departemen;

    $sql = "SELECT replid, nama
              FROM jbsfina.datapengeluaran
             WHERE departemen = '$departemen'
               AND aktif = 1
             ORDER BY nama";
    $res = QueryDb($sql);

    echo "<span style='display:inline-block; width: 85px'>Pengeluaran:</span>";
    echo "<select name='pengeluaran' id='pengeluaran'  class='inputbox' style='width: 250px' onchange='ChangePengeluaran()'>\r\n";
    echo "<option value='0'>--Pilih Pengeluaran--</option>\r\n";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>\r\n";
    }
    echo "</select>\r\n";
}

function ShowSelectPengguna()
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = $_REQUEST["departemen"];
        $sql = "SELECT DISTINCT namapemohon
                  FROM jbsfina.pengeluaran p, jbsfina.datapengeluaran dp
                 WHERE p.idpengeluaran = dp.replid
                   AND dp.departemen = '$departemen'
                   AND namapemohon IS NOT NULL 
                   AND LENGTH(namapemohon) > 0
                 ORDER BY namapemohon";
        $res = $db->QueryDb($sql);

        echo "<b>Pengguna dana:</b><br><br>";
        echo "<select id='slpengguna' class='inputbox' style='width: 250px''>\r\n";
        while($row = mysqli_fetch_row($res))
        {
            echo "<option value='$row[0]'>$row[0]</option>\r\n";
        }
        echo "</select><br><br>";
        echo "<input type='button' class='but' value='Pilih' style='width: 60px; height: 25px;' onclick='pilihPengguna();'>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kgu7j");
    }
    finally
    {
        $db->Close();
    }
}

function ShowSelectPenerima()
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = $_REQUEST["departemen"];
        $sql = "SELECT DISTINCT penerima
                  FROM jbsfina.pengeluaran p, jbsfina.datapengeluaran dp
                 WHERE p.idpengeluaran = dp.replid
                   AND dp.departemen = '$departemen'
                   AND penerima IS NOT NULL 
                   AND LENGTH(penerima) > 0
                 ORDER BY penerima";
        $res = $db->QueryDb($sql);

        echo "<b>Penerima dana:</b><br><br>";
        echo "<select id='slpenerima' class='inputbox' style='width: 250px''>\r\n";
        while($row = mysqli_fetch_row($res))
        {
            echo "<option value='$row[0]'>$row[0]</option>\r\n";
        }
        echo "</select><br><br>";
        echo "<input type='button' class='but' value='Pilih' style='width: 60px; height: 25px;' onclick='pilihPenerima();'>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kgu7j");
    }
    finally
    {
        $db->Close();
    }
}
?>