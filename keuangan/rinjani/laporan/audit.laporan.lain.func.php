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
$nRowPerPage = 10;

function ShowLaporanAuditPenerimaanLain($db)
{
    global $nRowPerPage;
    global $idTahunBuku, $departemen, $lap, $tanggal1, $tanggal2, $page;

    $rowPerPage = $nRowPerPage * 2;
    $startIndex = ($page - 1) * $rowPerPage;

    echo "<table class='tab' id='table' border='1' width='100%' align='left' cellpadding='5' cellspacing='0'>";
    echo "<tr height='30' align='center'>";
    echo "<td class='header' width='3%'>No</td>";
    echo "<td class='header' width='17%'>Status Data</td>";
    echo "<td class='header' width='10%'>Tanggal</td>";
    echo "<td class='header' width='15%'>Jumlah</td>";
    echo "<td class='header' width='*'>Keterangan</td>";
    echo "<td class='header' width='15%'>Petugas</td>";
    echo "</tr>";

    $sql = "SELECT DISTINCT ai.petugas as petugasubah, j.transaksi, date_format(ai.tanggal, '%d-%b-%Y %H:%i:%s') as tanggalubah, 
	               ap.replid AS id, ap.idaudit, ap.statusdata, j.nokas, date_format(ap.tanggal, '%d-%b-%Y') AS tanggal, ap.petugas, 
	    		   ap.keterangan, ap.jumlah, ap.petugas, ai.alasan 
              FROM jbsfina.auditpenerimaanlain ap, jbsfina.auditinfo ai, jbsfina.jurnal j 
             WHERE j.replid = ap.idjurnal 
               AND j.idtahunbuku = '$idTahunBuku' 
               AND ap.idaudit = ai.replid 
               AND ai.departemen = '$departemen' 
			   AND ai.sumber = 'penerimaanlain' 
			   AND ai.tanggal BETWEEN '$tanggal1 00:00:00' AND '$tanggal2 23:59:59' 
			 ORDER BY ap.idaudit DESC, ai.tanggal DESC, ap.statusdata ASC
			 LIMIT $startIndex, $rowPerPage";
    $result = $db->QueryDb($sql);
    $cnt = -1;
    $no = ($page - 1) * $nRowPerPage;
    while ($row = mysqli_fetch_array($result))
    {
        $cnt += 1;

        $statusdata = "Data Lama";
        $bgcolor = "#FFFFFF";
        if ($row['statusdata'] == 1)
        {
            $statusdata = "Data Perubahan";
            $bgcolor = "#ffffe1";
        }

        if ($cnt % 2 == 0)
        {
            $no += 1;

            echo "<tr>";
            echo "<td rowspan='4' align='center' bgcolor='#ededed'>$no</td>";
            echo "<td colspan='5' align='left' style='background-color: #3994c6; color: #ffffff;'><em><strong>Perubahan dilakukan oleh $row[petugasubah] tanggal $row[tanggalubah] </strong></em></td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td colspan='5' style='background-color: #e5fdff;'><strong>No. Jurnal :</strong> $row[nokas] ";
            echo "&nbsp;&nbsp;<strong>Alasan : </strong>$row[alasan]";
            echo "<br /><strong>Transaksi :</strong> $row[transaksi]";
            echo "</td>";
            echo "</tr>";
        }

        echo "<tr bgcolor='$bgcolor' style='height: 40px;'>";
        echo "<td>$statusdata </td>";
        echo "<td align='center'>$row[tanggal]</td>";
        echo "<td align='right'><b>" . FormatRupiah($row['jumlah']) . "</b></td>";
        echo "<td>$row[keterangan]</td>";
        echo "<td align='center'>$row[petugas]</td>";
        echo "</tr>";

        if ($cnt % 2 == 1)
        {
            echo "<tr>";
            echo "<td colspan='6' style='height: 2px'></td>";
            echo "</tr>";
        }
    }
    echo "</table>";
}
?>