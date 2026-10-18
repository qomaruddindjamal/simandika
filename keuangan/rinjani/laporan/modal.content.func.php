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
function ShowPerubahanModal($db, $showMenu = true)
{
    global $idTahunBuku, $tanggal1, $tanggal2;

    $ls = explode("-", $tanggal2);
    $thn = $ls[0];
    $bln = $ls[1];

    $first_date = "$thn-$bln-1";
    $sql = "SELECT date_sub('$first_date', INTERVAL 1 DAY)";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);
    $last_date = $row[0];

    $sql = "SELECT SUM(jd.kredit - jd.debet) 
              FROM jbsfina.rekakun ra, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
		     WHERE jd.idjurnal = j.replid 
		       AND jd.koderek = ra.kode 
		       AND j.idtahunbuku = '$idTahunBuku' 
		       AND j.tanggal BETWEEN '$tanggal1' AND '$last_date' 
		       AND ra.kategori IN ('PENDAPATAN', 'MODAL')";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);
    $totalpendapatan = (float)$row[0];

    $sql = "SELECT SUM(jd.debet - jd.kredit) 
              FROM jbsfina.rekakun ra, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
	         WHERE jd.idjurnal = j.replid 
	           AND jd.koderek = ra.kode 
	           AND j.idtahunbuku = '$idTahunBuku' 
	           AND j.tanggal BETWEEN '$tanggal1' AND '$last_date' 
	           AND ra.kategori = 'BIAYA'";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);
    $totalbiaya = (float)$row[0];

    $modalawal = $totalpendapatan - $totalbiaya;

    $sql = "SELECT SUM(jd.kredit - jd.debet) 
              FROM jbsfina.rekakun ra, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
	         WHERE jd.idjurnal = j.replid 
	           AND jd.koderek = ra.kode 
	           AND j.idtahunbuku = '$idTahunBuku' 
	           AND j.tanggal BETWEEN '$first_date' AND '$tanggal2' 
	           AND ra.kategori = 'MODAL' 
	           AND jd.kredit > 0";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);
    $jinvestasi = (float)$row[0];

    $sql = "SELECT SUM(jd.debet - jd.kredit) 
              FROM jbsfina.rekakun ra, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
	         WHERE jd.idjurnal = j.replid 
	           AND jd.koderek = ra.kode 
	           AND j.idtahunbuku = '$idTahunBuku' 
	           AND j.tanggal BETWEEN '$first_date' AND '$tanggal2' 
	           AND ra.kategori = 'MODAL' 
	           AND jd.debet > 0";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);
    $jpengambilan = (float)$row[0];

    $sql = "SELECT SUM(jd.kredit - jd.debet) 
              FROM jbsfina.rekakun ra, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
             WHERE jd.idjurnal = j.replid 
               AND jd.koderek = ra.kode 
               AND j.idtahunbuku = '$idTahunBuku' 
               AND j.tanggal BETWEEN '$first_date' AND '$tanggal2' 
               AND ra.kategori = 'PENDAPATAN'";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);
    $jpendapatan = (float)$row[0];

    $sql = "SELECT SUM(jd.debet - jd.kredit) 
              FROM jbsfina.rekakun ra, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
             WHERE jd.idjurnal = j.replid 
               AND jd.koderek = ra.kode 
               AND j.idtahunbuku = '$idTahunBuku' 
               AND j.tanggal BETWEEN '$first_date' AND '$tanggal2' 
               AND ra.kategori = 'BIAYA'";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);
    $jbiaya = (float)$row[0];

    $jincome = $jpendapatan - $jbiaya;

    $modalakhir = (float)$modalawal + (float)$jinvestasi - (float)$jpengambilan + (float)$jincome;

    if ($showMenu)
    {
        echo "<table border='0' width='70%' align='center' cellpadding='5' cellspacing='5'>";
        echo "<tr>";
        echo "<td>";
        echo "<font size='4'><strong>Laporan Perubahan Modal</strong></font><br />";
        echo "<font size='2'>Per Tanggal ". LongDateFormat($tanggal2) . "</font>";
        echo "</td>";
        echo "<td align='right' valign='top'>";
        echo "<a href='#' onClick='document.location.reload()'><img src='../images/ico/refresh.png' border='0' >&nbsp;refresh</a>&nbsp;&nbsp;";
        echo "<a href='JavaScript:cetak()'><img src='../images/ico/print.png' border='0'>&nbsp;cetak</a>&nbsp;&nbsp;";
        echo "<a href='JavaScript:excel()'><img src='../images/ico/excel.png' border='0'>&nbsp;excel</a>&nbsp;";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    }

    echo "<div id='dvLaporan'>";
    echo "<table id='table'  border='0' style='border-width: 0; border-collapse: collapse;' cellpadding='8' cellspacing='5' align='center' width='70%'>";
    echo "<tr style='height: 60px'>";
    echo "<td width='*'>Modal di awal " . NamaBulan($bln) . " $thn</td>";
    echo "<td align='right' width='200'><b>" . FormatRupiah($modalawal) . "</b></td>";
    echo "<td width='5'>&nbsp;</td>";
    echo "</tr>";
    echo "<tr style='height: 60px'>";
    echo "<td>Investasi pada " . NamaBulan($bln) . " $thn</td>";
    echo "<td align='right'><b>" . FormatRupiah($jinvestasi) . "</b></td>";
    echo "<td>&nbsp;</td>";
    echo "</tr>";
    echo "<tr style='height: 60px'>";
    echo "<td>Pengambilan pada " . NamaBulan($bln) . " $thn</td>";
    echo "<td align='right'><b>" . FormatRupiah(-1 * $jpengambilan) . "</b></td>";
    echo "<td>&nbsp;</td>";
    echo "</tr>";
    echo "<tr style='height: 60px'>";
    echo "<td>";
    if ($jpendapatan < $jbiaya)
        echo  'Rugi ';
    else
        echo  'Laba ';;
    echo "pada " . NamaBulan($bln) . " $thn</td>";
    echo "<td align='right'><b>" . FormatRupiah($jincome) . "</b></td>";
    echo "<td>&nbsp;</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan='2'>";
    echo "<hr width='100%' style='color:#000000; border-style:dashed; line-height:1px;' />";
    echo "</td>";
    echo "<td><font size='3'><strong>+</strong></font></td>";
    echo "</tr>";
    echo "<tr style='height: 60px'>";
    echo "<td>&nbsp;&nbsp;<font size='2'><strong>Modal per ". LongDateFormat($tanggal2) ."</strong></font></td>";
    echo "<td align='right'><font size='2'><strong>" . FormatRupiah($modalakhir) . "</strong></font></td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";
}
?>
