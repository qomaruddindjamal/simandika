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

function PrepareNisList($db)
{
    global $nisList;
    global $idTabungan, $idTingkat, $idKelas;

    if ($idKelas == -1)
    {
        $sql = "SELECT IFNULL(GROUP_CONCAT(DISTINCT CONCAT(\"'\", s.nis, \"'\") SEPARATOR \",\"), \"\")
                  FROM jbsfina.tabungan t, jbsakad.siswa s, jbsakad.kelas k
                 WHERE s.nis = t.nis
                   AND s.aktif = 1
                   AND s.alumni = 0 
                   AND s.idkelas = k.replid
                   AND t.idtabungan = $idTabungan 
                   AND k.idtingkat = $idTingkat";
    }
    else
    {
        $sql = "SELECT IFNULL(GROUP_CONCAT(DISTINCT CONCAT(\"'\", s.nis, \"'\") SEPARATOR \",\"), \"\")
                  FROM jbsfina.tabungan t, jbsakad.siswa s, jbsakad.kelas k
                 WHERE s.nis = t.nis
                   AND s.aktif = 1
                   AND s.alumni = 0
                   AND s.idkelas = k.replid
                   AND t.idtabungan = $idTabungan 
                   AND k.replid = $idKelas";
    }
    $res = $db->QueryDb($sql);
    $row = mysqli_fetch_row($res);
    $nisList = $row[0];
}

function ShowRekapTabunganSiswa($db, $showMenu = true)
{
    global $idTabungan, $nisList;

    $allsetor = 0;
    $alltarik = 0;
    $allsaldo = 0;

    $sql = "SELECT SUM(debet), SUM(kredit)
              FROM jbsfina.tabungan
             WHERE idtabungan = '$idTabungan'
               AND nis IN ($nisList)";
    $result = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($result))
    {
        $alltarik = $row[0];
        $allsetor = $row[1];
        $allsaldo = $allsetor - $alltarik;
    }

    echo "<div id='dvRekapTabungan'>";
    echo "<table id='tabRekapTabungan' cellpadding='5'>";
    echo "<tr>";
    echo "<td style='width: 180px' valign='top'>";
    echo "<span style='color: #999'>Total Setoran</span><br>";
    echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($allsetor) . " </span>&nbsp;&nbsp;";
    echo "</td>";
    echo "<td style='width: 180px' valign='top'>";
    echo "<span style='color: #999'>Total Tarikan</span><br>";
    echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($alltarik) . " </span>&nbsp;&nbsp;";
    echo "</td>";
    echo "<td style='width: 180px' valign='top'>";
    echo "<span style='color: #999'>Total Saldo</span><br>";
    echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($allsaldo) . " </span><br>";
    echo "</td>";
    echo "<td style='width: 250px' valign='bottom'>";
    if ($showMenu)
    {
        echo "<div id='dvMenu'>";
        echo "<a class='hide-in-report' href='JavaScript:refresh()'><img src='../images/ico/refresh.png' border='0'>&nbsp;refresh</a>&nbsp;&nbsp;&nbsp;";
        echo "<a class='hide-in-report' href='JavaScript:cetak()'><img src='../images/ico/print.png' border='0'>&nbsp;cetak</a>&nbsp;&nbsp;&nbsp;";
        echo "<a class='hide-in-report' href='JavaScript:excel()'><img src='../images/ico/excel.png' border='0'>&nbsp;excel</a>";
        echo "</div>";
    }
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";
}

