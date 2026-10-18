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
function LoadValue($db)
{
    global $idPt, $dept;
    global $idTabungan, $rekKasVendor, $rekUtangVendor, $maxTransVendor, $isReadOnly;
    global $namaRekKasVendor, $namaRekUtangVendor;

    if ($idPt == 0)
        return;

    $sql = "SELECT pt.idtabungan, pt.rekkasvendor, pt.rekutangvendor, pt.maxtransvendor, 
                   ra1.nama AS namarekkasvendor, ra2.nama AS namarekutangvendor
              FROM jbsfina.paymenttabungan pt, jbsfina.rekakun ra1, jbsfina.rekakun ra2
             WHERE pt.rekkasvendor = ra1.kode
               AND pt.rekutangvendor = ra2.kode
               AND pt.replid = $idPt";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_array($res))
    {
        $idTabungan = $row['idtabungan'];
        $rekKasVendor = $row['rekkasvendor'];
        $rekUtangVendor = $row['rekutangvendor'];
        $maxTransVendor = $row['maxtransvendor'];
        $namaRekKasVendor = $row['namarekkasvendor'];
        $namaRekUtangVendor = $row['namarekutangvendor'];
    }

    $sql = "SELECT p.replid 
              FROM jbsfina.paymenttrans p, jbsakad.siswa s, jbsakad.angkatan a
             WHERE p.nis = s.nis
               AND s.idangkatan = a.replid
               AND a.departemen = '$dept'
               AND p.jenis = 2
             LIMIT 1  ";
    $res = $db->QueryDb($sql);
    $isReadOnly = mysqli_num_rows($res) > 0;
}

function ShowSelectTabunganSiswa($db)
{
    global $dept;
    global $idTabungan;
    global $isReadOnly;

    $sql = "SELECT replid, nama 
              FROM jbsfina.datatabungan 
             WHERE departemen = '$dept' 
               AND aktif = 1 
             ORDER BY nama";
    $res = $db->QueryDb($sql);

    $readOnly = $isReadOnly ? "disabled" : "";
    $bgColor = $isReadOnly ? "#ededed" : "#ffffff";

    echo "<select id='tabungan' name='tabungan' class='inputbox' style='width: 250px; background-color: $bgColor;' $readOnly>";
    while($row = mysqli_fetch_row($res))
    {
        $sel = $idTabungan == $row[0] ? "selected" : "";
        echo "<option value='$row[0]' $sel>$row[1]</option>";
    }
    echo "</select>";
}

function SimpanKonfigurasi()
{
    $db = new Db();
    try
    {
        $db->Open();

        $dept = $_REQUEST["dept"];
        $idPt = $_REQUEST["idpt"];
        $idTabungan = $_REQUEST["idtabungan"];
        $maxTrans = $_REQUEST["maxtrans"];
        $rekKasVendor = $_REQUEST["rekkas"];
        $rekUtangVendor = $_REQUEST["rekutang"];

        if ($idPt == 0)
        {
            $sql = "INSERT INTO jbsfina.paymenttabungan
                       SET departemen = '$dept', jenis = 2, idtabungan = $idTabungan,
                           rekkasvendor = '$rekKasVendor', rekutangvendor = '$rekUtangVendor', maxtransvendor = '$maxTrans'";
        }
        else
        {
            $sql = "UPDATE jbsfina.paymenttabungan
                       SET idtabungan = $idTabungan, rekkasvendor = '$rekKasVendor', rekutangvendor = '$rekUtangVendor', maxtransvendor = '$maxTrans'
                     WHERE replid = $idPt";
        }
        $db->QueryDb($sql);

        echo json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();

        echo json_encode([-99, Msg::InfoError($ex->getMessage(), "kckwz")]);
    }
    finally
    {
        $db->Close();
    }
}
?>