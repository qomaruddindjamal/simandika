<?php
/**[N]**
 * JIBAS Education Community
 * Jaringan Informasi Bersama Antar Sekolah
 *
 * @version: 23.0 (November 12, 2020)
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
require_once ("logger.php");

function SimpanJurnal2($db, $idtahunbuku, $tanggal, $transaksi, $nokas, $keterangan, $idpetugas, $petugas, $sumber)
{
    $idpetugas_value = $idpetugas == "landlord" ? "NULL" : "'$idpetugas'";

    $sql = "INSERT INTO jbsfina.jurnal
			   SET idtahunbuku = $idtahunbuku, tanggal = '$tanggal', transaksi = '$transaksi',
				   nokas = '$nokas', keterangan = '$keterangan',
				   idpetugas = $idpetugas_value, petugas = '$petugas', sumber = '$sumber'";
    $db->QueryDb($sql);

    $idjurnal = $db->InsertId();
    return $idjurnal;
}

function SimpanDetailJurnal2($db, $idjurnal, $align, $koderek, $jumlah)
{
    if ($align == "D")
    {
        $sql = "INSERT INTO jbsfina.jurnaldetail 
                   SET idjurnal = $idjurnal, koderek = '$koderek', debet = $jumlah";
    }
    else
    {
        $sql = "INSERT INTO jbsfina.jurnaldetail 
                   SET idjurnal = $idjurnal, koderek = '$koderek', kredit = $jumlah";
    }
    $db->QueryDb($sql);
    
}

// $kateakun bisa HARTA, PENDAPATAN, DISKON, PIUTANG
function AmbilKodeRekJurnal2($db, $idjurnal, $kateakun, $idpenerimaan)
{
    $kategori = ($kateakun == "DISKON") ? "PENDAPATAN" : $kateakun;

    if ($kateakun == "PENDAPATAN")
    {
        $sql = "SELECT koderek
				  FROM jbsfina.jurnaldetail jd, jbsfina.rekakun rk
				 WHERE jd.koderek = rk.kode
				   AND jd.idjurnal = '$idjurnal'
				   AND rk.kategori = '$kategori'
				   AND jd.debet = 0
				   AND jd.kredit > 0";
    }
    elseif ($kateakun == "DISKON")
    {
        $sql = "SELECT koderek
				  FROM jbsfina.jurnaldetail jd, jbsfina.rekakun rk
				 WHERE jd.koderek = rk.kode
				   AND jd.idjurnal = '$idjurnal'
				   AND rk.kategori = '$kategori'
				   AND jd.debet > 0
				   AND jd.kredit = 0";
    }
    else
    {
        $sql = "SELECT koderek
				  FROM jbsfina.jurnaldetail jd, jbsfina.rekakun rk
				 WHERE jd.koderek = rk.kode
				   AND jd.idjurnal = '$idjurnal'
				   AND rk.kategori = '$kategori'";
    }

    $res = $db->QueryDb($sql);
    if (mysqli_num_rows($res) == 0)
    {
        $colname = "";
        if ($kateakun == "HARTA")
            $colname = "rekkas";
        elseif ($kateakun == "PIUTANG")
            $colname = "rekpiutang";
        elseif ($kateakun == "PENDAPATAN")
            $colname = "rekpendapatan";
        elseif ($kateakun == "DISKON")
            $colname = "info1";

        $sql = "SELECT $colname
				  FROM jbsfina.datapenerimaan
				 WHERE replid = '$idpenerimaan'";
        //echo "$sql";
        $res = $db->QueryDb($sql);
        if (mysqli_num_rows($res) > 0)
        {
            $row = mysqli_fetch_row($res);
            return $row[0];
        }
        else
        {
            return "";
        }
    }
    else
    {
        $row = mysqli_fetch_row($res);
        return $row[0];
    }
}
?>
