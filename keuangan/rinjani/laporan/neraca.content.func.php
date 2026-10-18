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
function ShowNeraca($db, $showMenu = true)
{
    global $idTahunBuku, $tanggal1, $tanggal2;

    $sqlAktivaLancar = "SELECT jd.koderek, ra.nama, sum(jd.debet - jd.kredit) 
                          FROM jbsfina.jurnal j, jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
                         WHERE j.replid = jd.idjurnal 
                           AND jd.koderek = ra.kode 
                           AND j.idtahunbuku = '$idTahunBuku' 
                           AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
                           AND ra.kategori IN ('HARTA', 'PIUTANG') 
                         GROUP BY jd.koderek, ra.nama 
                         ORDER BY jd.koderek";

    $sqlAktivaTetap =  "SELECT jd.koderek, ra.nama, sum(jd.debet - jd.kredit) 
                          FROM jbsfina.jurnal j, jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
                         WHERE j.replid = jd.idjurnal 
                           AND jd.koderek = ra.kode 
                           AND j.idtahunbuku = '$idTahunBuku' 
                           AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
                           AND ra.kategori = 'INVENTARIS' 
                         GROUP BY jd.koderek, ra.nama 
                         ORDER BY jd.koderek";

    $sqlUtang = "SELECT jd.koderek, ra.nama, sum(jd.kredit - jd.debet) 
                   FROM jbsfina.jurnal j, jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
                  WHERE j.replid = jd.idjurnal 
                    AND jd.koderek = ra.kode 
                    AND j.idtahunbuku = '$idTahunBuku' 
                    AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
                    AND ra.kategori = 'UTANG' 
                  GROUP BY jd.koderek, ra.nama 
                  ORDER BY jd.koderek";

    $sqlPendapatan = "SELECT SUM(jd.kredit - jd.debet) 
                        FROM jbsfina.rekakun ra, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
                       WHERE jd.idjurnal = j.replid 
                         AND jd.koderek = ra.kode 
                         AND j.idtahunbuku = '$idTahunBuku' 
                         AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
                         AND ra.kategori IN ('PENDAPATAN', 'MODAL')";

    $sqlBiaya = "SELECT SUM(jd.debet - jd.kredit) 
                   FROM jbsfina.rekakun ra, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
                  WHERE jd.idjurnal = j.replid 
                    AND jd.koderek = ra.kode
                    AND j.idtahunbuku = '$idTahunBuku' 
                    AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
                    AND ra.kategori = 'BIAYA'";

    if ($showMenu)
    {
        echo "<table border='0' width='100%' align='center' cellpadding='10' cellspacing='5' >";
        echo "<tr>";
        echo "<td>";
        echo "<span style='font-size: 18px;'><strong>Laporan Neraca</strong></span><br />";
        echo "Per Tanggal ". LongDateFormat($tanggal2);
        echo "</td>";
        echo "<td align='right' valign='top'>";
        echo "<a href='#' onClick='document.location.reload()'><img src='../images/ico/refresh.png' border='0'>&nbsp;refresh</a>&nbsp;&nbsp;";
        echo "<a href='JavaScript:cetak()'><img src='../images/ico/print.png' border='0'>&nbsp;cetak</a>&nbsp;&nbsp;";
        echo "<a href='JavaScript:excel()'><img src='../images/ico/excel.png' border='0'>&nbsp;excel</a>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    }

    echo "<div id='dvLaporan'>";
    echo "<table border='0' width='100%' cellpadding='10' cellspacing='5' align='center'>";
    echo "<tr>";
    echo "<td width='50%' valign='top'>";

        echo "<span style='font-size: 14px;'><strong>HARTA</strong></span><br />";

        echo "<table border='0' id='table1' style='border-collapse:collapse' cellpadding='2' width='100%' align='center'>";
        echo "<tr height='28'>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td colspan='6'><strong>AKTIVA LANCAR</strong><br /></td>";
        echo "</tr>";

        $totalaktivalancar = 0;
        $res = $db->QueryDb($sqlAktivaLancar);
        while ($row = mysqli_fetch_row($res))
        {
            $totalaktivalancar += (float)$row[2];

            echo "<tr height='23'>";
            echo "<td width='2%'>&nbsp;</td>";
            echo "<td width='2%'>&nbsp;</td>";
            echo "<td width='5%' align='left'>$row[0] </td>";
            echo "<td width='*' align='left'>$row[1] </td>";
            echo "<td width='28%' align='right'>" . FormatRupiah($row[2]) . "</td>";
            echo "<td width='30%'  align='right'>&nbsp;</td>";
            echo "<td width='13'>&nbsp;</td>";
            echo "</tr>";
        }
        echo "<tr height='23'>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td colspan='3' align='left'><strong><em>Sub Total Aktiva Lancar:</em></strong><br></td>";
        echo "<td align='right'><strong>" . FormatRupiah($totalaktivalancar) . "</strong></td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";
        echo "</table>";
        echo "<br>";

        echo "<table border='0' id='table2' style='border-collapse:collapse' cellpadding='2' width='100%' align='center'>";
        echo "<tr height='28'>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td colspan='6'><strong>AKTIVA TETAP</strong><br /></td>";
        echo "</tr>";

        $res = $db->QueryDb($sqlAktivaTetap);
        $totalaktivatetap = 0;
        while ($row = mysqli_fetch_row($res))
        {
            $totalaktivatetap += (float)$row[2];

            echo "<tr height='23'>";
            echo "<td width='2%'>&nbsp;</td>";
            echo "<td width='2%'>&nbsp;</td>";
            echo "<td width='5%' align='left'>$row[0] </td>";
            echo "<td width='*' align='left'>$row[1] </td>";
            echo "<td width='28%' align='right'>" . FormatRupiah($row[2]) . "</td>";
            echo "<td width='30%'  align='right'>&nbsp;</td>";
            echo "<td width='13'>&nbsp;</td>";
            echo "</tr>";
        }

        echo "<tr height='23'>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td colspan='3' align='left'><strong><em>Sub Total Aktiva Tetap:</em></strong><br></td>";
        echo "<td align='right'><strong>" . FormatRupiah($totalaktivatetap) . "</strong></td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td colspan='6'><hr width='100%' style='border-style:dashed' /></td>";
        echo "<td align='right'>+</td>";
        echo "</tr>";
        echo "<tr height='28'>";
        echo "<td colspan='5' align='left'><font size='2'><strong>TOTAL HARTA</strong></font><br /></td>";
        echo "<td align='right'><font size='2'><strong>" . FormatRupiah($totalaktivatetap + $totalaktivalancar) . "</strong></font></td>";
        echo "<td >&nbsp;</td>";
        echo "</tr>";
        echo "</table>";

    echo "</td>";
    echo "<td width='50%' valign='top'>";

        echo "<span style='font-size: 14px;'><strong>KEWAJIBAN</strong></span><br />";

        echo "<table border='0' id='table3' style='border-collapse:collapse' cellpadding='2' width='100%' align='center'>";
        echo "<tr height='28'>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td colspan='6'><strong>HUTANG</strong><br /></td>";
        echo "</tr>";

        $res = $db->QueryDb($sqlUtang);
        $totalhutang = 0;
        while ($row = mysqli_fetch_row($res))
        {
            $totalhutang += (float)$row[2];

            echo "<tr height='23'>";
            echo "<td width='2%'>&nbsp;</td>";
            echo "<td width='2%'>&nbsp;</td>";
            echo "<td width='5%' align='left'>$row[0] </td>";
            echo "<td width='*' align='left'>$row[1] </td>";
            echo "<td width='28%' align='right'>" . FormatRupiah($row[2]) . "</td>";
            echo "<td width='30%'  align='right'>&nbsp;</td>";
            echo "<td width='13'>&nbsp;</td>";
            echo "</tr>";
        }
        echo "<tr height='23'>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td colspan='3' align='left'><strong><em>Sub Total Hutang:</em></strong><br></td>";
        echo "<td align='right'><strong>" . FormatRupiah($totalhutang) . "</strong></td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";
        echo "</table>";
        echo "<br>";

        echo "<table border='0' id='table4' style='border-collapse:collapse' cellpadding='2' width='100%' align='center'>";
        echo "<tr height='28'>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td colspan='6'><strong>MODAL</strong><br /></td>";
        echo "</tr>";

        $res = $db->QueryDb($sqlPendapatan);
        $row = mysqli_fetch_row($res);
        $totalpendapatan = (float)$row[0];

        $res = $db->QueryDb($sqlBiaya);
        $row = mysqli_fetch_row($res);
        $totalbiaya = (float)$row[0];

        $modalusaha = $totalpendapatan - $totalbiaya;

        echo "<tr height='23'>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td width='5%' align='left'>&nbsp;</td>";
        echo "<td width='*' align='left'>Modal Usaha + Laba Ditahan</td>";
        echo "<td width='28%' align='right'>" . FormatRupiah($modalusaha) . "</td>";
        echo "<td width='30%'  align='right'>&nbsp;</td>";
        echo "<td width='13'>&nbsp;</td>";
        echo "</tr>";
        echo "<tr height='23'>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td width='2%'>&nbsp;</td>";
        echo "<td colspan='3' align='left'><strong><em>Sub Total Modal Usaha:</em></strong><br /></td>";
        echo "<td align='right'><strong>" . FormatRupiah($modalusaha) ."</strong></td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td colspan='6'><hr width='100%' style='border-style:dashed' /></td>";
        echo "<td align='right'>+</td>";
        echo "</tr>";
        echo "<tr height='28'>";
        echo "<td colspan='5' align='left'><font size='2'><strong>TOTAL KEWAJIBAN DAN MODAL</strong></font><br /></td>";
        echo "<td align='right'><font size='2'><strong>". FormatRupiah($modalusaha + $totalhutang) ."</strong></font></td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";
        echo "</table>";

    echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";

}
?>