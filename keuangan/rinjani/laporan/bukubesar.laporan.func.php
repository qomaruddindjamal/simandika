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

function ShowRekapBukuBesar($db)
{
    global $nRowPerPage, $totalPage, $nData;
    global $idTahunBuku, $tanggal1, $tanggal2, $koderek;

    $sql = "SELECT COUNT(j.nokas), SUM(jd.debet), SUM(jd.kredit) 
              FROM jbsfina.jurnal j, jbsfina.jurnaldetail jd 
             WHERE j.replid = jd.idjurnal 
               AND j.idtahunbuku = '$idTahunBuku' 
               AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
               AND jd.koderek = '$koderek'";
    $res = $db->QueryDb($sql);
    if (mysqli_num_rows($res) == 0)
    {
        echo "<span style='color: maroon'>belum ada data buku besar</span>";
        exit();
    }
    $row = mysqli_fetch_row($res);

    $nData = $row[0];
    $totalDebet = $row[1];
    $totalKredit = $row[2];

    $totalPage = ceil($nData / $nRowPerPage);

    echo "<input type='hidden' id='totalpage' value='$totalPage'>";
    echo "<input type='hidden' id='ndata' value='$nData'>";
    echo "<table border='0' cellpadding='0' cellspacing='2'>";
    echo "<tr>";
    echo "<td width='180'>";
    echo "<span style='color: #999'>Jumlah Data</span><br>";
    echo "<span style='color: #333; font-size: 18px'> $nData </span>";
    echo "</td>";
    echo "<td width='180'>";
    echo "<span style='color: #999'>Total Debet</span><br>";
    echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($totalDebet) . "</span>";
    echo "</td>";
    echo "<td width='180'>";
    echo "<span style='color: #999'>Total Kredit</span><br>";
    echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($totalKredit) . "</span>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
}

function ShowTransaksiBukuBesar($db, $pageLimit = true)
{
    global $nRowPerPage, $page;
    global $idTahunBuku, $tanggal1, $tanggal2, $koderek, $urut;

    $startIndex = ($page - 1) * $nRowPerPage;
    $cnt = $startIndex;

    echo "<table class='tab' id='table' border='1' cellpadding='2' style='border-collapse:collapse' cellspacing='0' width='100%' align='center'>";
    echo "<tr>";
    echo "<td class='header' width='5%' align='center'>No</td>";
    echo "<td class='header' width='20%' align='center' style='cursor:pointer;' onClick='onChangeUrut(\"j.nokas\")'>No. Jurnal/Tgl</td>";
    echo "<td class='header' width='9%' align='center'>Petugas</td>";
    echo "<td class='header' width='*' align='center'>Transaksi</td>";
    echo "<td class='header' width='15%' align='center'>Debet</td>";
    echo "<td class='header' width='15%' align='center'>Kredit</td>";
    echo "</tr>";

    $sql = "SELECT date_format(j.tanggal, '%d-%b-%Y') AS tanggal, j.jam, j.petugas, 
                   j.transaksi, j.keterangan, j.nokas, jd.debet, jd.kredit 
              FROM jbsfina.jurnal j, jbsfina.jurnaldetail jd 
             WHERE j.replid = jd.idjurnal 
               AND j.idtahunbuku = '$idTahunBuku' 
               AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
               AND jd.koderek = '$koderek' 
             ORDER BY $urut DESC";
    if ($pageLimit)
         $sql .= " LIMIT $startIndex, $nRowPerPage";

    $res = $db->QueryDb($sql);
    while($row = mysqli_fetch_array($res))
    {
        $cnt += 1;

        echo "<tr style='height: 30px'>";
        echo "<td valign='top' align='center' class='numberColumn'>$cnt</td>";
        echo "<td valign='top' align='center'><strong>$row[nokas]</strong><br /><em>$row[tanggal]<br>$row[jam]</em></td>";
        echo "<td valign='top' align='left'>$row[petugas]</td>";
        echo "<td valign='top' align='left'>$row[transaksi]<br>";
        if (strlen(trim($row['keterangan'])) > 0)
            echo "<strong>Keterangan: </strong>$row[keterangan]";
        echo "</td>";
        echo "<td valign='top' align='right'>" . FormatRupiah($row['debet']) . "</td>";
        echo "<td valign='top' align='right'>" . FormatRupiah($row['kredit']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

}

function ShowPageControl()
{
    global $totalPage, $nData;

    echo "Halaman&nbsp;&nbsp;";
    echo "<input type='button' class='but' style='height:28px;' value='  <  ' onclick='onPrevPage()'>";
    echo "<select id='page' class='inputbox' style='width: 50px' onchange='onChangePage()'>";
    for ($i = 1; $i <= $totalPage; $i++)
    {
        echo "<option value='$i'>$i</option>";
    }
    echo "</select>";
    echo "<input type='button' class='but' style='height:28px;' value='  >  ' onclick='onNextPage()'>";
    echo "&nbsp;dari $totalPage, jumlah $nData data";
}
?>
