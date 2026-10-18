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
$yrNow = date('Y');
$mnNow = date('n');
$dyNow = date('j');

$nRowPerPage = 10;

function ShowSelectTanggal()
{
    global $yrNow, $mnNow, $dyNow;

    $sql = "SELECT YEAR(NOW()), MONTH(NOW()), DAY(NOW())";
    $res = QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        $yrNow = $row[0];
        $mnNow = $row[1];
        $dyNow = $row[2];
    }

    echo "<select id='tahun' name='tahun' onchange='changeTanggal()'>";
    for($i = 2010; $i <= $yrNow + 1; $i++)
    {
        $sel = $i == $yrNow ? "selected" : "";
        echo "<option value='$i' $sel>$i</option>";
    }
    echo "</select>";

    echo "<select id='bulan' name='bulan' onchange='changeTanggal()'>";
    for($i = 1; $i <= 12; $i++)
    {
        $sel = $i == $mnNow ? "selected" : "";
        echo "<option value='$i' $sel>" . InaMonthName($i) . "</option>";
    }
    echo "</select>";

    $nMax = GetMaxDay($yrNow, $mnNow);
    echo "<span id='spCbTanggal'>";
    echo "<select id='tanggal' name='tanggal' onchange='clearReport()'>";
    for($i = 1; $i <= $nMax; $i++)
    {
        $sel = $i == $dyNow ? "selected" : "";
        echo "<option value='$i' $sel>$i</option>";
    }
    echo "</select>";
    echo "</span>";
}

function ShowCbTanggal()
{
    $yrNow = date('Y');
    $mnNow = date('n');
    $dyNow = date('j');

    $tahun = $_REQUEST["tahun"];
    $bulan = $_REQUEST["bulan"];

    $nMax = GetMaxDay($tahun, $bulan);
    echo "<select id='tanggal' name='tanggal' onchange='clearReport()'>";
    for($i = 1; $i <= $nMax; $i++)
    {
        $sel = "";
        if ($tahun == $yrNow && $bulan == $mnNow)
            $sel = $i == $dyNow ? "selected" : "";

        echo "<option value='$i' $sel>$i</option>";
    }
    echo "</select>";
}

function ShowSelectPetugas($db)
{
    $sql = "SELECT userid, nama FROM jbsfina.userpos WHERE aktif = 1 ORDER BY nama";
    $res = $db->QueryDb($sql);

    echo "<select id='petugas' name='petugas' class='inputbox' style='width: 300px' onchange='changePetugas()'>";
    echo "<option value='@0#'>(Semua Petugas)</option>";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}

function ShowSelectVendor($db, $userId)
{
    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT DISTINCT vu.vendorid, v.nama
                  FROM jbsfina.vendoruser vu, jbsfina.vendor v
                 WHERE vu.vendorid = v.vendorid
                   AND vu.userid = '$userId'
                 ORDER BY v.nama";
        $res = $db->QueryDb($sql);

        echo "<select id='vendor' name='vendor' class='inputbox' style='width: 300px' onchange='clearReport()'>";
        echo "<option value='@0#'>(Semua Vendor)</option>";
        while($row = mysqli_fetch_row($res))
        {
            echo "<option value='$row[0]'>$row[1]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kmegm");
    }
    finally
    {
        $db->Close();
    }
}

