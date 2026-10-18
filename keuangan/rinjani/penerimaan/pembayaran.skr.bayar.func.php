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
function LoadValues($db, $idPembayaran)
{
    global $idjurnal, $rekkas, $jumlah, $keterangan, $sumberdana;

    try
    {
        $sql = "SELECT p.jumlah, p.keterangan, jd.koderek AS rekkas, p.sumberdana, j.replid 
                  FROM jbsfina.penerimaaniuran p, jbsfina.jurnal j, jbsfina.jurnaldetail jd, rekakun rk
                 WHERE p.idjurnal = j.replid
                   AND j.replid = jd.idjurnal
                   AND jd.koderek = rk.kode
                   AND p.replid = $idPembayaran
                   AND rk.kategori = 'HARTA'";
        $res = $db->QueryDb($sql);
        if ($row = mysqli_fetch_row($res))
        {
            $jumlah = $row[0];
            $keterangan = $row[1];
            $rekkas = $row[2];
            $sumberdana = $row[3];
            $idjurnal = $row[4];
        }
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k2yuy");
    }

}

function ShowSelectSumberDanaSkr($db)
{
    global $sumberdana;

    try
    {
        $sql = "SELECT kode, nama
                  FROM jbsfina.sumberdana
                 WHERE aktif = 1
                 ORDER BY urutan";
        $res = $db->QueryDb($sql);

        echo "<select id='sumberdana' class='inputbox' style='width:260px'>";
        $sel = $sumberdana == "***" ? "selected" : "";
        echo "<option value='***' $sel>(tidak ada data)</option>";
        while($row = mysqli_fetch_row($res))
        {
            $sel = $sumberdana == $row[0] ? "selected" : "";
            echo "<option value='$row[0]' $sel>$row[0] - $row[1]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "knya4");
    }
}

function ShowSelectRekKasSkr($db)
{
    global $defrekkas;

    try
    {
        $sql = "SELECT kode, nama
                  FROM jbsfina.rekakun
                 WHERE kategori = 'HARTA'
                 ORDER BY nama";
        $res = $db->QueryDb($sql);

        echo "<select id='rekkas' class='inputbox' style='width:260px'>";
        while($row = mysqli_fetch_row($res))
        {
            $sel = $row[0] == $defrekkas ? "selected" : "";
            echo "<option value='$row[0]' $sel>$row[0] $row[1]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "krvp4");
    }
}

function SimpanBaruSkr()
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = RequestData("departemen", "");
        $nis = RequestData("nis", "");
        $namasiswa =  RequestData("nama", "");
        $idpenerimaan = RequestData("idpenerimaan", 0);
        $penerimaan = RequestData("penerimaan", "");
        $idtahunbuku = RequestData("idtahunbuku", 0);
        $idpetugas = getIdUser();
        $petugas = getUserName();
        $jumlah = RequestData('jumlah', 'INVALID');
        $rekkas =  RequestData('rekkas', '');
        $rekpendapatan =  RequestData('rekpendapatan', '');
        $sumberdana =  RequestData('sumberdana', '***');
        $keterangan = RequestData("keterangan", "");
        $sendnotif = RequestData('sendnotif', 0);

        if ($sumberdana == "***")
            $sumberdana = "NULL";
        else
            $sumberdana = "'$sumberdana'";

        if ($jumlah == "INVALID")
            return json_encode([-1, "Data pembayaran tidak sesuai /keqsf"]);

        //----------------
        $sql = "SELECT nama, departemen, aktif
                  FROM jbsfina.datapenerimaan
                 WHERE replid = '$idpenerimaan'";
        $res = $db->QueryDb($sql);
        if (mysqli_num_rows($res) == 0)
            return json_encode([-1, "Tidak ditemukan data penerimaan /kdstx"]);

        $row = mysqli_fetch_row($res);
        $dp_nama = $row[0];
        $dp_departemen = $row[1];
        $dp_aktif = $row[2];

        if ($dp_aktif == 0)
            return json_encode([-1, "Penerimaan $dp_nama tidak aktif /kqexq"]);

        if ($dp_departemen != $departemen)
            return json_encode([-1, "Penerimaan $dp_nama tidak seusai untuk departemen $departemen /ktgfc"]);

        // -- Ambil awalan dan cacah tahunbuku untuk bikin nokas -------------
        $sql = "SELECT awalan, cacah 
                  FROM jbsfina.tahunbuku 
                 WHERE replid = '$idtahunbuku'";
        //Logger::LogOnce($sql);
        $row = $db->FetchSingleRow($sql);
        if ($row == null)
            return json_encode([-1, "Tidak ditemukan data tahun buku /kyp2r"]);

        $awalan = $row[0];
        $cacah = $row[1];
        $cacah += 1; //increment cacah
        $nokas = $awalan . rpad($cacah, "0", 6); //form nokas

        $db->BeginTrans();

        $tanggal = date('Y-m-d');
        $ketsms = "pembayaran $penerimaan";
        $ketjurnal = "Pembayaran $penerimaan siswa $namasiswa ($nis)";

        $idjurnal = SimpanJurnal2($db, $idtahunbuku, $tanggal, $ketjurnal, $nokas, "", $idpetugas, $petugas, "penerimaaniuran");

        SimpanDetailJurnal2($db, $idjurnal, "D", $rekkas, $jumlah);

        SimpanDetailJurnal2($db, $idjurnal, "K", $rekpendapatan, $jumlah);

        // -- increment cacah di tahunbuku -----------------------------------
        $sql = "UPDATE jbsfina.tahunbuku 
                   SET cacah=cacah+1 
                 WHERE replid='$idtahunbuku'";
        //Logger::LogOnce($sql);
        $db->QueryDb($sql);

        // -- simpan data cicilan di penerimaanjtt ---------------------------
        $sql = "INSERT INTO jbsfina.penerimaaniuran
				   SET idpenerimaan='$idpenerimaan', nis='$nis', idjurnal='$idjurnal', jumlah='$jumlah',
					   tanggal = CURDATE(), keterangan = '$keterangan', petugas='$petugas', sumberdana=$sumberdana";
        //Logger::LogOnce($sql);
        $db->QueryDb($sql);

        if ($sendnotif == 1)
        {
            $tanggal = date("Y-m-d");
            CreateSMSPaymentInfo2($db, 'SISPAY',
                $departemen, $nis, $namasiswa,
                RegularDateFormat($tanggal),
                FormatRupiah($jumlah),
                $ketsms);
        }

        $db->CommitTrans();
        //$db->RollbackTrans();

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kqhj6")]);
    }
    finally
    {
        $db->Close();
    }
}

