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
function ShowInfoSiswa()
{
    global $noid, $nama, $kelompok;

    if ($kelompok == "siswa")
    {
        $sql = "SELECT replid FROM jbsakad.siswa WHERE nis = '$noid'";
        $table = "jbsakad.siswa";
    }
    else
    {
        $sql = "SELECT replid FROM jbsakad.calonsiswa WHERE nopendaftaran = '$noid'";
        $table = "jbsakad.calonsiswa";
    }
    $replid = FetchSingle($sql);
    ?>

    <center>
        <img src='<?= "../library/gambar.php?replid=$replid&table=$table" ?>' height='80'><br>
    </center>

    <?php
}

function ShowSelectJenisPayment()
{
    global $jenisp;

    echo "<span style='display:inline-block; width: 85px'>Kategori:</span><select name='kate' id='kate' onchange='ChangeKate()' onkeyup='ChangeKate()' class='inputbox' style='width: 180px'>\r\n";
    foreach($jenisp as $key => $value)
    {
        echo "<option value='$key'>$value</option>\r\n";
    }
    echo "</select>\r\n";
}

function ShowSelectPrevPaymentSiswa($nis)
{
    global $idtahunbuku;

    $sql = "SELECT p.replid, p.nama
              FROM jbsfina.besarjtt b, jbsfina.datapenerimaan p
             WHERE b.idpenerimaan = p.replid
               AND b.nis = '$nis'
               AND b.lunas = 0
               AND b.info2 = $idtahunbuku
             ORDER BY p.nama";
    $res = QueryDb($sql);

    echo "<span style='display:inline-block; width: 85px'>Pembayaran:</span><select id='payment'  class='inputbox' style='width: 250px'  onchange='ChangePayment()' onkeyup='ChangePayment()'>\r\n";
    echo "<option value='0'>--Pilih Pembayaran--</option>\r\n";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>\r\n";
    }
    echo "</select>\r\n";
}

function ShowSelectPrevPaymentCalonSiswa($idCalon)
{
    global $idtahunbuku;

    $sql = "SELECT p.replid, p.nama
              FROM jbsfina.besarjttcalon b, jbsfina.datapenerimaan p
             WHERE b.idpenerimaan = p.replid
               AND b.idcalon = '$idCalon'
               AND b.lunas = 0
               AND b.info2 = $idtahunbuku
             ORDER BY p.nama";
    $res = QueryDb($sql);

    echo "<span style='display:inline-block; width: 85px'>Pembayaran:</span><select id='payment'  class='inputbox' style='width: 250px'  onchange='ChangePayment()' onkeyup='ChangePayment()'>\r\n";
    echo "<option value='0'>--Pilih Pembayaran--</option>\r\n";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>\r\n";
    }
    echo "</select>\r\n";
}
?>