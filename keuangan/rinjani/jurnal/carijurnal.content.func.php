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
function CountDataJurnal()
{
    $db = new Db();
    try
    {
        $db->Open();

        $kriteria = $_REQUEST["kriteria"];
        $keyword = $_REQUEST["keyword"];
        $idTahunBuku = $_REQUEST["idtahunbuku"];
        $tanggal1 = $_REQUEST["tanggal1"];
        $tanggal2 = $_REQUEST["tanggal2"];

        if ($kriteria == 0)
        {
            $sqlCount = "SELECT COUNT(replid)
                           FROM jbsfina.jurnal 
                          WHERE idtahunbuku = '$idTahunBuku' 
                            AND tanggal BETWEEN '$tanggal1' AND '$tanggal2'";
        }
        else if ($kriteria == 1)
        {
            $sqlCount = "SELECT COUNT(replid)
                           FROM jbsfina.jurnal 
                          WHERE transaksi LIKE '%$keyword%'
                            AND idtahunbuku = '$idTahunBuku' 
                            AND tanggal BETWEEN '$tanggal1' AND '$tanggal2'";
        }
        else if ($kriteria == 2)
        {
            $sqlCount = "SELECT COUNT(replid)
                           FROM jbsfina.jurnal 
                          WHERE nokas LIKE '%$keyword%'
                            AND idtahunbuku = '$idTahunBuku' 
                            AND tanggal BETWEEN '$tanggal1' AND '$tanggal2'";
        }
        else if ($kriteria == 3)
        {
            $sqlCount = "SELECT COUNT(replid)
                      FROM jbsfina.jurnal 
                     WHERE keterangan LIKE '%$keyword%' 
                       AND idtahunbuku = '$idTahunBuku' 
                       AND tanggal BETWEEN '$tanggal1' AND '$tanggal2'";
        }
        else if ($kriteria == 4)
        {
            $sqlCount = "SELECT COUNT(replid)
                           FROM jbsfina.jurnal 
                          WHERE petugas LIKE '%$keyword%' 
                            AND idtahunbuku = '$idTahunBuku' 
                            AND tanggal BETWEEN '$tanggal1' AND '$tanggal2'";
        }

        $nData = $db->ExecuteScalar($sqlCount, 0);

        return [1, $nData];
    }
    catch (Exception $ex)
    {
        return [-99, Msg::InfoError($ex->getMessage(), "ktb5y")];
    }
    finally
    {
        $db->Close();
    }
}

function NamaJurnal($sumber)
{
    $namaJurnal = "";

    switch($sumber)
    {
        case 'jurnalumum':
            $namaJurnal = "Jurnal Umum"; break;
        case 'penerimaanjtt':
            $namaJurnal = "Penerimaan Iuran Wajib Siswa"; break;
        case 'penerimaaniuran':
            $namaJurnal = "Penerimaan Iuran Sukarela Siswa"; break;
        case 'penerimaanlain':
            $namaJurnal = "Penerimaan Lain-Lain"; break;
        case 'pengeluaran':
            $namaJurnal = "Pengeluaran"; break;
        case 'penerimaanjttcalon':
            $namaJurnal = "Penerimaan Iuran Wajib Calon Siswa"; break;
        case 'penerimaaniurancalon':
            $namaJurnal = "Penerimaan Iuran Sukarela Calon Siswa"; break;
    }

    return $namaJurnal;
}

