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
function ReloadBesarJtt()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idpenerimaan = $_REQUEST["idpenerimaan"];
        $idtahunbuku = $_REQUEST["idtahunbuku"];
        $nis = $_REQUEST["nis"];

        $payInfo = GetPaymentInfoJtt($db, $idpenerimaan, $idtahunbuku, $nis);
        ShowBesarJttInfo($payInfo);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kxrxp");
    }
    finally
    {
        $db->Close();
    }
}

function GetPaymentInfoJtt($db, $idpenerimaan, $idtahunbuku, $nis)
{
    $obj = new stdClass();

    $sql = "SELECT nama, rekkas, rekpendapatan, rekpiutang, info1 AS rekdiskon, info2 AS sendnotif
              FROM jbsfina.datapenerimaan 
             WHERE replid = '$idpenerimaan'";
    $result = $db->QueryDb($sql);
    if (mysqli_num_rows($result) == 0)
    {
        $obj->Exist = false;
        return $obj;
    }

    $row = mysqli_fetch_row($result);
    $obj->Exist = true;
    $obj->IdPenerimaan = $idpenerimaan;
    $obj->Penerimaan = $row[0];
    $obj->IdTahunBuku = $idtahunbuku;
    $obj->RekKas = $row[1];
    $obj->RekPendapatan = $row[2];
    $obj->RekPiutang = $row[3];
    $obj->RekDiskon = $row[4];
    $obj->SendNotif = $row[5];

    $sql = "SELECT b.replid AS id, b.besar, b.keterangan, b.lunas, b.info1 AS idjurnal, cicilan
	          FROM jbsfina.besarjtt b
		     WHERE b.nis = '$nis' 
		       AND b.idpenerimaan = '$idpenerimaan' 
		       AND b.info2 = '$idtahunbuku'";
    $res = $db->QueryDb($sql);
    if (mysqli_num_rows($res) == 0)
    {
        $obj->IdBesarJtt = 0;
    }
    else
    {
        $row = mysqli_fetch_row($res);
        $obj->IdBesarJtt = $row[0];
        $obj->Besar = $row[1];
        $obj->Keterangan = $row[2];
        $obj->Lunas = $row[3];
        $obj->IdJurnal = $row[4];
        $obj->Cicilan = $row[5];
    }

    return $obj;
}

function ShowBesarJttInfo($payInfo)
{
    if ($payInfo->IdBesarJtt == 0)
    {
        echo "<span style='color: maroon'>Besar pembayaran belum ditentukan</span><br>";
        echo "<input type='hidden' id='idbesarjtt' value='0'>";
        echo "<input type='button' class='dialogButtonPositive' value='atur' onclick='aturBesarJtt(0)'>";
    }
    else
    {
        echo "<input type='hidden' id='idbesarjtt' value='$payInfo->IdBesarJtt'>";
        echo "<input type='hidden' id='penerimaan' value='$payInfo->Penerimaan'>";
        echo "<input type='hidden' id='rekkas' value=' $payInfo->RekKas '>";
        echo "<input type='hidden' id='jcicilan' value=' $payInfo->Cicilan '>";
        echo "<input type='hidden' id='sendnotif' value=' $payInfo->SendNotif '>";

        echo "<table border='0' cellpadding='2'>";
        echo "<tr>";
        echo "<td style='width: 180px' valign='top'>";
        echo "<span style='color: #999'>Besar Pembayaran</span><br>";
        echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($payInfo->Besar) . " </span>&nbsp;&nbsp;";
        echo "<img src='../images/ico/ubah.png' class='hide-in-report' title='ubah' style='cursor: pointer' onclick='aturBesarJtt($payInfo->IdBesarJtt)'>";
        echo "</td>";
        echo "<td style='width: 180px' valign='top'>";
        echo "<span style='color: #999'>Besar Cicilan</span><br>";
        echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($payInfo->Cicilan) . " </span>";
        echo "</td>";
        echo "<td style='width: 180px' valign='top'>";
        echo "<span style='color: #999'>Status</span><br>";

	    if ($payInfo->Lunas == 1)
            echo "<span style='color: blue; font-size: 14px; font-weight: bold; padding: 2px'>Lunas</span>";
        else if ($payInfo->Lunas == 2)
            echo "<span style='color: brown; font-size: 14px; font-weight: bold; padding: 2px'>Gratis</span>";
        else
            echo "<span style='color: maroon; font-size: 14px; font-weight: bold; padding: 2px'>Belum Lunas</span>";

        echo "</td>";
        echo "<td style='width: 300px' valign='top'>";
        echo "<span style='color: #999'>Keterangan</span><br>";
        echo "<div style='overflow: auto; height: 40px;'>";
        if ($payInfo->Keterangan == "")
            echo "<span style='color: #333; font-size: 12px'>-</span>";
        else
            echo "<span style='color: #333; font-size: 12px'>$payInfo->Keterangan</span>";
        echo "</div>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    }
}

function ReloadRiwayatPembayaranJtt()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idpenerimaan = $_REQUEST["idpenerimaan"];
        $idtahunbuku = $_REQUEST["idtahunbuku"];
        $nis = $_REQUEST["nis"];

        $payInfo = GetPaymentInfoJtt($db, $idpenerimaan, $idtahunbuku, $nis);
        ShowRiwayatPembayaranJtt($db, $payInfo);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "krtyp");
    }
    finally
    {
        $db->Close();
    }

}

