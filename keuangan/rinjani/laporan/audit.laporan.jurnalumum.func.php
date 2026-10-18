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

function ShowLaporanAuditJurnalUmum($db)
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
    echo "<td class='header' width='15%'>Keterangan</td>";
    echo "<td class='header' width='*'>Rincian</td>";
    echo "<td class='header' width='15%'>Petugas</td>";
    echo "</tr>";

    $sql = "SELECT DISTINCT ai.petugas AS petugasubah, j.transaksi, date_format(ai.tanggal, '%d-%b-%Y %H:%i:%s') as tanggalubah, 
	               aj.replid AS id, aj.idaudit, aj.status, aj.nokas, date_format(aj.tanggal, '%d-%b-%Y') AS tanggal,  
				   aj.petugas, aj.keterangan, aj.petugas, ai.alasan 
			  FROM jbsfina.auditjurnal aj, jbsfina.auditinfo ai, jbsfina.jurnal j 
			 WHERE aj.idaudit = ai.replid 
			   AND ai.idsumber = j.replid 
			   AND j.idtahunbuku = '$idTahunBuku' 
			   AND ai.departemen = '$departemen' 
			   AND ai.sumber='jurnalumum' 
			   AND ai.tanggal BETWEEN '$tanggal1 00:00:00' AND '$tanggal2 23:59:59' 
			 ORDER BY aj.idaudit DESC, ai.tanggal DESC, aj.status ASC
			 LIMIT $startIndex, $rowPerPage";
    $result = $db->QueryDb($sql);
    $cnt = -1;
    $no = ($page - 1) * $nRowPerPage;
    while ($row = mysqli_fetch_array($result))
    {
        $cnt += 1;

        $idaudit = $row['idaudit'];
        $status = $row['status'];

        $statusdata = "Data Lama";
        $bgcolor = "#FFFFFF";
        if ($row['status'] == 1)
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

        echo "<tr bgcolor='$bgcolor'>";
        echo "<td>$statusdata </td>";
        echo "<td align='center'>$row[tanggal]</td>";
        echo "<td align='left'>$row[keterangan]</td>";
        echo "<td>";

        echo "<table cellpadding='5' cellspacing='0' border='1' class='tab' style='border-collapse:collapse' width='100%' bgcolor='#FFFFFF'>";
        $nokas = $row['nokas'];
        $sql = "SELECT ajd.koderek, ra.nama, ajd.debet, ajd.kredit 
                  FROM jbsfina.auditjurnaldetail ajd, jbsfina.jurnal j, jbsfina.rekakun ra 
                 WHERE ajd.idjurnal = j.replid 
                   AND ajd.koderek = ra.kode 
                   AND j.nokas = '$nokas' 
                   AND ajd.status = '$status' 
                   AND idaudit = '$idaudit' 
                 ORDER BY ajd.replid";
        $res2 = $db->QueryDb($sql);
        while ($row2 = mysqli_fetch_row($res2))
        {
            echo "<tr>";
            echo "<td width='*'>$row2[0] $row2[1]</td>";
            echo "<td width='30%' align='right'>" . FormatRupiah($row2[2]) . "</td>";
            echo "<td width='30%' align='right'>" . FormatRupiah($row2[3]) . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "</td>";
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