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
function ShowLaporanPembayaranCalonSiswa($db)
{
    global $idcalon, $idtahunbuku, $tanggal1, $tanggal2;

    $sql = "SELECT COUNT(*) 
              FROM jbsfina.besarjttcalon b, jbsfina.penerimaanjttcalon p 
             WHERE p.idbesarjttcalon = b.replid 
               AND b.idcalon ='$idcalon' 
               AND b.info2 = '$idtahunbuku'
               AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);
    $nwajib = $row[0];

    $sql = "SELECT COUNT(*) 
              FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j
             WHERE p.idjurnal = j.replid 
               AND j.idtahunbuku = '$idtahunbuku' 
               AND p.idcalon = '$idcalon' 
               AND p.tanggal 
           BETWEEN '$tanggal1' AND '$tanggal2'";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);
    $niuran = $row[0];

    if ($nwajib + $niuran == 0)
    {
        $db->Close();

        echo "<span style='color: maroon; font-size: 13px;'>";
        echo "Belum ada pembayaran yang dilakukan calon siswa";
        echo "</span>";

        exit();
    }

    $sql = "SELECT DISTINCT b.replid AS id, b.besar, b.lunas, b.keterangan, d.nama, b.idpenerimaan 
              FROM jbsfina.besarjttcalon b, jbsfina.penerimaanjttcalon p, jbsfina.datapenerimaan d 
             WHERE p.idbesarjttcalon = b.replid 
               AND b.idpenerimaan = d.replid 
               AND b.idcalon = '$idcalon' 
               AND b.info2 = '$idtahunbuku'
               AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
             ORDER BY nama";

    $totalbesarwjb = 0;
    $totalbayarwjb = 0;
    $totaldiskonwjb = 0;
    $totalsisawjb = 0;

    $result = $db->QueryDb($sql);
    $nJtt = mysqli_num_rows($result);

    if ($nJtt > 0)
    {
        echo "<table class='tab' id='tablejtt' border='1' cellpadding='5' style='border-collapse:collapse' cellspacing='0'>";
        echo "<tr height='30' align='center' class='header'>";
        echo "<td width='30'>No</td>";
        echo "<td width='200'>Iuran Wajib</td>";
        echo "<td width='150' align='center'>BP<br><span style='font-size: 9px; font-weight: normal'>Besar Pembayaran</span></td>";
        echo "<td width='150' align='center'>TP<br><span style='font-size: 9px; font-weight: normal'>Total Pembayaran</span></td>";
        echo "<td width='150' align='center'>TD<br><span style='font-size: 9px; font-weight: normal'>Total Diskon</span></td>";
        echo "<td width='150' align='center'>Sisa<br><span style='font-size: 9px; font-weight: normal'>Tunggakan</span></td>";
        echo "<td width='250' align='center'>PT<br><span style='font-size: 9px; font-weight: normal'>Pembayaran Terakhir</span></td>";
        echo "</tr>";
    }

    $cnt = 0;
    while ($row = mysqli_fetch_array($result))
    {
        $cnt += 1;

        $idbesarjtt = $row['id'];
        $namapenerimaan = $row['nama'];
        $idpenerimaan = $row['idpenerimaan'];
        $besar = $row['besar'];
        $lunas = $row['lunas'];
        $keterangan = $row['keterangan'];

        $sql = "SELECT SUM(jumlah), SUM(info1) 
                  FROM jbsfina.penerimaanjttcalon 
                 WHERE idbesarjttcalon = '$idbesarjtt'";
        $row2 = $db->FetchSingleRow($sql);
        $pembayaran = $row2[0] + $row2[1];
        $diskon = $row2[1];
        $sisa = $besar - $pembayaran;

        $totalbesarwjb += $besar;
        $totalbayarwjb += $pembayaran;
        $totaldiskonwjb += $diskon;
        $totalsisawjb += $sisa;

        $sql = "SELECT p.jumlah, DATE_FORMAT(p.tanggal, '%d-%b-%Y') AS ftanggal, p.info1, j.nokas
                  FROM jbsfina.penerimaanjttcalon p, jbsfina.jurnal j
                 WHERE p.idjurnal = j.replid
                   AND p.idbesarjttcalon = '$idbesarjtt'
                 ORDER BY p.tanggal DESC
                 LIMIT 1";

        $result2 = $db->QueryDb($sql);
        $byrakhir = 0;
        $dknakhir = 0;
        $tglakhir = "";
        $nojurnal = "";

        if (mysqli_num_rows($result2))
        {
            $row2 = mysqli_fetch_row($result2);
            $byrakhir = $row2[0];
            $tglakhir = $row2[1];
            $dknakhir = $row2[2];
            $nojurnal = $row2[3];
        };

        echo "<tr>";
        echo "<td align='center' class='numberColumn'>$cnt</td>";
        echo "<td align='left'><b>$namapenerimaan</b><br><a class='asmall hide-in-report' onclick='showRiwayatJtt($idpenerimaan, \"$namapenerimaan\")'>riwayat</a></td>";
        echo "<td align='right'><b>" . FormatRupiah($besar) . "</b></td>";
        echo "<td align='right'><b>" . FormatRupiah($pembayaran) . "</b></td>";
        echo "<td align='right'><b>" . FormatRupiah($diskon) . "</b></td>";
        echo "<td align='right'><b>" . FormatRupiah($sisa) . "</b></td>";
        echo "<td align='left'>";
        echo "<b>" . FormatRupiah($byrakhir) . "</b><br><i>diskon: " . FormatRupiah($dknakhir) . "</i><br><i>tanggal: " . $tglakhir . "</i><br><i>no jurnal: $nojurnal</i>";
        echo "</td>";
        echo "</tr>";
    }

    if ($nJtt > 0)
    {
        echo "<tr height='45'>";
        echo "<td align='right' colspan='2' class='footerColumn'><b>TOTAL</b></td>";
        echo "<td align='right' class='footerColumn'><b>" . FormatRupiah($totalbesarwjb) . "</b></td>";
        echo "<td align='right' class='footerColumn'><b>" . FormatRupiah($totalbayarwjb) . "</b></td>";
        echo "<td align='right' class='footerColumn'><b>" . FormatRupiah($totaldiskonwjb) . "</b></td>";
        echo "<td align='right' class='footerColumn'><b>" . FormatRupiah($totalsisawjb) . "</b></td>";
        echo "<td align='left' class='footerColumn'>&nbsp;</td>";
        echo "</tr>";
        echo "</table>";
    }

    $totalbayarskr = 0;

    $sql = "SELECT DISTINCT p.idpenerimaan, d.nama 
              FROM jbsfina.penerimaaniurancalon p, jbsfina.datapenerimaan d, jbsfina.jurnal j
             WHERE p.idpenerimaan = d.replid 
               AND p.idjurnal = j.replid 
               AND j.idtahunbuku = '$idtahunbuku'
               AND p.idcalon = '$idcalon' 
               AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
             ORDER BY nama";
    $result = $db->QueryDb($sql);
    $nSkr = mysqli_num_rows($result);

    if ($nSkr > 0)
    {
        echo "<br><br>";
        echo "<table class='tab' id='tableskr' border='1' cellpadding='5' style='border-collapse:collapse' cellspacing='0'>";
        echo "<tr height='30' align='center' class='header'>";
        echo "<td width='30'>No</td>";
        echo "<td width='200'>Iuran Sukarela</td>";
        echo "<td width='150' align='center'>TP<br><span style='font-size: 9px; font-weight: normal'>Total Pembayaran</span></td>";
        echo "<td width='250' align='center'>PT<br><span style='font-size: 9px; font-weight: normal'>Pembayaran Terakhir</span></td>";
        echo "</tr>";
    }

    $cnt = 0;
    while ($row = mysqli_fetch_array($result))
    {
        $cnt += 1;
        $idpenerimaan = $row['idpenerimaan'];
        $namapenerimaan = $row['nama'];

        $sql = "SELECT SUM(p.jumlah) 
                  FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j 
                 WHERE p.idjurnal = j.replid
                   AND j.idtahunbuku = $idtahunbuku
                   AND p.idpenerimaan = '$idpenerimaan' 
                   AND p.idcalon = $idcalon";
        $pembayaran = $db->FetchSingle($sql, 0);
        $totalbayarskr += $pembayaran;

        $sql = "SELECT p.jumlah, DATE_FORMAT(p.tanggal, '%d-%b-%Y') AS ftanggal
                  FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j 
                 WHERE p.idjurnal = j.replid
                   AND j.idtahunbuku = $idtahunbuku
                   AND p.idpenerimaan='$idpenerimaan'
                   AND p.idcalon = $idcalon
                 ORDER BY p.replid DESC
                 LIMIT 1";
        $result2 = $db->QueryDb($sql);
        $byrakhir = 0;
        $tglakhir = "";
        if (mysqli_num_rows($result2))
        {
            $row2 = mysqli_fetch_row($result2);
            $byrakhir = $row2[0];
            $tglakhir = $row2[1];
        };

        echo "<tr>";
        echo "<td align='center' class='numberColumn'>$cnt</td>";
        echo "<td align='left'><b>$namapenerimaan</b><br><a class='asmall hide-in-report' onclick='showRiwayatSkr($idpenerimaan, \"$namapenerimaan\")'>riwayat</a></td>";
        echo "<td align='right'><b>" . FormatRupiah($pembayaran) . "</b></td>";
        echo "<td align='left'>";
        echo "<b>" . FormatRupiah($byrakhir) . "</b><br><i>tanggal: " . $tglakhir . "</i>";
        echo "</td>";
        echo "</tr>";
    }

    if ($nSkr > 0)
    {
        echo "<tr height='45'>";
        echo "<td align='right' colspan='2' class='footerColumn'><b>TOTAL</b></td>";
        echo "<td align='right' class='footerColumn'><b>" . FormatRupiah($totalbayarskr) . "</b></td>";
        echo "<td align='left' class='footerColumn'>&nbsp;</td>";
        echo "</tr>";
        echo "</table>";
    }
}
?>
