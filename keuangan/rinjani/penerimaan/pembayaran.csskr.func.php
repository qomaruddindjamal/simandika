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
function GetPaymentInfoCsSkr($db, $idpenerimaan, $idtahunbuku)
{
    $obj = new stdClass();

    $sql = "SELECT nama, rekkas, rekpendapatan, info2 AS sendnotif 
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
    $obj->SendNotif = $row[3];

    return $obj;
}

function ShowRiwayatPembayaranCsSkr($db, $idCalon, $idPenerimaan, $idTahunBuku)
{
    try
    {
        $sql = "SELECT COUNT(p.replid), SUM(p.jumlah)
                  FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j 
                 WHERE j.replid = p.idjurnal
                   AND j.idtahunbuku = '$idTahunBuku'
                   AND p.idpenerimaan = '$idPenerimaan'
                   AND p.idcalon = '$idCalon'";
        $result = $db->QueryDb($sql);
        $row = mysqli_fetch_row($result);
        $nData = (int)$row[0];
        $totJumlah = (int)$row[1];

        if ($nData == 0)
        {
            echo "<table width='780' border='0' align='center'>";
            echo "<tr>";
            echo "<td align='center' valign='middle' height='100'>";
            echo "<font size = '2' color ='red'><b>Tidak ditemukan adanya data.";
            echo "<span class='hide-in-report'><br />Klik &nbsp;<a href='JavaScript:showPembayaran(0)'><font size = '2' color ='green'>di sini</font></a>&nbsp;untuk melakukan pembayaran pertama.</span>";
            echo "</b></font>";
            echo "</td>";
            echo "</tr>";
            echo "</table>";
        }
        else
        {
            echo "<table border='0' cellpadding='0' cellspacing='0' width='780'>";
            echo "<tr>";
            echo "<td width='400' align='left' valign='top'>";

            echo "<table border='0' cellspacing='0' cellpadding='5'>";
            echo "<tr>";
            echo "<td style='width: 200px'>";
            echo "<span style='color: #666'>Total Pembayaran</span><br>";
            echo "<span style='font-size: 14px; font-weight: bold'>" . FormatRupiah($totJumlah) . "</span>";
            echo "</td>";
            echo "</tr>";
            echo "</table>";

            echo "</td>";
            echo "<td width='380' align='right' valign='bottom'>";

            echo "<div style='width: 100%; text-align: right; padding: 5px;' class='hide-in-report'>";
            echo "<a href='#' onClick='reloadRiwayatCsSkr()'><img src='../images/ico/refresh.png' title='refresh' border='0'/>&nbsp;refresh</a>&nbsp;&nbsp;";
            echo "<a href='#' onClick='cetakHalaman()'><img src='../images/ico/print.png' title='cetak' border='0'/>&nbsp;cetak</a>&nbsp;&nbsp;";
            echo "<a href='#' onClick='showPembayaran(0)'>";
            echo "<img src='../images/ico/tambah.png' border='0'>&nbsp;terima pembayaran</a>&nbsp;";
            echo "</a>";
            echo "</div>";

            echo "</td>";
            echo "</tr>";
            echo "</table>";

            $sql = "SELECT p.replid AS id, j.nokas, date_format(p.tanggal, '%d-%b-%Y') as tanggal, p.keterangan, p.jumlah, p.petugas,
                           jd.koderek AS rekkas, ra.nama AS namakas, p.jam, IFNULL(p.sumberdana, 'Tidak ada data') AS fsumberdana
                      FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j, jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
                     WHERE j.replid = p.idjurnal
                       AND j.replid = jd.idjurnal
                       AND jd.koderek = ra.kode
                       AND j.idtahunbuku = '$idTahunBuku'
                       AND p.idpenerimaan = '$idPenerimaan'
                       AND p.idcalon = '$idCalon'
                       AND ra.kategori = 'HARTA'
                    ORDER BY p.replid DESC";
            $result = $db->QueryDb($sql);

            echo "<div style='width: 100%; overflow: auto; max-height: 400px;'>";

            echo "<table class='tab' id='tableRiwayat' border='1' style='border-collapse:collapse' align='center'>";
            echo "<tr height='30' align='center'>";
            echo "<td class='header' width='30'>No</td>";
            echo "<td class='header' width='120'>No. Jurnal/Tgl</td>";
            echo "<td class='header' width='150'>Jumlah</td>";
            echo "<td class='header' width='350'>Informasi</td>";
            echo "<td class='header' width='70'>&nbsp;</td>";
            echo "</tr>";

            $cnt = $nData + 1;
            while ($row = mysqli_fetch_array($result))
            {
                $cnt -= 1;

                echo "<tr height='25'>";
                echo "<td align='center' class='numberColumn'>$cnt</td>";
                echo "<td align='center'><strong>$row[nokas]</strong><br><i>$row[tanggal] $row[jam]</i></td>";
                echo "<td align='right' class='bg-light-blue'><b>" . FormatRupiah($row['jumlah']) . "</b></td>";
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

            echo "</table>";
            echo "</div>";

            echo "<br>";
        }
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kha1f");
    }
}
?>