function SimpanEditSkr()
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = RequestData("departemen", "");
        $nis = RequestData("nis", "");
        $namasiswa =  RequestData("nama", "");
        $idpenerimaan = RequestData("idpenerimaan", 0);
        $penerimaan = RequestData("penerimaan", "");
        $idtahunbuku = RequestData("idtahunbuku", 0);
        $idpembayaran = RequestData("idpembayaran", 0);
        $idpetugas = getIdUser();
        $petugas = getUserName();
        $jumlah = RequestData('jumlah', 'INVALID');
        $rekkas =  RequestData('rekkas', '');
        $rekpendapatan =  RequestData('rekpendapatan', '');
        $sumberdana =  RequestData('sumberdana', '***');
        $keterangan = RequestData("keterangan", "");
        $alasan = RequestData("alasan", "");
        $sendnotif = RequestData('sendnotif', 0);

        $origjumlah = RequestData("origjumlah", "INVALID");
        $origrekkas = RequestData("origrekkas", "");
        $idjurnal = RequestData("idjurnal", "0");

        if ($sumberdana == "***")
            $sumberdana = "NULL";
        else
            $sumberdana = "'$sumberdana'";

        if ($jumlah == "INVALID" || $origjumlah == "INVALID")
            return json_encode([-1, "Data pembayaran tidak sesuai /knsaz"]);

        $db->BeginTrans();

        if ($jumlah == $origjumlah)
        {
            //--------------------------------------------------------------
            // Hanya mengubah informasi pembayaran tanpa mengubah besarnya
            // -------------------------------------------------------------
            $sql = "UPDATE jbsfina.penerimaaniuran
				       SET keterangan = '$keterangan', sumberdana = $sumberdana,
					       alasan = '$alasan', petugas = '$petugas'
				     WHERE replid = $idpembayaran";
            //Logger::LogOnce($sql);
            $db->QueryDb($sql);

            if ($origrekkas != $rekkas)
            {
                $sql = "UPDATE jbsfina.jurnaldetail
					       SET koderek = '$rekkas'
					     WHERE idjurnal = '$idjurnal'
					       AND koderek = '$origrekkas'
					       AND kredit = 0";
                //Logger::LogOnce($sql);
                $db->QueryDb($sql);
            }

            $db->CommitTrans();
            //$db->RollbackTrans();
        }
        else
        {
            $paymentExist = false;
            $sql = "SELECT COUNT(replid)
		              FROM jbsfina.paymenttrans
		              WHERE idpenerimaaniuran = $idpembayaran";
            ///Logger::LogOnce($sql);
            $res = $db->QueryDb($sql);
            if ($row = mysqli_fetch_row($res))
            {
                $paymentExist = $row[0] > 0;
            }

            if ($paymentExist)
            {
                $sql = "SELECT p.nis, a.departemen, pt.idtabungan AS iddatatabungan, p.idjurnaltabcust, t.replid AS idtabungan,
                               dt.rekkas, dt.rekutang, p.jenistrans, IFNULL(p.idpenerimaanjtt, 0) AS idpenerimaanjtt, 
                               IFNULL(p.idpenerimaaniuran, 0) AS idpenerimaaniuran, IFNULL(p.iddatapenerimaan, 0) AS iddatapenerimaan,
                               p.replid AS idpayment
                          FROM jbsfina.paymenttrans p
                         INNER JOIN jbsakad.siswa s ON p.nis = s.nis
                         INNER JOIN jbsakad.angkatan a ON s.idangkatan = a.replid
                         INNER JOIN jbsfina.paymenttabungan pt ON pt.departemen = a.departemen AND pt.jenis = 2
                         INNER JOIN jbsfina.tabungan t ON p.idjurnaltabcust = t.idjurnal
                         INNER JOIN jbsfina.datatabungan dt ON t.idtabungan = dt.replid
                         WHERE p.idpenerimaaniuran = $idpembayaran";
                //Logger::LogOnce($sql);
                $res = $db->QueryDb($sql);

                $row = mysqli_fetch_array($res);
                $idDataTabungan = $row["iddatatabungan"];
                $idJurnalTabungan = $row["idjurnaltabcust"];
                $idTabungan = $row["idtabungan"];
                $rekKasTab = $row["rekkas"];
                $rekUtangTab = $row["rekutang"];
                $jenisTrans = $row["jenistrans"];
                $idPenerimaanIuran = $row["idpenerimaaniuran"];
                $idPayment = $row["idpayment"];

                // Cek Saldo
                $sql = "SELECT SUM(kredit) - SUM(debet)
                          FROM jbsfina.tabungan
                         WHERE nis = '$nis'
                           AND idtabungan = '$idDataTabungan'";
                //Logger::LogOnce($sql);
                $res = $db->QueryDb($sql);
                $row = mysqli_fetch_row($res);
                $jSaldo = $row[0];

                $sql = "SELECT debet
                          FROM jbsfina.tabungan
                         WHERE replid = $idTabungan";
                //Logger::LogOnce($sql);
                $res = $db->QueryDb($sql);
                $row = mysqli_fetch_row($res);
                $debetAwal = (int)$row[0];

                if ($jSaldo + $debetAwal < $jumlah)
                {
                    $msg = "Maaf, pembayaran tidak dapat dilakukan! Saldo tabungan tidak mencukupi untuk penarikan!";
                    return json_encode([-1, $msg]);
                }
            }

            $sql = "UPDATE jbsfina.penerimaaniuran
                       SET jumlah = '$jumlah', sumberdana = $sumberdana,
                           keterangan = '$keterangan', alasan='$alasan', petugas='$petugas'
                     WHERE replid = '$idpembayaran'";
            //Logger::LogOnce($sql);
            $db->QueryDb($sql);

            //$rekkas = AmbilKodeRekJurnal($db, $idjurnal, "HARTA", $idpenerimaan);
            //$rekpendapatan = AmbilKodeRekJurnal($db, $idjurnal, "PENDAPATAN", $idpenerimaan);

            $sql = "UPDATE jbsfina.jurnaldetail
                       SET debet = '$jumlah'
                     WHERE idjurnal = '$idjurnal' 
                       AND koderek = '$rekkas' 
                       AND kredit = 0";
            //Logger::LogOnce($sql);
            $db->QueryDb($sql);

            if ($origrekkas != $rekkas)
            {
                $sql = "UPDATE jbsfina.jurnaldetail
					       SET koderek = '$rekkas'
					     WHERE idjurnal = '$idjurnal'
					       AND koderek = '$origrekkas'
					       AND kredit = 0";
                //Logger::LogOnce($sql);
                $db->QueryDb($sql);
            }

            $sql = "UPDATE jbsfina.jurnaldetail
                       SET kredit = '$jumlah'
                     WHERE idjurnal = '$idjurnal'
                       AND koderek = '$rekpendapatan'
                       AND debet = 0";
            //Logger::LogOnce($sql);
            $db->QueryDb($sql);

            if ($paymentExist)
            {
                $sql = "UPDATE jbsfina.tabungan
                           SET alasan = '$alasan',
                               debet = '$jumlah',
                               kredit = '0'
                         WHERE replid = $idTabungan";
                //Logger::LogOnce($sql);
                $db->QueryDb($sql);

                $sql = "UPDATE jbsfina.jurnaldetail
                           SET debet = '0', kredit = '$jumlah'
                         WHERE idjurnal = '$idJurnalTabungan'
                           AND koderek = '$rekKasTab'";
                //Logger::LogOnce($sql);
                $db->QueryDb($sql);

                $sql = "UPDATE jbsfina.jurnaldetail
                           SET debet = '$jumlah', kredit = '0'
                         WHERE idjurnal = '$idJurnalTabungan'
                           AND koderek = '$rekUtangTab'";
                //Logger::LogOnce($sql);
                $db->QueryDb($sql);

                $sql = "UPDATE jbsfina.paymenttrans
                           SET jumlah = '$jumlah'
                         WHERE replid = $idPayment";
                //Logger::LogOnce($sql);
                $db->QueryDb($sql);
            }

            $db->CommitTrans();
            //$db->RollbackTrans();
        }

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kj1xs")]);
    }
    finally
    {
        $db->Close();
    }
}
?>
