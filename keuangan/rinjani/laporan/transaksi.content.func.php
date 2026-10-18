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

function ColumnColor($urutBy)
{
    global $urut;
    return ($urut == $urutBy) ? "#fffc00" : "#25f2ff";
}

function ShowTransactionList($db, $pageLimit = true)
{
    global $departemen, $tanggal1, $tanggal2, $idTahunBuku;
    global $page, $nRowPerPage, $nData, $urut;

    $startIndex = ($page - 1) * $nRowPerPage;

    echo "<table class='tab' id='table' border='1' cellpadding='5' style='border-collapse:collapse' cellspacing='0' width='100%' align='left' bordercolor='#000000'>";
    echo "<tr height='30' align='center'>";
    echo "<td width='4%' class='header' >No</td>";
    $fgcolor = ColumnColor("nokas");
    echo "<td width='18%' class='header' style='cursor:pointer; color: $fgcolor;' onClick='onChangeUrut(\"nokas\")'>No. Jurnal/Tanggal</td>";
    $fgcolor = ColumnColor("petugas");
    echo "<td width='10%' class='header' style='cursor:pointer; color: $fgcolor;' onClick='onChangeUrut(\"petugas\")'>Petugas</td>";
    echo "<td width='*' class='header'>Transaksi</td>";
    $fgcolor = ColumnColor("debet");
    echo "<td width='15%' class='header' style='cursor:pointer; color: $fgcolor;' onClick='onChangeUrut(\"debet\")'>Debet</td>";
    $fgcolor = ColumnColor("kredit");
    echo "<td width='15%' class='header' style='cursor:pointer; color: $fgcolor;' onClick='onChangeUrut(\"kredit\")'>Kredit</td>";
    echo "</tr>";

    $sql = "SELECT nokas, date_format(tanggal, '%d-%b-%Y') AS tanggal, jam, petugas, 
                   transaksi, keterangan, debet, kredit 
              FROM jbsfina.transaksilog 
             WHERE departemen = '$departemen' 
               AND tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
               AND idtahunbuku = '$idTahunBuku' 
             ORDER BY $urut DESC ";
    if ($pageLimit)
        $sql .= " LIMIT $startIndex, $nRowPerPage";

    $res = $db->QueryDb($sql);
    $cnt = $startIndex;
    while ($row = mysqli_fetch_array($res))
    {
        $cnt += 1;

        echo "<tr height='25'>";
        echo "<td align='center' valign='top' class='numberColumn'>$cnt</td>";
        echo "<td align='center' valign='top'><strong>$row[nokas]</strong><br>$row[tanggal] $row[jam]</td>";
        echo "<td valign='top' align='center'>$row[petugas]</td>";
        echo "<td align='left' valign='top'>$row[transaksi]";
        if (strlen(trim($row['keterangan'])) > 0)
            echo "<br><strong>Keterangan: </strong>$row[keterangan]";
        echo "</td>";
        echo "<td align='right' valign='top'>" . FormatRupiah($row['debet']) . "</td>";
        echo "<td align='right' valign='top'>" . FormatRupiah($row['kredit']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<input type='hidden' id='urut' value='$urut'>";
}
?>
