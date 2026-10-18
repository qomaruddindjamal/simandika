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
function GetNoKas()
{
    global $idtahunbuku;

    //Ambil awalan dan cacah tahunbuku untuk bikin nokas;
    $sql = "SELECT awalan, cacah
              FROM tahunbuku
             WHERE replid = '$idtahunbuku'";
    $row = FetchSingleRow($sql);
    $awalan = $row[0];
    $cacah = $row[1];

    $cacah += 1; // Increment cacah
    $nokas = $awalan . rpad($cacah, "0", 6); // Form nomor kas

    return $nokas;
}

function SavePengeluaran($rowno)
{
    global $idtahunbuku, $transactions;

    $tmp = "i_jumlah_$rowno";
    $jumlah = $_REQUEST[$tmp];

    $tmp = "i_tanggal_$rowno";
    $tanggal = $_REQUEST[$tmp];

    $tmp = "i_idpengeluaran_$rowno";
    $idpengeluaran = $_REQUEST[$tmp];

    $tmp = "i_namapengeluaran_$rowno";
    $namapengeluaran = $_REQUEST[$tmp];

    $tmp = "i_rekkas_$rowno";
    $rekkas = $_REQUEST[$tmp];

    $tmp = "i_rekbeban_$rowno";
    $rekbeban = $_REQUEST[$tmp];

    $tmp = "i_keperluan_$rowno";
    $keperluan = $_REQUEST[$tmp];

    $tmp = "i_pengguna_$rowno";
    $pengguna = $_REQUEST[$tmp];

    $tmp = "i_penerima_$rowno";
    $penerima = $_REQUEST[$tmp];

    $tmp = "i_keterangan_$rowno";
    $keterangan = $_REQUEST[$tmp];

    //-- petugas pendata & keterangan
    $idpetugas = getIdUser();
    $petugas = getUserName();

    $nokas = GetNoKas();

    $idjurnal = 0;
    $success = SimpanJurnal($idtahunbuku, $tanggal, $keperluan, $nokas, "", $idpetugas, $petugas, "pengeluaran", $idjurnal);

    //Simpan ke jurnaldetail
    if ($success)
        $success = SimpanDetailJurnal($idjurnal, "D", $rekbeban, $jumlah);

    if ($success)
        $success = SimpanDetailJurnal($idjurnal, "K", $rekkas, $jumlah);

    if ($success)
    {
        $sql = "UPDATE jbsfina.tahunbuku 
                   SET cacah=cacah+1 
                 WHERE replid='$idtahunbuku'";
        QueryDbTrans($sql, $success);
    }

    $sql = "INSERT INTO jbsfina.pengeluaran 
               SET idpengeluaran='$idpengeluaran', idjurnal='$idjurnal', tanggal='$tanggal', 
                   jumlah='$jumlah', keperluan='$keperluan', keterangan='$keterangan', 
                   petugas='$petugas', tanggalkeluar=now(), penerima='$penerima', 
                   jenispemohon=0, namapemohon='$pengguna'";
    if ($success)
        QueryDbTrans($sql, $success);

    if ($success)
        $transactions[] = [ $namapengeluaran, $keperluan, $pengguna, $penerima, $jumlah ];

    return $success;
}

function CreateDivPrintReportDetail()
{
    global $transactions;

    echo "<table border='1' cellpadding='2' cellspacing='0' style='border-width: 1px; border-collapse: collapse;'>";
    echo "<tr height='25'>";
    echo "<td width='25' align='center'>No</td>";
    echo "<td width='540' align='center'>Keperluan</td>";
    echo "<td width='120' align='center'>Jumlah</td>";
    echo "</tr>";

    $total = 0;
    for($i = 0; $i < count($transactions); $i++)
    {
        $total += $transactions[$i][4];

        echo "<tr height='35' style='font-size: 8px;'>";
        echo "<td align='center' valign='top'>" . ($i + 1) . "</td>";
        echo "<td align='left' valign='top'>" . $transactions[$i][0] . "<br>";
        echo "<i>" . $transactions[$i][1] . "</i>";
        echo "</td>";
        echo "<td align='right' valign='top'>" . FormatRupiah($transactions[$i][4]) . "</td>";
        echo "</tr>";
    }

    echo "<tr height='25'>";
    echo "<td colspan='2' align='right'><strong>TOTAL</strong></td>";
    echo "<td align='right'><strong>". FormatRupiah($total) . "</strong></td>";
    echo "</tr>";
    echo "</table>";
}
?>