function ShowRekapDailyReport($showMenu)
{
    $db = new Db();
    try
    {
        $db->Open();

        $tanggal = $_REQUEST["tanggal"];
        $petugas = $_REQUEST["petugas"];
        $vendor = $_REQUEST["vendor"];

        $sql = "SELECT SUM(p.jumlah), COUNT(DISTINCT p.transactionid)
                  FROM jbsfina.paymenttrans p
                 WHERE p.tanggal = '$tanggal'";

        if ($petugas != "@0#")
            $sql .= " AND p.userid = '$petugas'";

        if ($vendor != "@0#")
            $sql .= " AND p.vendorid = '$vendor'";

        $res = $db->QueryDb($sql);
        $row = mysqli_fetch_row($res);
        $jumlah = $row[0];
        $count = $row[1];

        echo "<table border='0' cellpadding='2'>";
        echo "<tr>";
        echo "<td style='width: 180px' valign='top'>";
        echo "<input type='hidden' id='ndata' value='$count'>";
        echo "<span style='color: #999'>Jumlah Transaksi</span><br>";
        echo "<span style='color: #333; font-size: 18px'>$count</span>&nbsp;&nbsp;";
        echo "</td>";
        echo "<td style='width: 180px' valign='top'>";
        echo "<span style='color: #999'>Besar Transaksi</span><br>";
        echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($jumlah) . " </span>";
        echo "</td>";
        echo "<td style='width: 180px' valign='top'>";
        if ($showMenu)
        {
            echo "<a href='#' class='hide-in-report' onclick='cetakReport()'><img src='../images/ico/print.png' border='0'>&nbsp;cetak</a>&nbsp;&nbsp;";
            echo "<a href='#' class='hide-in-report' onclick='excelReport()'><img src='../images/ico/excel.png' border='0'>&nbsp;excel</a>";
        }
        echo "</td>";
        echo "</table>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kj91z");
    }
    finally
    {
        $db->Close();
    }
}

