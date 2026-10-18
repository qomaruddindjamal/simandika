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
require_once('../include/sessionchecker.php');
require_once('../include/sessioninfo.php');
require_once('../library/common.func.php');
require_once('../include/config.php');
require_once('../library/rupiah.php');
require_once('../include/db.onfunc.php');
require_once('../library/departemen.php');
require_once('../include/errorhandler.php');
require_once('../library/date.func.php');
require_once('../library/logger.php');
require_once('../library/msg.php');
require_once('../library/stringbuilder.php');

$op = $_REQUEST["op"];
if ($op <> "7834682374672834324")
    return;

$vendorId = $_REQUEST["vendorid"];
$departemen = $_REQUEST["departemen"];
$idTahunBuku = $_REQUEST["idtahunbuku"];
$idPenerima = $_REQUEST["idpenerima"];
$keterangan = $_REQUEST["keterangan"];
$nTanggal = $_REQUEST["ntanggal"];

$lsTanggal = array();
$lsTagihan = array();
$stTanggal = "";
$totalTagihan = 0;
$stAllIdPayment = "";
for($i = 1; $i <= $nTanggal; $i++)
{
    $param = "tanggal$i";
    $tanggal = $_REQUEST[$param];
    $lsTanggal[] = $tanggal;

    $param = "tagihan$i";
    $tagihan = $_REQUEST[$param];
    $lsTagihan[] = $tagihan;
    $totalTagihan += $tagihan;

    $param = "replid$i";
    if ($stAllIdPayment != "") $stAllIdPayment .= ",";
    $stAllIdPayment .= $_REQUEST[$param];

    if ($stTanggal <> "") $stTanggal .= ",";
    $stTanggal .= "'$tanggal'";
}

$db = new Db();
try
{
    $db->Open();
    $db->BeginTrans();

    // Ambil Konfigurasi Pembayaran utk Siswa
    $rekKasVendorSiswa = "---";
    $rekUtangVendorSiswa = "---";
    $sql = "SELECT rekkasvendor, rekutangvendor
              FROM jbsfina.paymenttabungan
             WHERE jenis = 2
               AND departemen = '$departemen'";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        $rekKasVendorSiswa = $row[0];
        $rekUtangVendorSiswa = $row[1];
    }

    // Ambil Konfigurasi Pembayaran utk Pegawai
    // Karena bisa beda rek kas dan rek utang yang digunakan
    $deptPeg = "---";
    $rekKasVendorPegawai = "---";
    $rekUtangVendorPegawai = "---";
    $sql = "SELECT departemen, rekkasvendor, rekutangvendor
              FROM jbsfina.paymenttabungan
             WHERE jenis = 1";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        $deptPeg = $row[0];
        $rekKasVendorPegawai = $row[1];
        $rekUtangVendorPegawai = $row[2];
    }

    // Ambil jumlah tagihan dari transaksi siswa
    $tagihanSiswa = 0;
    $sql = "SELECT IFNULL(SUM(p.jumlah), 0)
              FROM jbsfina.paymenttrans p 
             WHERE p.jenis = 2 
               AND p.replid IN ($stAllIdPayment)";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
        $tagihanSiswa = $row[0];

    // Ambil jumlah tagihan dari transaksi pegawai
    $tagihanPegawai = 0;
    $sql = "SELECT IFNULL(SUM(p.jumlah), 0)
              FROM jbsfina.paymenttrans p 
             WHERE p.jenis = 1  
               AND p.replid IN ($stAllIdPayment)";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
        $tagihanPegawai = $row[0];

    $idPetugas = getIdUser();
    $petugas = getUserName();
    if ($idPetugas == "landlord")
        $idPetugas = "NULL";
    else
        $idPetugas =  "'$idPetugas'";

    //Ambil awalan dan cacah tahunbuku untuk bikin nokas;
    $sql = "SELECT awalan, cacah
              FROM jbsfina.tahunbuku
             WHERE replid = '$idTahunBuku'";
    $row = $db->FetchSingleRow($sql);
    $awalan = $row[0];
    $cacah = $row[1];

