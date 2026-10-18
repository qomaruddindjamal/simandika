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
function ShowCbVendor($db)
{
    $sql = "SELECT vendorid, nama 
              FROM jbsfina.vendor
             WHERE aktif = 1
             ORDER BY nama";
    $res = $db->QueryDb($sql);

    echo "<select id='vendor' name='vendor' onchange='clearReport()' class='inputbox' style='width: 250px'>";
    echo "<option value='@0#'>(Semua Vendor)</option>";
    while($row = mysqli_fetch_row($res))
    {
        echo "<option value='$row[0]'>$row[1]</option>";
    }
    echo "</select>";
}

function ShowStatTransReport($showMenu)
{
    $db = new Db();
    try
    {
        $db->Open();

        $vendorId = $_REQUEST["vendorid"];
        $dtStart = $_REQUEST["dtstart"];
        $dtEnd = $_REQUEST["dtend"];

        $sql = "SELECT DISTINCT DATE_FORMAT(tanggal, '%d-%b-%Y') AS ftanggal, tanggal
                  FROM jbsfina.paymenttrans
                 WHERE tanggal BETWEEN '$dtStart' AND '$dtEnd'";
        if ($vendorId <> "@0#")
            $sql .= " AND vendorid = '$vendorId'";
        $sql .= "ORDER BY tanggal";

        $res = $db->QueryDb($sql);

        $lsTanggal = array();
        while($row = mysqli_fetch_row($res))
        {
            $lsTanggal[] = array($row[0], $row[1]);
        }

        if (count($lsTanggal) == 0)
        {
            echo "Belum ada transaksi";
            return;
        }

        $sb = new StringBuilder();
        $sb->AppendLine("<br>");
        if ($showMenu)
        {
            $sb->AppendLine("<a href='#' onclick='cetakReport()'><img src='../images/ico/print.png' border='0'>&nbsp;cetak</a>&nbsp;&nbsp;");
            $sb->AppendLine( "<a href='#' onclick='excelReport()'><img src='../images/ico/excel.png' border='0'>&nbsp;excel</a>");
        }

        $sb->AppendLine("<table id='table' border='1' cellspacing='0' cellpadding='5' class='tab' style='border-width: 1px; border-collapse: collapse'>");
        $sb->AppendLine("<tr style='height: 30px'>");
        $sb->AppendLine("<td width='50' rowspan='2' align='center' class='header-bgonly'>No</td>");
        $sb->AppendLine("<td width='150' rowspan='2' align='center' class='header-bgonly'>Tanggal</td>");
        $sb->AppendLine("<td width='240' colspan='2' align='center' class='header-bgonly'>Transaksi Siswa</td>");
        $sb->AppendLine("<td width='240' colspan='2' align='center' class='header-bgonly'>Transaksi Pegawai</td>");
        $sb->AppendLine("<td width='240' colspan='2' align='center' class='header-bgonly'>Sub Total</td>");
        $sb->AppendLine("</tr>");
        $sb->AppendLine("<tr style='height: 20px'>");
        $sb->AppendLine("<td width='80' align='center' class='header-bgonly'>Banyak</td>");
        $sb->AppendLine("<td width='160' align='center' class='header-bgonly'>Jumlah</td>");
        $sb->AppendLine("<td width='80' align='center' class='header-bgonly'>Banyak</td>");
        $sb->AppendLine("<td width='160' align='center' class='header-bgonly'>Jumlah</td>");
        $sb->AppendLine("<td width='80' align='center' class='header-bgonly'>Banyak</td>");
        $sb->AppendLine("<td width='160' align='center' class='header-bgonly'>Jumlah</td>");
        $sb->AppendLine("</tr>");


        $arrSub = array(0, 0, 0, 0, 0, 0);

        $no = 0;
        for($i = 0; $i < count($lsTanggal); $i++)
        {
            $no += 1;
            $ftanggal = $lsTanggal[$i][0];
            $tanggal = $lsTanggal[$i][1];

            $sb->AppendLine("<tr style='height: 30px'>");
            $sb->AppendLine("<td align='center' class='numberColumn'>$no</td>");
            $sb->AppendLine("<td align='center'>$ftanggal</td>");

            $sql = "SELECT COUNT(replid), IFNULL(SUM(jumlah), 0) 
                      FROM jbsfina.paymenttrans
                     WHERE tanggal = '$tanggal'
                       AND jenis = 2 ";
            if ($vendorId <> "@0#")
                $sql .= " AND vendorid = '$vendorId'";

            $res = $db->QueryDb($sql);
            $row = mysqli_fetch_row($res);
            $counta = $row[0];
            $suma = $row[1];
            $rpa = FormatRupiah($suma);

            $arrSub[0] += $counta;
            $arrSub[1] += $suma;

            $sb->AppendLine("<td align='center'>$counta</td>");
            $sb->AppendLine("<td align='right'>$rpa</td>");

            $sql = "SELECT COUNT(replid), IFNULL(SUM(jumlah), 0) 
                      FROM jbsfina.paymenttrans
                     WHERE tanggal = '$tanggal'
                       AND jenis = 1 ";
            if ($vendorId <> "@0#")
                $sql .= " AND vendorid = '$vendorId'";

            $res = $db->QueryDb($sql);
            $row = mysqli_fetch_row($res);
            $countb = $row[0];
            $sumb = $row[1];
            $rpb = FormatRupiah($sumb);

            $arrSub[2] += $countb;
            $arrSub[3] += $sumb;

            $sb->AppendLine("<td align='center'>$countb</td>");
            $sb->AppendLine("<td align='right'>$rpb</td>");

            $count = $counta + $countb;
            $sum = $suma + $sumb;
            $rp = FormatRupiah($sum);

            $arrSub[4] += $count;
            $arrSub[5] += $sum;

            $sb->AppendLine("<td align='center'>$count</td>");
            $sb->AppendLine("<td align='right'>$rp</td>");

            $sb->AppendLine("</tr>");
        }

        // SUB TOTAL
        $sb->AppendLine("<tr style='height: 50px; background-color: #ededed'>");
        $sb->AppendLine("<td align='right' colspan='2' style='background-color: #ededed'><strong>SUB TOTAL</strong></td>");
        $cnt = $arrSub[0];
        $rp = FormatRupiah($arrSub[1]);
        $sb->AppendLine("<td align='center' style='background-color: #ededed'><strong>$cnt</strong></td>");
        $sb->AppendLine("<td align='right' style='background-color: #ededed'><strong>$rp</strong></td>");
        $cnt = $arrSub[2];
        $rp = FormatRupiah($arrSub[3]);
        $sb->AppendLine("<td align='center' style='background-color: #ededed'><strong>$cnt</strong></td>");
        $sb->AppendLine("<td align='right' style='background-color: #ededed'><strong>$rp</strong></td>");
        $cnt = $arrSub[4];
        $rp = FormatRupiah($arrSub[5]);
        $sb->AppendLine("<td align='center' style='background-color: #ededed'><strong>$cnt</strong></td>");
        $sb->AppendLine("<td align='right' style='background-color: #ededed'><strong>$rp</strong></td>");
        $sb->AppendLine("</tr>");

        $sb->AppendLine("</table>");

        $sb->AppendLine("</table>");

        echo $sb->ToString();
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kqqjr");
    }
    finally
    {
        $db->Close();
    }
}
?>