function ShowDailyReport($showMenu, $pageLimit = true)
{
    global $nRowPerPage;

    $db = new Db();
    try
    {
        $db->Open();

        $tanggal = $_REQUEST["tanggal"];
        $petugas = $_REQUEST["petugas"];
        $vendor = $_REQUEST["vendor"];
        $nData = $_REQUEST["ndata"];
        $page = $_REQUEST["page"];

        $nPage = ceil($nData / $nRowPerPage);
        $startIndex = ($page - 1) * $nRowPerPage;

        $sql = "SELECT p.transactionid, DATE_FORMAT(p.waktu, '%d-%b-%Y %H:%i') AS waktu,
                       v.nama as namavendor, u.nama AS namauser, p.jenis, IFNULL(p.nis, '') AS nis, IFNULL(s.nama, '') AS namasiswa,
                       IFNULL(p.nip, '') AS nip, IFNULL(pg.nama, '') AS namapegawai, p.jumlah, p.keterangan, p.jenistrans, p.iddatapenerimaan,
                       IFNULL(dp.nama, '') AS namapenerimaan, IF(p.valmethod = 1, 'PIN', 'Agreement') AS valmethod,
                       IF(r.waktu IS NULL, '<b>(belum refund)</b>', DATE_FORMAT(r.waktu, '%d-%b-%Y %H:%i')) AS refund
                  FROM jbsfina.paymenttrans p
                 INNER JOIN jbsfina.vendor v ON p.vendorid = v.vendorid
                 INNER JOIN jbsfina.userpos u ON p.userid = u.userid
                  LEFT JOIN jbsakad.siswa s ON p.nis = s.nis
                  LEFT JOIN jbssdm.pegawai pg ON p.nip = pg.nip
                  LEFT JOIN jbsfina.datapenerimaan dp ON p.iddatapenerimaan = dp.replid
                  LEFT JOIN jbsfina.refund r ON p.idrefund = r.replid
                 WHERE p.tanggal = '$tanggal'";

        if ($petugas != "@0#")
            $sql .= " AND p.userid = '$petugas'";

        if ($vendor != "@0#")
            $sql .= " AND p.vendorid = '$vendor'";

        $sql .= " ORDER BY p.waktu DESC, p.transactionid";

        if ($pageLimit)
            $sql .= " LIMIT $startIndex, $nRowPerPage";

        $res = $db->QueryDb($sql);
        $num = mysqli_num_rows($res);
        if ($num == 0)
        {
            echo "belum ada data transaksi";
            return;
        }

        if ($pageLimit)
            $no = $startIndex;
        else
            $no = 0;

        $total = 0;

        echo "<table id='table' border='1' cellpadding='5' cellspacing='0' class='tab' style='border-width: 1px;'>";
        echo "<tr style='height: 30px;'>";
        echo "<td align='center' class='header' width='40'>No</td>";
        echo "<td align='left' class='header' width='150'>Waktu</td>";
        echo "<td align='left' class='header' width='180'>Vendor / Petugas</td>";
        echo "<td align='left' class='header' width='180'>Pelanggan</td>";
        echo "<td align='right' class='header' width='150'>Jumlah</td>";
        echo "<td align='left' class='header' width='150'>Jenis</td>";
        echo "<td align='left' class='header' width='120'>Validasi</td>";
        echo "<td align='left' class='header' width='250'>Keterangan</td>";
        if ($showMenu)
        {
            echo "<td align='left' class='header' width='40'>&nbsp;</td>";
        }
        echo "</tr>";
        while($row = mysqli_fetch_array($res))
        {
            $no += 1;

            if ($row['jenis'] == 1)
            {
                $nip = $row["nip"];
                $pelanggan  = "Pegawai: " . $row["namapegawai"] . " - ";
                $pelanggan .= "<a class='ablue' onclick='showInfoPegawai(\"$nip\")'>$nip</a>";
            }
            else
            {
                $nis = $row["nis"];
                $pelanggan  = "Siswa: " . $row["namasiswa"] . " - ";
                $pelanggan .= "<a class='ablue' onclick='showInfoSiswa(\"$nis\")'>$nis</a>";
            }

            $jumlah = FormatRupiah($row["jumlah"]);
            $total += $row["jumlah"];

            $jenisTrans = $row["jenistrans"];
            $pembayaran = "";
            if ($jenisTrans == 0)
                $pembayaran = "Pembayaran Vendor";
            else if ($jenisTrans == 1)
                $pembayaran = "Pembayaran Iuran Wajib " . $row["namapenerimaan"];
            else if ($jenisTrans == 2)
                $pembayaran = "Pembayaran Iuran Sukarela " . $row["namapenerimaan"];

            $keterangan = "";
            $ket = $row["keterangan"];
            if (strlen($ket) != 0)
                $keterangan = "Ket: " . $ket . "<br>";
            $keterangan .= "Id Trans: " . $row["transactionid"] . "<br>";
            if ($jenisTrans == 0)
                $keterangan .= "Refund: " . $row["refund"];

            $transId = $row["transactionid"];

            echo "<tr>";
            echo "<td align='center' class='numberColumn'>$no</td>";
            echo "<td align='left'>$row[waktu]</td>";
            echo "<td align='left'>$row[namavendor]<br>$row[namauser]</td>";
            echo "<td align='left'>$pelanggan</td>";
            echo "<td align='right'>$jumlah</td>";
            echo "<td align='left'>$pembayaran</td>";
            echo "<td align='left'>$row[valmethod]</td>";
            echo "<td align='left'>$keterangan</td>";
            if ($showMenu)
            {
                echo "<td align='center' class='hide-in-report' valign='top'>";
                echo "<a href='#' onclick=\"cetakKuitansi('$transId')\" title='cetak kuitansi'>";
                echo "<img src='../images/ico/print.png' border='0'></a>";
                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";

        if ($pageLimit)
        {
            echo "<div class='hide-in-report'>";
            echo "halaman&nbsp;&nbsp;";
            echo "<input type='button' class='but' style='width: 30px; height: 25px'  value=' < ' onclick='prevPage()'>&nbsp;&nbsp;";
            echo "<select id='page' class='inputbox' style='width: 80px' onchange='changePage()'>";
            for($i = 1; $i <= $nPage; $i++)
            {
                $sel = $page == $i ? "selected" : "";
                echo "<option value='$i' $sel>$i</option>";
            }
            echo "</select>&nbsp;&nbsp;";
            echo "<input type='button' class='but' style='width: 30px; height: 25px'  value=' > ' onclick='nextPage()'>";
            echo " dari $nPage, jumlah $nData data";
            echo "</div>";
        }

    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kj91z");
    }
    finally
    {
        $db->Close();
    }
}
?>
