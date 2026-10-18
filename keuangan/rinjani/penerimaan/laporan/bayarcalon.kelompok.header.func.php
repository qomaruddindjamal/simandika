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
function ShowSelectDepartemenLapBayarCalon($db)
{
    global $departemen;

    $dep = getDepartemen($db, getAccess());

    echo "<select name='departemen' id='departemen' style='width:180px' class='inputbox' onchange='onChangeDept();'>";
    foreach($dep as $value)
    {
        if ($departemen == "")
            $departemen = $value;
        echo "<option value='$value' " . StringIsSelected($value, $departemen) . ">$value</option>";
    }
    echo "</select>";
}

function ShowSelectProsesLapBayarCalon($db)
{
    global $departemen, $idProses;

    $sql = "SELECT replid, proses 
              FROM jbsakad.prosespenerimaansiswa 
             WHERE departemen = '$departemen' 
               AND aktif = 1 
             ORDER BY replid DESC";
    $res = $db->QueryDb($sql);

    echo "<select id='proses' onChange='onProsesChange()' class='inputbox' style='width:120px'>";
    while($row = mysqli_fetch_row($res))
    {
        if ($idProses == "")
            $idProses = $row[0];

        $sel = $idProses == $row[0] ? "selected" : "";
        echo "<option value='$row[0]' $sel>$row[1]</option>";
    }
    echo "</select>";
}

function ShowSelectKelompokLapBayarCalon($db)
{
    global $idTingkat, $idProses, $idKelompok;

    $sql = "SELECT k.replid, k.kelompok 
              FROM jbsakad.kelompokcalonsiswa k
             WHERE k.idproses = $idProses 
             ORDER BY k.kelompok";
    $res = $db->QueryDb($sql);

    echo "<select id='kelompok' onChange='showBlankPage()' class='inputbox' style='width:200px'>";
    echo "<option value='-1' selected>(Semua Kelompok)</option>";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}

function ShowSelectKategoriLapBayarCalon($db, $kode)
{
    global $idKategori;

    $sql = "SELECT kode, kategori 
              FROM jbsfina.kategoripenerimaan 
             WHERE kode IN ($kode) 
             ORDER BY urutan";
    $res = $db->QueryDb($sql);

    echo "<select id='kategori' onChange='onKategoriChange()' class='inputbox' style='width:180px'>";
    while($row = mysqli_fetch_row($res))
    {
        if ($idKategori == "")
            $idKategori = $row[0];

        $sel = $idKategori == $row[0] ? "selected" : "";
        echo "<option value='$row[0]' $sel>$row[1]</option>";
    }
    echo "</select>";
}

function ShowSelectPenerimaanLapBayarCalon($db)
{
    global $departemen, $idKategori;

    $sql = "SELECT replid, nama 
              FROM jbsfina.datapenerimaan 
             WHERE aktif = 1 
               AND idkategori = '$idKategori' 
               AND departemen = '$departemen' 
             ORDER BY nama";
    $res = $db->QueryDb($sql);

    echo "<select id='penerimaan' onChange='showBlankPage()' class='inputbox' style='width:400px'>";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}

function ShowSelectStatusLapBayarCalon()
{
    echo "<select id='status' class='inputbox' style='width: 160px' onchange='showBlankPage()'>";
    echo "<option value='-1'>(Semua)</option>";
    echo "<option value='0'>Belum Lunas</option>";
    echo "<option value='1'>Lunas</option>";
    echo "<option value='2'>Gratis</option>";
    echo "</select>";
}
?>