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
function ShowSelectRekKas($defrekkas)
{
    echo "<select name='rekkas' id='rekkas' class='inputbox' style='width: 220px'>\r\n";
    $sql = "SELECT kode, nama
              FROM jbsfina.rekakun
             WHERE kategori = 'HARTA'
             ORDER BY nama";
    $res = QueryDb($sql);
    while($row = mysqli_fetch_row($res))
    {
        $sel = $row[0] == $defrekkas ? "selected" : "";
        echo "<option value='$row[0]' $sel>$row[0] $row[1]</option>";
    }
    echo "</select>";
}

function InfoWjbSiswa()
{
    global $noid, $idpayment, $idtahunbuku, $tag_mandatory;

    $sql = "SELECT rekkas
              FROM jbsfina.datapenerimaan
             WHERE replid = '$idpayment'";
    $defrekkas = FetchSingle($sql);

    $sql = "SELECT b.replid AS id, b.besar, b.keterangan, b.lunas, b.info1 AS idjurnal, cicilan
              FROM besarjtt b
             WHERE b.nis = '$noid'
               AND b.idpenerimaan = '$idpayment'
               AND b.info2 = '$idtahunbuku'";
    //echo "$sql<br>";
    $result = QueryDb($sql);
    $newdata = (mysqli_num_rows($result) == 0);

    $idbesarjtt = 0;
    $lunas = 0;
    $tagihan = 0;
    $bcicilan = 0;
    $keterangan = "";
    $idjurnal = 0;
    $sisa = 0;
    $nbayar = 0;

    if (!$newdata)
    {
        $row = mysqli_fetch_array($result);

        $idbesarjtt = $row['id'];
        $lunas = $row['lunas'];
        $tagihan = $row['besar'];
        $bcicilan = $row['cicilan'];
        $keterangan = $row['keterangan'];
        $idjurnal = $row['idjurnal'];

        $sql = "SELECT SUM(jumlah) AS jumlah, SUM(info1) AS diskon
			  	  FROM penerimaanjtt
				 WHERE idbesarjtt = '$idbesarjtt'";
        $result = QueryDb($sql);
        $row = mysqli_fetch_row($result);
        $jbayar = $row[0];
        $jdiskon = $row[1];
        $sisa = $tagihan - $jbayar - $jdiskon;

        $sql = "SELECT COUNT(replid)
			  	  FROM penerimaanjtt
				 WHERE idbesarjtt = '$idbesarjtt'";
        $nbayar = FetchSingle($sql);
    }

    if ($lunas == 2)
        $statuslunas = "<font color='brown'><strong>GRATIS</strong></font>";
    else if ($lunas == 1)
        $statuslunas = "<font color='blue'><strong>LUNAS</strong></font>";
    else
        $statuslunas = "<font color='red'><strong>BELUM LUNAS</strong></font>";

    if ($idbesarjtt == 0)
    {
        $infocicil = "Pembayaran ke-1";
        $ncicil = 1;
        $tagihan = "";
        $bcicilan = "";
        $keterangan = "";
        $sisa = "";
        $ro = "";
        $rostyle = "";
    }
    else
    {
        $infocicil = "Pembayaran ke-" . ($nbayar + 1);
        $ncicil = $nbayar + 1;
        $tagihan = FormatRupiah($tagihan);
        $bcicilan = FormatRupiah($bcicilan);
        $sisa = FormatRupiah($sisa);
        $ro = "readonly";
        $rostyle = "style='background-color: #CCCC99;'";
    }

    echo "<table border='0' cellpadding='3' cellspacing='0'>\r\n";
    echo "<tr>\r\n";
    echo "<td align='left' colspan='2'>\r\n";
    echo "<span style='font-size: 12px; font-weight: bold;'>Informasi Tagihan</span>\r\n";
    echo "<input type='hidden' id='idbesarjtt' value='$idbesarjtt'>\r\n";
    echo "</td>\r\n";
    echo "</tr>\r\n";
    echo "<tr>\r\n";
    echo "<td align='right' width='80'>Status:</td>\r\n";
    echo "<td align='left'>&nbsp;&nbsp;$statuslunas</td>\r\n";
    echo "</tr>\r\n";
    echo "<tr>\r\n";
    echo "<td align='right'>Total Tagihan:</td>\r\n";
    echo "<td align='left'>\r\n";
    echo "&nbsp;&nbsp;<input type='text' name='tagihan' id='tagihan' class='inputbox' style='width: 200px' $ro $rostyle value='$tagihan' onblur=\"formatRupiah('tagihan');\" onfocus=\"unformatRupiah('tagihan')\">\r\n";
    echo "</td>\r\n";
    echo "</tr>\r\n";
    echo "<tr>\r\n";
    echo "<td align='right'>Besar Cicilan:</td>\r\n";
    echo "<td align='left'>\r\n";
    echo "&nbsp;&nbsp;<input type='text' name='bcicilan' id='bcicilan' class='inputbox' style='width: 200px' $ro $rostyle value='$bcicilan' onblur=\"formatRupiah('bcicilan');\" onfocus=\"unformatRupiah('bcicilan')\">\r\n";
    echo "</td>\r\n";
    echo "</tr>\r\n";
    echo "<tr>\r\n";
    echo "<td align='right'>Keterangan:</td>\r\n";
    echo "<td align='left'>\r\n";
    echo "&nbsp;&nbsp;<input type='text' name='ktagihan' id='ktagihan' class='inputbox' style='width: 250px' $ro $rostyle value='$keterangan'>\r\n";
    echo "</td>\r\n";
    echo "</tr>\r\n";

    if ($idbesarjtt > 0)
    {
        echo "<tr>\r\n";
        echo "<td align='right'>Sisa:</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<input type='text' name='sisa' id='sisa' readonly class='inputbox inputbox-readonly' style='width: 200px' value='$sisa'>\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";

        echo "<tr>\r\n";
        echo "<td align='right'>&nbsp;</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<a href=\"JavaScript:showRiwayatPembayaran('JTT', '$noid', 0, $idpayment, $idtahunbuku)\" style='font-weight: normal; text-decoration: underline; color: blue'>riwayat pembayaran</a>\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";
    }


    if ($lunas == 0)
    {
        echo "<tr>\r\n";
        echo "<td align='left' colspan='2'><br><span style='font-size: 12px; font-weight: bold;'>$infocicil</span></td>\r\n";
        echo "<input type='hidden' id='ncicil' name='ncicil' value='$ncicil'>\r\n";
        echo "</tr>\r\n";
        echo "<tr>\r\n";
        echo "<td align='right'>Cicilan:$tag_mandatory</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<input type='text' name='jcicilan' id='jcicilan' class='inputbox' value='$bcicilan' maxlength='12' style='width: 200px' onblur=\"CalculatePay(); formatRupiah('jcicilan');\" onfocus=\"unformatRupiah('jcicilan')\">\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";
        echo "<tr>\r\n";
        echo "<td align='right'>Diskon:</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<input type='text' name='jdiskon' id='jdiskon' class='inputbox' style='width: 200px' maxlength='12' value='Rp 0' onblur=\"CalculatePay(); formatRupiah('jdiskon');\" onfocus=\"unformatRupiah('jdiskon')\">\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";
        echo "<tr>\r\n";
        echo "<td align='right'>Bayar:</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<input type='text' name='jbayar' id='jbayar' class='inputbox inputbox-readonly' value='$bcicilan' style='width: 200px' readonly size='15'>\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";
        echo "<tr>\r\n";
        echo "<td align='right'>Rek. Kas:$tag_mandatory</td>\r\n";
        echo "<td align='left'>&nbsp;\r\n";
        ShowSelectRekKas($defrekkas);
        echo "</td>\r\n";
        echo "</tr>\r\n";
        echo "<tr>\r\n";
        echo "<td align='right'>Keterangan:</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<input type='text' name='kcicilan' id='kcicilan'  class='inputbox' maxlength='255' style='width: 280px'>\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";

        echo "<tr>\r\n";
        echo "<td align='left' colspan='2'>\r\n";
        echo "<input type='button' class='dialogButtonPositive' style='height: 30px' value='Tambah ke Daftar Pembayaran >' onclick='AddToPaymentList()'>";
        echo "</td>\r\n";
        echo "</tr>\r\n";
    }

    echo "</table>\r\n";
    /*
    echo "Lunas = $lunas<br>";
    echo "Besar = $besar<br>";
    echo "Sisa = $sisa<br>";
    echo "IdBesarJtt = $idbesarjtt<br>";
    */
}

