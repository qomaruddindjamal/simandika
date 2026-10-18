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
require_once ("../library/msg.php");

function ShowSelectDepartemen_BYR($db)
{
    global $departemen;

    try
    {
        echo "<select name='departemen' id='departemen' onChange='change_dep()' class='inputbox' style='width:200px'>";
        $dep = getDepartemen($db, getAccess());
        foreach($dep as $value)
        {
            if ($departemen == "") $departemen = $value;
            $sel = $departemen == $value ? "selected" : "";
            echo "<option value='$value' $sel>$value</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kv16p");
    }
}

function ShowTahunBuku_BYR($db)
{
    global $departemen;

    try
    {
        $idTahunBuku = 0;
        $tahunBuku = "";

        $sql = "SELECT replid, tahunbuku 
                  FROM jbsfina.tahunbuku 
                 WHERE aktif = 1 
                   AND departemen = '$departemen'";
        $res = $db->QueryDb($sql);
        if ($row = mysqli_fetch_row($res))
        {
            $idTahunBuku = $row[0];
            $tahunBuku = $row[1];
        }

        echo "<input type='text' id='tahunbuku' style='width: 180px; background-color: #efefef' readonly class='inputbox' value='$tahunBuku'>";
        echo "<input type='hidden' id='idtahunbuku' value='$idTahunBuku'>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kqp73");
    }
}

function ShowSelectKategoriPenerimaan_BYR($db)
{
    global $idkategori;

    try
    {
        $sql = "SELECT kode, kategori 
                  FROM jbsfina.kategoripenerimaan 
                 ORDER BY urutan";
        $res = $db->QueryDb($sql);

        echo "<select name='idkategori' id='idkategori' onChange='change_kate()' class='inputbox' style='width:200px'>";
        while($row = mysqli_fetch_row($res))
        {
            if ($idkategori == "") $idkategori = $row[0];
            $sel = $idkategori == $row[0] ? "selected" : "";
            echo "<option value='$row[0]' $sel>$row[1]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kmnhj");
    }
}

function ShowSelectJenisPenerimaan_BYR($db)
{
    global $idkategori, $departemen;

    try
    {
        $sql = "SELECT replid, nama 
                  FROM jbsfina.datapenerimaan 
                 WHERE aktif = 1 
                   AND idkategori = '$idkategori' 
                   AND departemen = '$departemen' 
                 ORDER BY nama";
        $res = $db->QueryDb($sql);

        echo "<select name='idpenerimaan' id='idpenerimaan' onChange='change_penerimaan()' class='inputbox' style='width:300px'>";
        while($row = mysqli_fetch_row($res))
        {
            echo "<option value='$row[0]'>$row[1]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kxexd");
    }
}
?>