function ShowRiwayatPembayaranJtt($db, $payInfo)
{
    if ($payInfo->IdBesarJtt == 0 || $payInfo->Lunas == 2)
    {
        echo "&nbsp;";
        return;
    }

    $sql = "SELECT count(*) 
              FROM jbsfina.penerimaanjtt 
             WHERE idbesarjtt = '$payInfo->IdBesarJtt'";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);
    $nbayar = $row[0];

    if ($nbayar == 0)
    {
        echo "<table width='100%' border='0' align='center'>";
        echo "<tr>";
        echo "<td align='center' valign='middle' height='100'>";
        echo "<font size = '2' color ='red'><b>Tidak ditemukan adanya data.";
        echo "<br />Klik &nbsp;<a href='JavaScript:showPembayaran(0)'><font size = '2' color ='green'>di sini</font></a>&nbsp;untuk melakukan pembayaran pertama.";
        echo "</b></font>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    }
    else
    {
        $sql = "SELECT COUNT(p.replid), SUM(p.jumlah + p.info1) AS totjumlah, SUM(p.info1) AS todiskon
                  FROM jbsfina.penerimaanjtt p, jbsfina.besarjtt b
                 WHERE p.idbesarjtt = b.replid
                   AND b.replid = '$payInfo->IdBesarJtt'";
        $result = $db->QueryDb($sql);
        $row = mysqli_fetch_row($result);
        $nData = (int) $row[0];
        $totJumlah = (int) $row[1];
        $totDiskon = (int) $row[2];

        echo "<table border='0' cellpadding='0' cellspacing='0' width='95%'>";
        echo "<tr>";
        echo "<td width='60%' align='left' valign='top'>";

        echo "<table border='0' cellspacing='0' cellpadding='5'>";
        echo "<tr>";
        echo "<td style='width: 200px'>";
        echo "<span style='color: #666'>Total Pembayaran</span><br>";
        echo "<span style='font-size: 14px; font-weight: bold' id='spTotalPembayaran'>" . FormatRupiah($totJumlah). "</span>";
        echo "</td>";
        echo "<td style='width: 200px'>";
        echo "<span style='color: #666'>Total Diskon</span><br>";
        echo "<span style='font-size: 14px; font-weight: bold' id='spTotalDiskon'>" . FormatRupiah($totDiskon). "</span>";
        echo "</td>";
        echo "<td style='width: 250px'>";
        echo "<span style='color: #666'>Sisa Pembayaran</span><br>";
        echo "<span style='font-size: 14px; font-weight: bold' id='spSisaPembayaran'></span>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";

        echo "</td>";
        echo "<td width='40%' align='right' valign='bottom'>";

        echo "<div style='width: 100%; text-align: right; padding: 5px;' class='hide-in-report'>";
        echo "<a href='#' onClick='reloadRiwayatJtt()'><img src='../images/ico/refresh.png' title='refresh' border='0'/>&nbsp;refresh</a>&nbsp;&nbsp;";
        echo "<a href='#' onClick='cetakHalaman()'><img src='../images/ico/print.png' title='cetak' border='0'/>&nbsp;cetak</a>&nbsp;&nbsp;";
        if ($payInfo->Lunas == 0)
        {
            echo "<a href='#' onClick='showPembayaran(0)'>";
            echo "<img src='../images/ico/tambah.png' border='0'>&nbsp;terima pembayaran</a>&nbsp;";
            echo "</a>";
        }
        echo "</div>";

        echo "</td>";
        echo "</tr>";
        echo "</table>";


        $sql = "SELECT p.replid AS id, j.nokas, date_format(p.tanggal, '%d-%b-%Y') as tanggal,
                       p.keterangan, p.jumlah, p.petugas, p.info1 AS diskon, jd.koderek AS rekkas, ra.nama AS namakas,
                       IFNULL(p.sumberdana, 'Tidak ada data') AS fsumberdana, p.jam
                  FROM jbsfina.penerimaanjtt p, jbsfina.besarjtt b, jbsfina.jurnal j, 
                       jbsfina.jurnaldetail jd, jbsfina.rekakun ra
                 WHERE p.idbesarjtt = b.replid
                   AND j.replid = p.idjurnal
                   AND j.replid = jd.idjurnal
                   AND jd.koderek = ra.kode
                   AND ra.kategori = 'HARTA'
                   AND b.replid = '$payInfo->IdBesarJtt'
                 ORDER BY p.replid DESC";
        $result = $db->QueryDb($sql);

        echo "<div style='width: 100%; overflow: auto; max-height: 400px;'>";

        echo "<table class='tab' id='tableRiwayat' border='1' style='border-collapse:collapse'>";
        echo "<tr height='30' align='center'>";
        echo "<td class='header' width='30'>No</td>";
        echo "<td class='header' width='120'>No. Jurnal/Tgl</td>";
        echo "<td class='header' width='150'>Jumlah</td>";
        echo "<td class='header' width='150'>Diskon</td>";
        echo "<td class='header' width='350'>Informasi</td>";
        echo "<td class='header' width='70'>&nbsp;</td>";
        echo "</tr>";

        $cnt = $nData + 1;
        while ($row = mysqli_fetch_array($result))
        {
            $cnt -= 1;

            // 2020-10-05 Check SchoolPay Transaction
            $id = $row['id'];
            $sql = "SELECT jenistrans
                      FROM jbsfina.paymenttrans
                     WHERE idpenerimaanjtt = $id";
            $res2 = $db->QueryDb($sql);
            $isSchoolPay = mysqli_num_rows($res2) > 0;
            $infoSchoolPay = "";
            if ($row2 = mysqli_fetch_row($res2))
            {
                $jenisTrans = $row2[0];
                if ($jenisTrans == 0)
                    $jenisTrans = "&nbsp;SchoolPay&nbsp;<span style='background-color: #636363; color: #ffffff'>&nbsp;Vendor&nbsp;</span>";
                else if ($jenisTrans == 1)
                    $jenisTrans = "&nbsp;SchoolPay&nbsp;<span style='background-color: #47973c; color: #ffffff'>&nbsp;Iuran&nbsp;</span>";
                else if ($jenisTrans == 2)
                    $jenisTrans = "&nbsp;SchoolPay&nbsp;<span style='background-color: #9f4aa3; color: #ffffff'>&nbsp;Iuran&nbsp;</span>";

                $infoSchoolPay = $isSchoolPay ? "<span style='background-color: #296eeb; color: #ffffff; font-size: 10px;'>$jenisTrans</span>" : "";
            }

            // 2023-08-09
            if ($infoSchoolPay == "")
            {
                $sql = "SELECT COUNT(replid)
                          FROM jbsfina.pgtransdata
                         WHERE idpenerimaanjtt = $id";
                $res2 = $db->QueryDb($sql);
                $row2 = mysqli_fetch_row($res2);
                if ($row2[0] > 0)
                {
                    $infoSchoolPay = "<span style='background-color: #43b9c9; color: #ffffff; font-size: 10px;'>&nbsp;OnlinePay&nbsp;</span>";
                }
            }

            echo "<tr height='25'>";
            echo "<td align='center' class='numberColumn'>$cnt</td>";
            echo "<td align='center'><strong>$row[nokas]</strong><br><i>$row[tanggal] $row[jam]</i></td>";
            echo "<td align='right' class='bg-light-blue'><b>" . FormatRupiah($row['jumlah'] + $row['diskon']) . "<br> $infoSchoolPay </b></td>";
            echo "<td align='right' class='bg-light-green'><b>" . FormatRupiah($row['diskon']) . "</b></td>";
            echo "<td align='left'>";
            echo "<b>Rek Kas</b>: $row[rekkas] $row[namakas]<br>";
            echo "<b>Sumber Dana</b>: $row[fsumberdana]<br>";
            echo "<b>Petugas</b>: $row[petugas]<br>";
            echo "<i>$row[keterangan]</i>";
            echo "</td>";
            echo "<td align='center'>";
            echo "<div class='hide-in-report'>";
            echo "<a href='#' onclick='cetakKuitansi($row[id])'><img src='../images/ico/print.png' border='0'/></a>&nbsp;";
            if (getLevel() != 2)
                echo "<a href='#' onclick='showPembayaran($row[id])'><img src='../images/ico/ubah.png' border='0' /></a>";
            echo "</div>";
            echo "</td>";
            echo "</tr>";
        }
        $sisa = $payInfo->Besar - $totJumlah;
        echo "<input type='hidden' id='sisapembayaran' value='" . FormatRupiah($sisa) . "'>";

        echo "</table>";
        echo "</div>";

    }
}
?>
