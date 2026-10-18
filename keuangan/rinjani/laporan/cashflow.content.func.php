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
function ShowCashflow($db, $showMenu = true)
{
    global $idTahunBuku, $tanggal1, $tanggal2;

    if ($showMenu)
    {
        echo "<table border='0' width='70%' align='center' cellpadding='10' cellspacing='0'>";
        echo "<tr>";
        echo "<td>";
        echo "<font size='4'><strong>LAPORAN ARUS KAS</strong></font><br />";
        echo "<font size='2'>Per Tanggal " . LongDateFormat($tanggal2) . "</font>";
        echo "<td align='right' valign='top'>";
        echo "<a href='#' onClick='document.location.reload()'><img src='../images/ico/refresh.png' border='0' >&nbsp;refresh</a>&nbsp;&nbsp;";
        echo "<a href='JavaScript:cetak()'><img src='../images/ico/print.png' border='0'>&nbsp;cetak</a>&nbsp;&nbsp;";
        echo "<a href='JavaScript:excel()'><img src='../images/ico/excel.png' border='0'>&nbsp;excel</a>";
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "<br>";
    }

    $ls = explode("-", $tanggal2);
    $thn = $ls[0];
    $bln = $ls[1];

    $firstDate = "$thn-$bln-1";
    $sql = "SELECT date_sub('$firstDate', INTERVAL 1 DAY)";
    $res = $db->QueryDb($sql);
    $row = mysqli_fetch_row($res);
    $lastDate = $row[0];

    echo "<div id='dvLaporan'>";

    echo "<table border='0' id='table' cellpadding='10' cellspacing='5' align='center' width='70%'>";
    echo "<tr height='30'>";
    echo "<td colspan='4' align='left'><font size='2'><strong>Arus Kas dari Kegiatan Operasional</strong></font></td>";
    echo "</tr>";

    // A. Jumlah Setiap Pendapatan dari Iuran Wajib Siswa
    $totalpendapatan = 0;
    $sql = "SELECT kode, nama 
              FROM jbsfina.rekakun 
             WHERE kategori = 'PENDAPATAN' 
             ORDER BY kode";
    $res = $db->QueryDb($sql);
    while ($row = mysqli_fetch_row($res))
    {
        $koderek = $row[0];
        $namarek = $row[1];

        $sql = "SELECT sum(jd.debet - jd.kredit) 
                  FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
                 WHERE jd.koderek = ra.kode 
                   AND ra.kategori = 'HARTA' 
                   AND jd.idjurnal IN (
                       SELECT j.replid 
                         FROM jbsfina.jurnal j, jbsfina.penerimaanjtt p, jbsfina.besarjtt b, jbsfina.datapenerimaan dp 
                        WHERE j.replid = p.idjurnal 
                          AND p.idbesarjtt = b.replid 
                          AND b.idpenerimaan = dp.replid 
                          AND dp.rekpendapatan = '$koderek' 
                          AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                          AND j.idtahunbuku = '$idTahunBuku')";
        $res2 = $db->QueryDb($sql);
        $row2 = mysqli_fetch_row($res2);
        $jpendapatan = (float)$row2[0];
        if ($jpendapatan > 0)
        {
            $totalpendapatan += $jpendapatan;

            echo "<tr height='25'>";
            echo "<td width='20'>&nbsp;</td>";
            echo "<td width='420'>A Kas diterima dari $namarek </td>";
            echo "<td width='120' align='right'>" . FormatRupiah($jpendapatan) . "</td>";
            echo "<td width='120' align='right'>&nbsp;</td>";
            echo "</tr>";
        }
    }

    // B. Jumlah Setiap Pendapatan dari Iuran Sukarela Siswa
    $sql = "SELECT kode, nama 
              FROM jbsfina.rekakun 
             WHERE kategori = 'PENDAPATAN' ORDER BY kode";
    $res = $db->QueryDb($sql);
    while ($row = mysqli_fetch_row($res))
    {
        $koderek = $row[0];
        $namarek = $row[1];

        $sql = "SELECT sum(jd.debet - jd.kredit) 
                  FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
                 WHERE jd.koderek = ra.kode 
                   AND ra.kategori = 'HARTA' 
                   AND jd.idjurnal IN (
                       SELECT j.replid 
                         FROM jbsfina.jurnal j, jbsfina.penerimaaniuran p, jbsfina.datapenerimaan dp 
                        WHERE j.replid = p.idjurnal 
                          AND p.idpenerimaan = dp.replid 
                          AND dp.rekpendapatan = '$koderek' 
                          AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                          AND j.idtahunbuku = '$idTahunBuku')";
        $res2 = $db->QueryDb($sql);
        $row2 = mysqli_fetch_row($res2);
        $jpendapatan = (float)$row2[0];
        if ($jpendapatan > 0)
        {
            $totalpendapatan += $jpendapatan;

            echo "<tr height='25'>";
            echo "<td width='20'>&nbsp;</td>";
            echo "<td width='420'>B Kas diterima dari $namarek </td>";
            echo "<td width='120' align='right'>" . FormatRupiah($jpendapatan) . "</td>";
            echo "<td width='120' align='right'>&nbsp;</td>";
            echo "</tr>";
        }
    }

    // C. Jumlah Setiap Pendapatan dari Iuran Wajib Calon Siswa
    $sql = "SELECT kode, nama 
              FROM jbsfina.rekakun 
             WHERE kategori = 'PENDAPATAN' 
             ORDER BY kode";
    $res = $db->QueryDb($sql);
    while ($row = mysqli_fetch_row($res))
    {
        $koderek = $row[0];
        $namarek = $row[1];
        $sql = "SELECT sum(jd.debet - jd.kredit) 
                  FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
                 WHERE jd.koderek = ra.kode 
                   AND ra.kategori = 'HARTA' 
                   AND jd.idjurnal IN (
                       SELECT j.replid 
                         FROM jbsfina.jurnal j, jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsfina.datapenerimaan dp 
                        WHERE j.replid = p.idjurnal 
                          AND p.idbesarjttcalon = b.replid 
                          AND b.idpenerimaan = dp.replid 
                          AND dp.rekpendapatan = '$koderek' 
                          AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                          AND j.idtahunbuku = '$idTahunBuku')";

        $res2 = $db->QueryDb($sql);
        $row2 = mysqli_fetch_row($res2);
        $jpendapatan = (float)$row2[0];
        if ($jpendapatan > 0)
        {
            $totalpendapatan += $jpendapatan;

            echo "<tr height='25'>";
            echo "<td width='20'>&nbsp;</td>";
            echo "<td width='420'>C Kas diterima dari $namarek </td>";
            echo "<td width='120' align='right'>" . FormatRupiah($jpendapatan) . "</td>";
            echo "<td width='120' align='right'>&nbsp;</td>";
            echo "</tr>";
        }

    }

    // D. Jumlah Setiap Pendapatan dari Iuran Sukarela Calon Siswa
    $sql = "SELECT kode, nama 
              FROM jbsfina.rekakun 
             WHERE kategori = 'PENDAPATAN' 
             ORDER BY kode";
    $res = $db->QueryDb($sql);
    while ($row = mysqli_fetch_row($res))
    {
        $koderek = $row[0];
        $namarek = $row[1];

        $sql = "SELECT sum(jd.debet - jd.kredit) 
                  FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
                 WHERE jd.koderek = ra.kode 
                   AND ra.kategori = 'HARTA' 
                   AND jd.idjurnal IN (
                       SELECT j.replid 
                         FROM jbsfina.jurnal j, jbsfina.penerimaaniurancalon p, jbsfina.datapenerimaan dp 
                        WHERE j.replid = p.idjurnal 
                          AND p.idpenerimaan = dp.replid 
                          AND dp.rekpendapatan = '$koderek' 
                          AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                          AND j.idtahunbuku = '$idTahunBuku')";
        $res2 = $db->QueryDb($sql);
        $row2 = mysqli_fetch_row($res2);
        $jpendapatan = (float)$row2[0];
        if ($jpendapatan > 0)
        {
            $totalpendapatan += $jpendapatan;

            echo "<tr height='25'>";
            echo "<td width='20'>&nbsp;</td>";
            echo "<td width='420'>D Kas diterima dari $namarek </td>";
            echo "<td width='120' align='right'>" . FormatRupiah($jpendapatan) . "</td>";
            echo "<td width='120' align='right'>&nbsp;</td>";
            echo "</tr>";
        }
    }

    // E Jumlah Setiap Pendapatan dari Peneriman Lain
    $sql = "SELECT kode, nama 
              FROM jbsfina.rekakun 
             WHERE kategori = 'PENDAPATAN' 
             ORDER BY kode";
    $res = $db->QueryDb($sql);
    while ($row = mysqli_fetch_row($res))
    {
        $koderek = $row[0];
        $namarek = $row[1];

        $sql = "SELECT sum(jd.debet - jd.kredit) 
                  FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
                 WHERE jd.koderek = ra.kode 
                   AND ra.kategori = 'HARTA' 
                   AND jd.idjurnal IN (
                       SELECT j.replid 
                         FROM jbsfina.jurnal j, jbsfina.penerimaanlain p, jbsfina.datapenerimaan dp 
                        WHERE j.replid = p.idjurnal 
                          AND p.idpenerimaan = dp.replid 
                          AND dp.rekpendapatan = '$koderek' 
                          AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                          AND j.idtahunbuku = '$idTahunBuku')";
        $res2 = $db->QueryDb($sql);
        $row2 = mysqli_fetch_row($res2);
        $jpendapatan = (float)$row2[0];
        if ($jpendapatan > 0)
        {
            $totalpendapatan += $jpendapatan;

            echo "<tr height='25'>";
            echo "<td width='20'>&nbsp;</td>";
            echo "<td width='420'>D Kas diterima dari $namarek </td>";
            echo "<td width='120' align='right'>" . FormatRupiah($jpendapatan) . "</td>";
            echo "<td width='120' align='right'>&nbsp;</td>";
            echo "</tr>";
        }
    }

    // F Jumlah Pembayaran Beban
    $sql = "SELECT SUM(jd.debet - jd.kredit) 
                  FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
                 WHERE jd.koderek = ra.kode 
                   AND ra.kategori = 'HARTA' 
                   AND jd.idjurnal IN (
                       SELECT jd.idjurnal 
                         FROM jbsfina.jurnaldetail jd, jbsfina.jurnal j, jbsfina.rekakun ra 
                        WHERE jd.idjurnal = j.replid 
                          AND jd.koderek = ra.kode 
                          AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                          AND j.idtahunbuku = '$idTahunBuku' 
                          AND ra.kategori = 'BIAYA')";
    $res2 = $db->QueryDb($sql);
    $row2 = mysqli_fetch_row($res2);
    $totalbiaya = (float)$row2[0];

    echo "<tr height='25'>";
    echo "<td width='20'>&nbsp;</td>";
    echo "<td width='420'>Pembayaran Beban</td>";
    echo "<td width='120' align='right'>". FormatRupiah($totalbiaya) . "</td>";
    echo "<td width='120' align='right'>&nbsp;</td>";
    echo "</tr>";

    echo "";
    echo "<tr height='30'>";
    echo "<td width='20'>&nbsp;</td>";
    echo "<td width='420'><font size='2'><strong><em>Arus Kas Bersih Kegiatan Operasional</em></strong></font></td>";
    echo "<td width='120' align='right'>&nbsp;</td>";
    echo "<td width='120' align='right'><font size='2'><strong>";
    $totaloperasional = ($totalpendapatan + $totalbiaya);
    echo FormatRupiah($totaloperasional);
    echo "</strong></font></td>";
    echo "</tr>";
    echo "";
    echo "";
    echo "<tr height='5'>";
    echo "<td colspan='4' align='left'>&nbsp;</td>";
    echo "</tr>";
    echo "";
    echo "<tr height='30'>";
    echo "<td colspan='4' align='left'><font size='2'><strong>Arus Kas dari Kegiatan Keuangan</strong></font></td>";
    echo "</tr>";

    // G Penambahan Piutang
    $sql = "SELECT SUM(jd.debet - jd.kredit) 
              FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
             WHERE jd.koderek = ra.kode 
               AND ra.kategori = 'HARTA' 
               AND jd.kredit > 0 
               AND jd.idjurnal IN (
                   SELECT jd.idjurnal 
                     FROM jbsfina.jurnaldetail jd, jbsfina.jurnal j, jbsfina.rekakun ra 
                    WHERE j.sumber = 'jurnalumum' 
                      AND jd.idjurnal = j.replid 
                      AND jd.koderek = ra.kode 
                      AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                      AND j.idtahunbuku = '$idTahunBuku'
                      AND ra.kategori = 'PIUTANG' 
                      AND jd.debet > 0)
              GROUP BY ra.nama";
    $res = $db->QueryDb($sql);
    $totalpiutangtambah = 0;
    while($row = mysqli_fetch_row($res))
    {
        $piutang = (float)$row[0];
        $totalpiutangtambah += $piutang;

        echo "<tr height='25'>";
        echo "<td width='20'>&nbsp;</td>";
        echo "<td width='420'>G Penambahan Piutang Usaha</td>";
        echo "<td width='120' align='right'>" . FormatRupiah($piutang) . "</td>";
        echo "<td width='120' align='right'>&nbsp;</td>";
        echo "</tr>";
    }

    // H Pengurangan Piutang
    $sql = "SELECT SUM(jd.debet - jd.kredit) 
              FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
             WHERE jd.koderek = ra.kode 
               AND ra.kategori = 'HARTA' 
               AND jd.debet > 0 
               AND jd.idjurnal IN (
                   SELECT jd.idjurnal 
                     FROM jbsfina.jurnaldetail jd, jbsfina.jurnal j, jbsfina.rekakun ra 
                    WHERE j.sumber = 'jurnalumum' 
                      AND jd.idjurnal = j.replid 
                      AND jd.koderek = ra.kode
                      AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                      AND j.idtahunbuku = '$idTahunBuku' 
                      AND ra.kategori = 'PIUTANG' 
                      AND jd.kredit > 0)
             GROUP BY ra.nama";
    $res = $db->QueryDb($sql);
    $totalpiutangkurang = 0;
    while($row = mysqli_fetch_row($res))
    {
        $piutang = (float)$row[0];
        $totalpiutangkurang += $piutang;

        echo "<tr height='25'>";
        echo "<td width='20'>&nbsp;</td>";
        echo "<td width='420'>H Pengurangan Piutang Usaha</td>";
        echo "<td width='120' align='right'>" . FormatRupiah($piutang) . "</td>";
        echo "<td width='120' align='right'>&nbsp;</td>";
        echo "</tr>";
    }

    // I Jumlah Penurunan Hutang
    $sql = "SELECT SUM(jd.debet - jd.kredit) 
              FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
             WHERE jd.koderek = ra.kode 
               AND ra.kategori = 'HARTA' 
               AND jd.kredit > 0 
               AND jd.idjurnal IN (
                   SELECT jd.idjurnal 
                     FROM jbsfina.jurnaldetail jd, jbsfina.jurnal j, jbsfina.rekakun ra 
                    WHERE j.sumber <> 'saldoawal' 
                      AND jd.idjurnal = j.replid 
                      AND jd.koderek = ra.kode 
                      AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                      AND j.idtahunbuku = '$idTahunBuku' 
                      AND ra.kategori = 'UTANG' 
                      AND jd.debet > 0)";
    $res = $db->QueryDb($sql);
    $row = mysqli_fetch_row($res);
    $totalutangturun = (float)$row[0];
    echo "<tr height='25'>";
    echo "<td width='20'>&nbsp;</td>";
    echo "<td width='420'>I Penurunan Utang</td>";
    echo "<td width='120' align='right'>" . FormatRupiah($totalutangturun) . "</td>";
    echo "<td width='120' align='right'>&nbsp;</td>";
    echo "</tr>";

    // J Jumlah Kenaikan Hutang
    $sql = "SELECT SUM(jd.debet - jd.kredit) 
              FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
             WHERE jd.koderek = ra.kode 
               AND ra.kategori = 'HARTA' 
               AND jd.debet > 0 
               AND jd.idjurnal IN (
                   SELECT jd.idjurnal 
                     FROM jbsfina.jurnaldetail jd, jbsfina.jurnal j, jbsfina.rekakun ra 
                    WHERE j.sumber <> 'saldoawal' 
                      AND jd.idjurnal = j.replid 
                      AND jd.koderek = ra.kode 
                      AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                      AND j.idtahunbuku = '$idTahunBuku' 
                      AND ra.kategori = 'UTANG' 
                      AND jd.kredit > 0)";
    $res = $db->QueryDb($sql);
    $row = mysqli_fetch_row($res);
    $totalutangnaik = (float)$row[0];
    echo "<tr height='25'>";
    echo "<td width='20'>&nbsp;</td>";
    echo "<td width='420'>J Kenaikan Utang</td>";
    echo "<td width='120' align='right'>" . FormatRupiah($totalutangnaik) . "</td>";
    echo "<td width='120' align='right'>&nbsp;</td>";
    echo "</tr>";

    echo "<tr height='30'>";
    echo "<td width='20'>&nbsp;</td>";
    echo "<td width='420'><font size='2'><strong><em>Arus Kas Bersih Kegiatan Keuangan</em></strong></font></td>";
    echo "<td width='120' align='right'>&nbsp;</td>";
    echo "<td width='120' align='right'><font size='2'><strong>";
    $totalkeuangan = $totalpiutangtambah + $totalpiutangkurang + $totalutangturun + $totalutangnaik;
    echo  FormatRupiah($totalkeuangan);
    echo "</strong></font></td>";
    echo "</tr>";
    echo "";
    echo "<tr height='5'>";
    echo "<td colspan='4' align='left'>&nbsp;</td>";
    echo "</tr>";
    echo "";
    echo "<tr height='30'>";
    echo "<td colspan='4' align='left'><font size='2'><strong>Arus Kas dari Kegiatan Investasi</strong></font></td>";
    echo "</tr>";

    //K Penambahan kas dari setoran modal
    $sql = "SELECT x.nama, SUM(jd.debet - jd.kredit) 
              FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra,
                   (SELECT jd.idjurnal, ra.nama 
                      FROM jbsfina.jurnaldetail jd, jbsfina.jurnal j, jbsfina.rekakun ra 
                     WHERE j.sumber = 'jurnalumum' 
                       AND jd.idjurnal = j.replid 
                       AND jd.koderek = ra.kode
                       AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                       AND j.idtahunbuku = '$idTahunBuku' 
                       AND ra.kategori = 'MODAL' 
                       AND jd.kredit > 0) AS x
             WHERE x.idjurnal = jd.idjurnal 
               AND jd.koderek = ra.kode 
               AND jd.debet > 0 
               AND ra.kategori = 'HARTA' 
             GROUP BY x.nama";
    $result = $db->QueryDb($sql);
    $totalmodalterima = 0;
    while($row = mysqli_fetch_row($result))
    {
        $totalmodalterima += (float)$row[1];

        echo "<tr height='25'>";
        echo "<td width='20'>&nbsp;</td>";
        echo "<td width='420'>K Kas diterima dari penambahan $row[0] </td>";
        echo "<td width='120' align='right'>" . FormatRupiah($row[1]) . "</td>";
        echo "<td width='120' align='right'>&nbsp;</td>";
        echo "</tr>";
    }

    // L Pengembilan kas dari modal
    $sql = "SELECT x.nama, SUM(jd.debet - jd.kredit) 
              FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra,
                   (SELECT jd.idjurnal, ra.nama 
                      FROM jbsfina.jurnaldetail jd, jbsfina.jurnal j, jbsfina.rekakun ra 
                     WHERE j.sumber = 'jurnalumum' 
                       AND jd.idjurnal = j.replid 
                       AND jd.koderek = ra.kode 
                       AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                       AND j.idtahunbuku = '$idTahunBuku' 
                       AND ra.kategori = 'MODAL' 
                       AND jd.debet > 0) AS x
             WHERE x.idjurnal = jd.idjurnal 
               AND jd.koderek = ra.kode 
               AND jd.kredit > 0 
               AND ra.kategori = 'HARTA' 
             GROUP BY x.nama";
    $res = $db->QueryDb($sql);
    $totalmodalambil = 0;
    while($row = mysqli_fetch_row($res))
    {
        $totalmodalambil += (float)$row[1];

        echo "<tr height='25'>";
        echo "<td width='20'>&nbsp;</td>";
        echo "<td width='420'>L Pengurangan kas dari pengambilan $row[0] </td>";
        echo "<td width='120' align='right'>" . FormatRupiah($row[1]) . "</td>";
        echo "<td width='120' align='right'>&nbsp;</td>";
        echo "</tr>";
    }

    // M INVESTASi
    $sql = "SELECT x.nama, SUM(jd.debet - jd.kredit) 
              FROM jbsfina.jurnaldetail jd, jbsfina.rekakun ra,
                   (SELECT jd.idjurnal, ra.nama 
                      FROM jbsfina.jurnaldetail jd, jbsfina.jurnal j, jbsfina.rekakun ra 
                     WHERE j.sumber = 'jurnalumum' 
                       AND jd.idjurnal = j.replid 
                       AND jd.koderek = ra.kode 
                       AND j.tanggal BETWEEN '$firstDate' AND '$tanggal2' 
                       AND j.idtahunbuku = '$idTahunBuku' 
                       AND ra.kategori = 'INVENTARIS') AS x
            WHERE x.idjurnal = jd.idjurnal 
              AND jd.koderek = ra.kode 
              AND ra.kategori = 'HARTA' 
            GROUP BY x.nama";
    $res = $db->QueryDb($sql);
    $totalinvest = 0;
    $subinvest = 0;
    while($row = mysqli_fetch_row($res))
    {
        $invest = (float)$row[1];
        $subinvest += $invest;

        echo "<tr height='25'>";
        echo "<td width='20'>&nbsp;</td>";
        echo "<td width='420'>M $row[0] </td>";
        echo "<td width='120' align='right'>" . FormatRupiah($invest) . "</td>";
        echo "<td width='120' align='right'>&nbsp;</td>";
        echo "</tr>";
    }

    echo "<tr height='30'>";
    echo "<td width='20'>&nbsp;</td>";
    echo "<td width='420'><font size='2'><strong><em>Arus Kas Bersih Kegiatan Investasi</em></strong></font></td>";
    echo "<td width='120' align='right'>&nbsp;</td>";
    echo "<td width='120' align='right'><font size='2'><strong>";
    $totalinvest = $totalmodalterima + $totalmodalambil + $subinvest;
    echo FormatRupiah($totalinvest);
    echo "</strong></font></td>";
    echo "</tr>";
    echo "";
    echo "<tr height='5'>";
    echo "<td colspan='4' align='left'>&nbsp;</td>";
    echo "</tr>";

    echo "<tr height='30'>";
    echo "<td colspan='3'><font size='2'><strong><em>Perubahan Kas</em></strong></font></td>";
    echo "<td width='150' align='right'><font size='2'><strong>";
    $totalperubahan = $totaloperasional + $totalkeuangan + $totalinvest;
    echo FormatRupiah($totalperubahan);
    echo "</strong></font></td>";
    echo "</tr>";

    $sql = "SELECT SUM(jd.debet - jd.kredit) 
              FROM jbsfina.jurnaldetail jd, jbsfina.jurnal j, jbsfina.rekakun ra 
             WHERE jd.idjurnal = j.replid 
               AND jd.koderek = ra.kode 
               AND j.tanggal BETWEEN '$tanggal1' AND '$lastDate' 
               AND j.idtahunbuku = '$idTahunBuku' 
               AND ra.kategori = 'HARTA'";
    $res = $db->QueryDb($sql);
    $row = mysqli_fetch_row($res);
    $saldoawal = (float)$row[0];

    echo "<tr height='30'>";
    echo "<td colspan='3'><font size='2'><strong><em>Saldo Kas " . LongDateFormat($firstDate) . "</em></strong></font></td>";
    echo "<td width='120' align='right'><font size='2'><strong>";
    echo FormatRupiah($saldoawal);
    echo "</strong></font></td>";
    echo "</tr>";

    echo "<tr height='30'>";
    echo "<td colspan='3'><font size='2'><strong><em>Saldo Kas " . LongDateFormat($tanggal2) . "</em></strong></font></td>";
    echo "<td width='150' align='right'><font size='2'><strong>";
    echo FormatRupiah($saldoawal + $totalperubahan);
    echo "</strong></font></td>";
    echo "</tr>";
    echo "</table>";

    echo "</div>";
}
?>