function ShowDaftarTabunganSiswa($db, $pageLimit = true)
{
    global $page, $nRowPerPage;
    global $nisList, $urut, $idTabungan;

    echo "<div id='dvDaftarTabungan'>";
    echo "<table class='tab' id='tabDaftarTabungan' border='1' cellpadding='5' style='border-collapse:collapse' cellspacing='0'>";
    echo "<tr style='height: 30px;'>";
    echo "<td width='30' class='header'>No</td>";
    echo "<td width='80' class='header'>NIS</td>";
    echo "<td width='140' class='header'>Nama</td>";
    echo "<td width='75' class='header'>Kelas</td>";
    echo "<td width='150' align='right' class='header'>Total Setoran</td>";
    echo "<td width='150' align='right' class='header'>Setoran Terakhir</td>";
    echo "<td width='150' align='right' class='header'>Total Tarikan</td>";
    echo "<td width='150' align='right' class='header'>Tarikan Terakhir</td>";
    echo "<td width='170' align='right' class='header'>Saldo Tabungan</td>";
    echo "<td width='50' align='center' class='header'>&nbsp;</td>";
    echo "</tr>";

    $startIndex = ($page - 1) * $nRowPerPage;
    $cnt = $startIndex;

    $sql = "SELECT s.nis, s.nama, t.tingkat, k.kelas, s.aktif
              FROM jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t
             WHERE s.idkelas = k.replid
               AND k.idtingkat = t.replid
               AND s.nis IN ($nisList)
             ORDER BY $urut";

    if ($pageLimit)
        $sql .= " LIMIT $startIndex, $nRowPerPage";

    $res = $db->QueryDb($sql);
    while($rowsis = mysqli_fetch_row($res))
    {
        $nis = $rowsis[0];
        $nama = $rowsis[1];
        $tingkat = $rowsis[2];
        $kelas = $rowsis[3];
        $aktif = $rowsis[4];

        $totaltarik = 0;
        $totalsetor = 0;
        $saldo = 0;

        $sql = "SELECT SUM(debet), SUM(kredit)
			      FROM jbsfina.tabungan
				 WHERE idtabungan = '$idTabungan'
				   AND nis = '$nis'";
        $result = $db->QueryDb($sql);
        if ($row = mysqli_fetch_row($result))
        {
            $totaltarik = $row[0];
            $totalsetor = $row[1];
            $saldo = $totalsetor - $totaltarik;
        }

        $setorakhir = 0;
        $tglsetorakhir = "";

        $sql = "SELECT DATE_FORMAT(tanggal, '%d-%b-%Y %H:%i:%s'), kredit
				  FROM jbsfina.tabungan
				 WHERE idtabungan = '$idTabungan'
				   AND nis = '$nis'
				   AND kredit <> 0
				 ORDER BY replid DESC
				 LIMIT 1";
        $result = $db->QueryDb($sql);
        if ($row = mysqli_fetch_row($result))
        {
            $tglsetorakhir = $row[0];
            $setorakhir = $row[1];
        }

        $tarikakhir = 0;
        $tgltarikakhir = "";

        $sql = "SELECT DATE_FORMAT(tanggal, '%d-%b-%Y %H:%i:%s'), debet
				  FROM jbsfina.tabungan
				 WHERE idtabungan = '$idTabungan'
				   AND nis = '$nis'
				   AND debet <> 0
				 ORDER BY replid DESC
				 LIMIT 1";
        $result = $db->QueryDb($sql);
        if ($row = mysqli_fetch_row($result))
        {
            $tgltarikakhir = $row[0];
            $tarikakhir = $row[1];
        }

        $cnt += 1;

        echo "<tr>";
        echo "<td align='center' class='numberColumn'>$cnt</td>";
        echo "<td align='left'>";
        echo "<a class='ablue' onclick='showInfoSiswa(\"$nis\")'>$nis</a>";
        echo "</td>";
        echo "<td align='left'>$nama</td>";
        echo "<td align='left'>$kelas</td>";
        echo "<td align='right' style='background-color:#E0F3FF'><b>" . FormatRupiah($totalsetor) . "</b></td>";
        echo "<td align='right' style='background-color:#E0F3FF'><b>". FormatRupiah($setorakhir) . "</b><br><i>$tglsetorakhir</i></td>";
        echo "<td align='right' style='background-color:#F2E9C6'><b>" . FormatRupiah($totaltarik) . "</b></td>";
        echo "<td align='right' style='background-color:#F2E9C6'><b>" . FormatRupiah($tarikakhir) . "</b><br><i>$tgltarikakhir</i></td>";
        echo "<td align='right' style='background-color:#DBF4C1'><b>" . FormatRupiah($saldo) . "</b></td>";
        echo "<td align='center' style='background-color: #ededed'><img src='../images/ico/lihat.png' style='cursor: pointer' onclick='showRiwayatTabungan(\"$nis\",\"$nama\")' title='riwayat'></td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
}

function ShowPageControl()
{
    global $page, $nisList, $nRowPerPage;

    $nData = substr_count($nisList, ",") + 1;
    $totalPage = ceil($nData / $nRowPerPage);

    echo "<div id='dvPageControl' style='width: 100%'>";
    echo "<input type='hidden' id='totalpage' value='$totalPage'>";
    echo "Halaman&nbsp;";
    echo "<input type='button' class='but' style='height: 28px; width: 28px' value=' < ' onclick='onPrevPage()'>";
    echo "<select id='page' class='inputbox' style='width: 50px' onchange='onChangePage()'>";
    for ($i = 1; $i <= $totalPage; $i++)
    {
        $sel = $i == $page ? "selected" : "";
        echo "<option value='$i' $sel>$i</option>";
    }
    echo "</select>";
    echo "<input type='button' class='but' style='height: 28px; width: 28px' value=' > ' onclick='onNextPage()'>";
    echo "&nbsp;dari $totalPage, jumlah $nData data";
    echo "</div>";
}
?>