function ShowDataJurnal()
{
    $db = new Db();
    try
    {
        $db->Open();

        $kriteria = $_REQUEST["kriteria"];
        $keyword = $_REQUEST["keyword"];
        $idTahunBuku = $_REQUEST["idtahunbuku"];
        $tanggal1 = $_REQUEST["tanggal1"];
        $tanggal2 = $_REQUEST["tanggal2"];
        $page = $_REQUEST["page"];
        $nPage = $_REQUEST["npage"];
        $nData = $_REQUEST["ndata"];

        $nItemPerPage = 10;
        $startFrom = ($page - 1) * $nItemPerPage;

        if ($kriteria == 0)
        {
            $sql = "SELECT * 
                      FROM jbsfina.jurnal 
                     WHERE idtahunbuku = '$idTahunBuku' 
                       AND tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
                     ORDER BY replid DESC 
                     LIMIT $startFrom, $nItemPerPage";
        }
        else if ($kriteria == 1)
        {
            $sql = "SELECT * 
                      FROM jbsfina.jurnal 
                     WHERE transaksi LIKE '%$keyword%' 
                       AND idtahunbuku = '$idTahunBuku' 
                       AND tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
                     ORDER BY replid DESC 
                     LIMIT $startFrom, $nItemPerPage";
        }
        else if ($kriteria == 2)
        {
            $sql = "SELECT * 
                      FROM jbsfina.jurnal 
                     WHERE nokas LIKE '%$keyword%' 
                       AND idtahunbuku = '$idTahunBuku' 
                       AND tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
                     ORDER BY replid DESC
                     LIMIT $startFrom, $nItemPerPage";
        }
        else if ($kriteria == 3)
        {
            $sql = "SELECT * 
                      FROM jbsfina.jurnal 
                     WHERE keterangan LIKE '%$keyword%' 
                       AND idtahunbuku = '$idTahunBuku' 
                       AND tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
                     ORDER BY replid DESC
                     LIMIT $startFrom, $nItemPerPage";
        }
        else if ($kriteria == 4)
        {
            $sql = "SELECT * 
                      FROM jbsfina.jurnal 
                     WHERE petugas LIKE '%$keyword%' 
                       AND idtahunbuku = '$idTahunBuku' 
                       AND tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
                     ORDER BY replid DESC
                     LIMIT $startFrom, $nItemPerPage";
        }

        echo "<table border='1' id='tabContent' style='border-collapse:collapse' cellpadding='5' cellspacing='0' width='95%' class='tab' bordercolor='#000000'>";
        echo "<tr height='30' align='center'>";
        echo "<td width='4%' class='header'>No</td>";
        echo "<td width='15%' class='header'>No. Jurnal/Tanggal</td>";
        echo "<td width='35%' class='header'>Transaksi</td>";
        echo "<td class='header'>Detail Jurnal</td>";
        echo "<td width='3%' class='header colButton'>&nbsp;</td>";
        echo "</tr>";

        $cnt = 1 + $nData - ($page - 1) * $nItemPerPage;
        $res = $db->QueryDb($sql);
        while($row = mysqli_fetch_array($res))
        {
            $cnt = $cnt - 1;
            $idjurnal = $row['replid'];
            $jurnal = NamaJurnal($row['sumber']);

            echo "<tr height='25'>";
            echo "<td align='center' rowspan='2' class='numberColumn'><strong>$cnt</strong></td>";
            echo "<td colspan='2'><span style='font-size: 12px; color: #2455aa; font-weight: bold'>$row[transaksi]</span></td>";
            echo "<td valign='top' style='background-color: #f7f7f7' rowspan='2'>";

            $sql = "SELECT jd.koderek, ra.nama, jd.debet, jd.kredit 
                      FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
                     WHERE jd.idjurnal = '$idjurnal' 
                       AND jd.koderek = ra.kode 
                     ORDER BY jd.replid";
            $res2 = $db->QueryDb($sql);

            echo "<table border='1' style='border-collapse: collapse' width='100%' cellpadding='2' bgcolor='#FFFFFF'>";
            while ($row2 = mysqli_fetch_array($res2))
            {
                echo "<tr height='25'>";
                echo "<td width='8%' align='center'>$row2[koderek]</td>";
                echo "<td width='*' align='left'>$row2[nama]</td>";
                echo "<td width='23%' align='right'>" . FormatRupiah($row2['debet']) . "</td>";
                echo "<td width='23%' align='right'>" . FormatRupiah($row2['kredit']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</td>";

            echo "<td align='center' valign='center' class='colButton' rowspan='2'>";
            if ((getLevel() != 2))
            {
                if ($row['sumber'] == "jurnalumum")
                    echo "<img src='../images/ico/ubah.png' border='0' style='cursor: pointer' title='Ubah Jurnal Umum' onclick='edit($idjurnal)'/></a>";
                else
                    echo "<img src='../images/ico/ubah_x.png' border='0' title='Ubah di menu $jurnal'/>";
            }
            else
            {
                echo "&nbsp;";
            }
            echo "</td>";

            echo "</tr>";

            echo "<tr height='25'>";
            echo "<td align='center' valign='top'><strong>$row[nokas]</strong><br><i>" . LongDateFormat($row['tanggal']) . "<br>$row[jam]</i></td>";
            echo "<td align='left' valign='top'>";
            echo "<strong>Petugas: </strong>$row[petugas]<br>";
            echo "<strong>Sumber: </strong>$jurnal<br>";
            if (strlen($row['keterangan']) > 0)
                echo "<strong>Keterangan: </strong>$row[keterangan]";
            echo "</td>";
            echo "</tr>";
        }

        echo "<tr height='50' class='rowButton'>";
        echo "<td colspan='5' align='left' valign='middle'>";

        echo "halaman&nbsp;";
        echo "<input type='button' class='but' style='width: 30px; height: 25px'  value=' < ' onclick='prevPage()'>";
        echo "<select id='page' class='inputbox' style='width: 80px' onchange='changePage()'>";
        for($i = 1; $i <= $nPage; $i++)
        {
            $sel = $page == $i ? "selected" : "";
            echo "<option value='$i' $sel>$i</option>";
        }
        echo "</select>";
        echo "<input type='button' class='but' style='width: 30px; height: 25px'  value=' > ' onclick='nextPage()'>";
        echo " dari $nPage, jumlah $nData data";

        echo "</td>";
        echo "</tr>";
        echo "</table>";

    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kmnj2");
    }
    finally
    {
        $db->Close();
    }
}
?>
