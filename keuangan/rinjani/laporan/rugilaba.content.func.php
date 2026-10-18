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
function ShowRugiLaba($db, $showMenu = true)
{
    global $idTahunBuku, $tanggal1, $tanggal2;

    $sql1 = "SELECT nama, kode, SUM(debet) AS debet, SUM(kredit) AS kredit 
               FROM ((SELECT DISTINCT j.replid, ra.nama, ra.kode, jd.debet, jd.kredit 
			           FROM jbsfina.rekakun ra, jbsfina.katerekakun k, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
			          WHERE jd.idjurnal = j.replid 
			            AND jd.koderek = ra.kode 
			            AND j.idtahunbuku = '$idTahunBuku' 
			            AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
			            AND ra.kategori = 'PENDAPATAN' 
			          GROUP BY j.replid, ra.nama, ra.kode 
			          ORDER BY ra.kode) AS X) 
		      GROUP BY nama, kode";
    $res1 = $db->QueryDb($sql1);

    $sql2 = "SELECT nama, kode, SUM(debet) AS debet, SUM(kredit) AS kredit 
               FROM ((SELECT DISTINCT j.replid, ra.nama, ra.kode, jd.debet, jd.kredit 
			            FROM jbsfina.rekakun ra, jbsfina.katerekakun k, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
				       WHERE jd.idjurnal = j.replid 
				         AND jd.koderek = ra.kode 
				         AND j.idtahunbuku = '$idTahunBuku' 
				         AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
				         AND ra.kategori = 'BIAYA' 
				       GROUP BY j.replid, ra.nama, ra.kode 
				       ORDER BY ra.kode) AS X) 
	          GROUP BY nama, kode";
    $res2 = $db->QueryDb($sql2);

    if (mysqli_num_rows($res1) == 0 && mysqli_num_rows($res2) == 0)
    {
        echo "<span style='color: maroon'>belum tersedia data untuk laporan rugi laba</span>";
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
    echo "<table border='0' id='table' cellpadding='5' cellspacing='5' width='80%' align='center'>";
    echo "<tr>";
    echo "<td colspan='6'><span style='font-size: 15px; font-weight: bold;'>PENDAPATAN</span></td>";
    echo "</tr>";

    $cnt = 0;
    $totalpendapatan = 0;
    while($row = mysqli_fetch_array($res1))
    {
        $debet = $row['kredit'] - $row['debet'];
        $debet = FormatRupiah($debet);
        $kredit = "&nbsp;";

        $totalpendapatan += ($row['kredit'] - $row['debet']);

        echo "<tr height='25'>";
        echo "<td width='2%' align='right'>&nbsp;</td>";
        echo "<td width='5%' align='left' valign='top'>$row[kode] </td>";
        echo "<td align='left' width='*' valign='top'>$row[nama] </td>";
        echo "<td align='right' width='18%' valign='top'>$debet</td>";
        echo "<td align='right' width='18%' valign='top'>$kredit</td>";
        echo "<td width='20%'>&nbsp;</td>";
        echo "</tr>";
    }
    echo "<tr style='height: 30px'>";
    echo "<td>&nbsp;</td>";
    echo "<td colspan='4'><strong>SUB TOTAL PENDAPATAN</strong></td>";
    echo "<td align='right'><strong>" . FormatRupiah($totalpendapatan) . "</strong></td>";
    echo "</tr>";
    echo "<tr style='height: 5px;'>";
    echo "<td colspan='6'>&nbsp;</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan='6'><span style='font-size: 15px; font-weight: bold;'>BIAYA</span></td>";
    echo "</tr>";

    $cnt = 0;
    $totalbiaya = 0;
    while($row = mysqli_fetch_array($res2))
    {
        $kredit = $row['debet'] - $row['kredit'];
        $kredit = FormatRupiah($kredit);
        $debet = "&nbsp;";

        $totalbiaya += ($row['debet'] - $row['kredit']);

        echo "<tr height='25'>";
        echo "<td width='2%' align='right'>&nbsp;</td>";
        echo "<td width='5%' align='left' valign='top'>$row[kode]</td>";
        echo "<td align='left' width='*' valign='top'>$row[nama]</td>";
        echo "<td align='right' width='18%' valign='top'>$debet</td>";
        echo "<td align='right' width='18%' valign='top'>$kredit</td>";
        echo "<td width='20%'>&nbsp;</td>";
        echo "</tr>";
    }
    echo "<tr style='height: 30px'>";
    echo "<td>&nbsp;</td>";
    echo "<td colspan='4'><strong>SUB TOTAL BIAYA</strong></td>";
    echo "<td align='right'><strong>" . FormatRupiah($totalbiaya) . "</strong></td>";
    echo "</tr>";
    echo "<tr style='height: 5px'>";
    echo "<td colspan='6'>&nbsp;</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td colspan='4'>";
    echo "<span style='font-size: 14px; font-weight: bold;'>";
    if ($totalpendapatan < $totalbiaya)
        echo  'RUGI';
    else
        echo  'LABA';
    echo "</span>";
    echo "</td>";
    echo "<td colspan='2' align='right'>";
    echo "<span style='font-size: 14px; font-weight: bold;'>";
    echo FormatRupiah($totalpendapatan - $totalbiaya);
    echo "</span>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";

    echo "</div>";
}
?>