// Simpan Jurnal untuk pembayaran tagihan dari transaksi Siswa
    $idJurnalSiswa = 0;
    if ($tagihanSiswa <> 0)
    {
        $cacah += 1;
        $noKas = $awalan . rpad($cacah, "0", 6); // Form nomor kas
        
        $transaksi = "Pembayaran refund penerimaan vendor dari pembayaran non tunai siswa";
        $sql = "INSERT INTO jbsfina.jurnal
                   SET idtahunbuku = $idTahunBuku, tanggal = CURDATE(), transaksi = '$transaksi',
                       nokas='$noKas', keterangan = '$keterangan',
                       idpetugas = $idPetugas, petugas = '$petugas', sumber='schoolpay'";
        $db->QueryDb($sql);

        $sql = "SELECT LAST_INSERT_ID()";
        $res = $db->QueryDb($sql);
        if ($row = mysqli_fetch_row($res))
            $idJurnalSiswa = $row[0];

        $sql = "INSERT INTO jbsfina.jurnaldetail 
                   SET idjurnal = $idJurnalSiswa, koderek = '$rekUtangVendorSiswa', debet = $tagihanSiswa";
        $db->QueryDb($sql);

        $sql = "INSERT INTO jbsfina.jurnaldetail 
                   SET idjurnal = $idJurnalSiswa, koderek = '$rekKasVendorSiswa', kredit = $tagihanSiswa";
        $db->QueryDb($sql);

        $sql = "UPDATE jbsfina.tahunbuku SET cacah = cacah + 1 WHERE replid = $idTahunBuku";
        $db->QueryDb($sql);
    }

    // Simpan Jurnal untuk pembayaran tagihan dari transaksi Pegawai
    $idJurnalPegawai = 0;
    if ($tagihanPegawai <> 0)
    {
        $cacah += 1; // Increment cacah
        $noKas = $awalan . rpad($cacah, "0", 6); // Form nomor kas

        $transaksi = "Pembayaran refund penerimaan vendor dari pembayaran non tunai pegawai";
        $sql = "INSERT INTO jbsfina.jurnal
 		           SET idtahunbuku = $idTahunBuku, tanggal = CURDATE(), transaksi = '$transaksi',
         			   nokas='$noKas', keterangan = '$keterangan',
	    		       idpetugas = $idPetugas, petugas = '$petugas', sumber='schoolpay'";
        $db->QueryDb($sql);

        $sql = "SELECT LAST_INSERT_ID()";
        $res = $db->QueryDb($sql);
        if ($row = mysqli_fetch_row($res))
            $idJurnalPegawai = $row[0];

        $sql = "INSERT INTO jbsfina.jurnaldetail 
                   SET idjurnal = $idJurnalPegawai, koderek = '$rekUtangVendorPegawai', debet = $tagihanPegawai";
        $db->QueryDb($sql);

        $sql = "INSERT INTO jbsfina.jurnaldetail 
                   SET idjurnal = $idJurnalPegawai, koderek = '$rekKasVendorPegawai', kredit = $tagihanPegawai";
        $db->QueryDb($sql);

        $sql = "UPDATE jbsfina.tahunbuku SET cacah = cacah + 1 WHERE replid = $idTahunBuku";
        $db->QueryDb($sql);
    }

    //  Simpan di riwayat refund
    $sql = "INSERT INTO jbsfina.refund
               SET idtahunbuku = $idTahunBuku, vendorid = '$vendorId', waktu = NOW(), nip = $idPetugas, 
                   jumlah = $totalTagihan, idpenerima = '$idPenerima', keterangan = '$keterangan', 
                   idjurnalsiswa = $idJurnalSiswa, idjurnalpegawai = $idJurnalPegawai ";
    $db->QueryDb($sql);

    $idRefund = 0;
    $sql = "SELECT LAST_INSERT_ID()";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
        $idRefund = $row[0];

    for($i = 0; $i < count($lsTanggal); $i++)
    {
        $tanggal = $lsTanggal[$i];

        $sql = "INSERT INTO jbsfina.refunddate
                   SET idrefund = $idRefund, tanggal = '$tanggal'";
        $db->QueryDb($sql);
    }

    // Update paymenttrans yg sudah di refund
    $sql = "UPDATE jbsfina.paymenttrans 
               SET idrefund = $idRefund 
             WHERE replid IN ($stAllIdPayment)";
    $db->QueryDb($sql);

    $db->CommitTrans();

    echo json_encode([1, "OK", $idRefund]);
}
catch (Exception $ex)
{
    $db->LogLastErrorIfExist();
    $db->RollbackTrans();

    echo json_encode([-99, Msg::InfoError($ex->getMessage(), "kae95"), 0]);
}
finally
{
    $db->Close();
}

?>