function InfoWjbCalonSiswa()
{
    global $noid, $idpayment, $idtahunbuku, $tag_mandatory;

    $sql = "SELECT rekkas
              FROM jbsfina.datapenerimaan
             WHERE replid = '$idpayment'";
    $defrekkas = FetchSingle($sql);

    $sql = "SELECT replid
			  FROM jbsakad.calonsiswa
			 WHERE nopendaftaran = '$noid' ";
    $idcalon = FetchSingle($sql);

    $sql = "SELECT b.replid AS id, b.besar, b.keterangan, b.lunas, b.info1 AS idjurnal, cicilan
              FROM besarjttcalon b
             WHERE b.idcalon = '$idcalon'
               AND b.idpenerimaan = '$idpayment'
               AND b.info2 = '$idtahunbuku'";
    $result = QueryDb($sql);
    $newdata = (mysqli_num_rows($result) == 0);

    $idbesarjtt = 0;
    $lunas = 0;
    $tagihan = 0;
    $bcicilan = 0;
    $keterangan = "";
    $idjurnal = 0;
    $sisa = 0;
    $nbayar = 0;

    if (!$newdata)
    {
        $row = mysqli_fetch_array($result);

        $idbesarjtt = $row['id'];
        $lunas = $row['lunas'];
        $tagihan = $row['besar'];
        $bcicilan = $row['cicilan'];
        $keterangan = $row['keterangan'];
        $idjurnal = $row['idjurnal'];

        $sql = "SELECT SUM(jumlah) AS jumlah, SUM(info1) AS diskon
			  	  FROM penerimaanjttcalon
				 WHERE idbesarjttcalon = '$idbesarjtt'";
        $result = QueryDb($sql);
        $row = mysqli_fetch_row($result);
        $jbayar = $row[0];
        $jdiskon = $row[1];
        $sisa = $tagihan - $jbayar - $jdiskon;

        $sql = "SELECT COUNT(replid)
			  	  FROM penerimaanjttcalon
				 WHERE idbesarjttcalon = '$idbesarjtt'";
        $nbayar = FetchSingle($sql);
    }

    if ($lunas == 2)
        $statuslunas = "<font color='brown'><strong>GRATIS</strong></font>";
    else if ($lunas == 1)
        $statuslunas = "<font color='blue'><strong>LUNAS</strong></font>";
    else
        $statuslunas = "<font color='red'><strong>BELUM LUNAS</strong></font>";

    if ($idbesarjtt == 0)
    {
        $infocicil = "Cicilan ke-1";
        $ncicil = 1;
        $tagihan = "";
        $bcicilan = "";
        $keterangan = "";
        $sisa = "";
        $ro = "";
        $rostyle = "";
    }
    else
    {
        $infocicil = "Cicilan ke-" . ($nbayar + 1);
        $ncicil = $nbayar + 1;
        $tagihan = FormatRupiah($tagihan);
        $bcicilan = FormatRupiah($bcicilan);
        $sisa = FormatRupiah($sisa);
        $ro = "readonly";
        $rostyle = "style='background-color: #CCCC99;'";
    }

    echo "<table border='0' cellpadding='3' cellspacing='0'>\r\n";
    echo "<tr>\r\n";
    echo "<td align='left' colspan='2'>\r\n";
    echo "<span style='font-size: 12px; font-weight: bold;'>Informasi Tagihan</span>\r\n";
    echo "<input type='hidden' id='idbesarjtt' name='idbesarjtt' value='$idbesarjtt'>";
    echo "</td>\r\n";
    echo "</tr>\r\n";

    echo "<tr>\r\n";
    echo "<td align='right' width='80'>Status:</td>\r\n";
    echo "<td align='left'>&nbsp;&nbsp;$statuslunas</td>\r\n";
    echo "</tr>\r\n";

    echo "<tr>\r\n";
    echo "<td align='right' width='80'>Total Tagihan:$tag_mandatory</strong></td>\r\n";
    echo "<td align='left'>\r\n";
    echo "&nbsp;&nbsp;<input type='text' name='tagihan' id='tagihan' class='inputbox' style='width: 200px' $ro $rostyle value='$tagihan' onblur=\"formatRupiah('tagihan');\" onfocus=\"unformatRupiah('tagihan')\">\r\n";
    echo "</td>\r\n";
    echo "</tr>\r\n";

    echo "<tr>\r\n";
    echo "<td align='right'>Besar Cicilan:$tag_mandatory</td>\r\n";
    echo "<td align='left'>\r\n";
    echo "&nbsp;&nbsp;<input type='text' name='bcicilan' id='bcicilan' class='inputbox' style='width: 200px' $ro $rostyle value='$bcicilan' onblur=\"formatRupiah('bcicilan');\" onfocus=\"unformatRupiah('bcicilan')\">\r\n";
    echo "</td>\r\n";
    echo "</tr>\r\n";

    echo "<tr>\r\n";
    echo "<td align='right'>Keterangan:</td>\r\n";
    echo "<td align='left'>\r\n";
    echo "&nbsp;&nbsp;<input type='text' name='ktagihan' id='ktagihan' class='inputbox' style='width: 260px' $ro $rostyle value='$keterangan'>\r\n";
    echo "</td>\r\n";
    echo "</tr>\r\n";

    if ($idbesarjtt > 0)
    {
        echo "<tr>\r\n";
        echo "<td align='right'>Sisa:</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<input type='text' name='sisa' id='sisa' readonly class='inputbox inputbox-readonly' style='width: 200px' value='$sisa'>\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";

        echo "<tr>\r\n";
        echo "<td align='right'>&nbsp;</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<a href=\"JavaScript:showRiwayatPembayaran('CSWJB', '$noid', $idcalon, $idpayment, $idtahunbuku)\" style='font-weight: normal; text-decoration: underline; color: blue'>riwayat pembayaran</a>\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";
    }


    if ($lunas == 0)
    {
        echo "<tr>\r\n";
        echo "<td align='left' colspan='2'>\r\n";
        echo "<span style='font-size: 12px; font-weight: bold;'>$infocicil</span>\r\n";
        echo "<input type='hidden' id='ncicil' name='ncicil' value='$ncicil'>\r\n";
        echo "</tr>\r\n";

        echo "<tr>\r\n";
        echo "<td align='right'>Cicilan:$tag_mandatory</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<input type='text' name='jcicilan' id='jcicilan' class='inputbox' style='width: 200px' value='$bcicilan' onblur=\"CalculatePay(); formatRupiah('jcicilan');\" onfocus=\"unformatRupiah('jcicilan')\">\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";

        echo "<tr>\r\n";
        echo "<td align='right'>Diskon:</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<input type='text' name='jdiskon' id='jdiskon' class='inputbox' style='width: 200px' value='Rp 0' onblur=\"CalculatePay(); formatRupiah('jdiskon');\" onfocus=\"unformatRupiah('jdiskon')\">\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";
        echo "<tr>\r\n";
        echo "<td align='right'>Bayar:</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<input type='text' name='jbayar' id='jbayar' readonly class='inputbox inputbox-readonly' value='$bcicilan' style='width: 200px'>\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";

        echo "<tr>\r\n";
        echo "<td align='right'><strong>Rek. Kas:</strong></td>\r\n";
        echo "<td align='left'>&nbsp;\r\n";
        ShowSelectRekKas($defrekkas);
        echo "</td>\r\n";
        echo "</tr>\r\n";

        echo "<tr>\r\n";
        echo "<td align='right'>Keterangan:</td>\r\n";
        echo "<td align='left'>\r\n";
        echo "&nbsp;&nbsp;<input type='text' name='kcicilan' id='kcicilan' class='inputbox' style='width: 260px'>\r\n";
        echo "</td>\r\n";
        echo "</tr>\r\n";

        echo "<tr>\r\n";
        echo "<td align='left' colspan='2'>\r\n";
        echo "<input type='button' class='dialogButtonPositive' style='height: 30px' value='Tambah ke Daftar Pembayaran >' onclick='AddToPaymentList()'>";
        echo "</td>\r\n";
        echo "</tr>\r\n";
    }

    echo "</table>\r\n";
    /*
    echo "Lunas = $lunas<br>";
    echo "Besar = $besar<br>";
    echo "Sisa = $sisa<br>";
    echo "IdBesarJtt = $idbesarjtt<br>";
    */
}
?>