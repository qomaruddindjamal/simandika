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
function ShowNeracaPercobaan($db, $showMenu = true)
{
    global $idTahunBuku, $tanggal1, $tanggal2;

    $sql = "SELECT ra.nama, ra.kode, k.kategori, SUM(jd.debet) AS debet, SUM(jd.kredit) AS kredit 
	          FROM jbsfina.rekakun ra, jbsfina.katerekakun k, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
			 WHERE jd.idjurnal = j.replid 
			   AND jd.koderek = ra.kode 
			   AND ra.kategori = k.kategori 
			   AND j.idtahunbuku = '$idTahunBuku' 
			   AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
		     GROUP BY ra.nama, ra.kode, k.kategori 
		     ORDER BY k.urutan, ra.kode;";
    $res = $db->QueryDb($sql);
    if (mysqli_num_rows($res) == 0)
    {
        echo "<span style='color: maroon'>belum tersedia data untuk laporan neraca percobaan</span>";
        return;
    }

    if ($showMenu)
    {
        echo "<div id='dvMenu'>";
        echo "<table border='0' width='95%' align='center'>";
        echo "<tr>";
        echo "<td align='right'>";
        echo "<a href='#' onClick='document.location.reload()'><img src='../images/ico/refresh.png' border='0'>&nbsp;refresh</a>&nbsp;&nbsp;";
        echo "<a href='JavaScript:cetak()'><img src='../images/ico/print.png' border='0'>&nbsp;cetak</a>&nbsp;&nbsp;";
        echo "<a href='JavaScript:excel()'><img src='../images/ico/excel.png' border='0'>&nbsp;excel</a>&nbsp;";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "</div>";
        echo "<br />";
    }

    echo "<div id='dvLaporan'>";
    echo "<table class='tab' style='border-collapse:collapse' id='table' border='1' cellpadding='2' width='100%'>";
    echo "<tr>";
    echo "<td class='header' width='5%' align='center'>No</td>";
    echo "<td class='header' width='8%' align='center'>Kode</td>";
    echo "<td class='header' width='*' align='center'>Rekening</td>";
    echo "<td class='header' width='20%' align='center'>Debet</td>";
    echo "<td class='header' width='20%' align='center'>Kredit</td>";
    echo "</tr>";

    $cnt = 0;
    $totaldebet = 0;
    $totalkredit = 0;
    while($row = mysqli_fetch_array($res))
    {
        $cnt += 1;

        $kategori = $row['kategori'];
        switch($kategori)
        {
            case 'HARTA':
            case 'PIUTANG':
            case 'INVENTARIS':
            case 'BIAYA':
                $debet1 = $row['debet'] - $row['kredit'];
                $debet = FormatRupiah($debet1);
                $kredit = "&nbsp;";
                $totaldebet += $debet1;
                break;
            default:
                $kredit1 = $row['kredit'] - $row['debet'];
                $kredit = FormatRupiah($kredit1);
                $debet = "&nbsp";
                $totalkredit += $kredit1;
        }

        echo "<tr height='25'>";
        echo "<td align='center' class='numberColumn'>$cnt </td>";
        echo "<td align='center'>$row[kode] </td>";
        echo "<td align='left'>$row[nama] </td>";
        echo "<td align='right'><b>$debet</b></td>";
        echo "<td align='right'><b>$kredit</b></td>";
        echo "</tr>";
    }
    echo "<tr style='height: 50px'>";
    echo "<td colspan='3' align='center' bgcolor='#ededed'><span style='font-size: 14px; font-weight: bold'>T O T A L</span></td>";
    echo "<td align='right' bgcolor='#ededed'><span style='font-size: 14px; font-weight: bold'>" . FormatRupiah($totaldebet) . "</span></td>";
    echo "<td align='right' bgcolor='#ededed'><span style='font-size: 14px; font-weight: bold'>" . FormatRupiah($totalkredit) . "</span></td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";


}
?>