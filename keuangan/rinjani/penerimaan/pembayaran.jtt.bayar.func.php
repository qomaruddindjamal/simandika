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
function ShowSelectRekKasJtt($db)
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
        echo Msg::InfoError($ex->getMessage(), "krt72");
    }
}

function ShowSelectSumberDanaJtt($db)
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

function SimpanBaruJtt()
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = $_REQUEST["departemen"];
        $nis = $_REQUEST['nis'];
        $namasiswa = $_REQUEST["nama"];
        $idkategori = $_REQUEST['idkategori'];
        $idpenerimaan = $_REQUEST['idpenerimaan'];
        $idtahunbuku = $_REQUEST['idtahunbuku'];

        $idpetugas = getIdUser();
        $petugas = getUserName();
        $idbesarjtt = RequestData('idbesarjtt', '');
        $jcicilan = RequestData('jcicilan', 'INVALID');
        $jdiskon = RequestData('jdiskon', 'INVALID');
        $kcicilan = RequestData('kcicilan', "");
        $rekkas =  RequestData('rekkas', '');
        $sumberdana =  RequestData('sumberdana', '***');
        $sendnotif = RequestData('sendnotif', 0);

        if ($sumberdana == "***")
            $sumberdana = "NULL";
        else
            $sumberdana = "'$sumberdana'";

        if ($jcicilan == "INVALID" || $jdiskon == "INVALID")
            return json_encode([-1, "Data pembayaran tidak sesuai /keqsf"]);

        $jbayar = $jcicilan - $jdiskon;

        //-- Ambil nama penerimaan -----------------------------------------------
        $sql = "SELECT nama, rekkas, rekpendapatan, rekpiutang, info1 AS rekdiskon, CURDATE() AS tcicilan,
                       departemen, aktif
			      FROM jbsfina.datapenerimaan 
			     WHERE replid = '$idpenerimaan'";
        $row = $db->FetchSingleRow($sql);
        if ($row == null)
            return json_encode([-1, "Tidak ditemukan data penerimaan /keqsf"]);

        $namapenerimaan = $row[0];
        $rekpendapatan = $row[2];
        $rekpiutang = $row[3];
        $rekdiskon = $row[4];
        $tcicilan = $row[5];
        $dp_departemen = $row[6];
        $dp_aktif = $row[7];

        if ($dp_aktif == 0)
            return json_encode([-1, "Penerimaan $namapenerimaan tidak aktif /kwra4"]);

        if ($dp_departemen != $departemen)
            return json_encode([-1, "Penerimaan $namapenerimaan - $dp_departemen tidak sesuai dengan departemen $departemen /k0kw7"]);

        //-- Cari tahu besar pembayaran ------------------------------------------
        $idbesarjtt = 0;
        $besarjtt = 0;
        $sql = "SELECT b.replid AS id, b.besar
  		   	      FROM jbsfina.besarjtt b
			     WHERE b.nis='$nis' AND b.idpenerimaan='$idpenerimaan' AND b.info2='$idtahunbuku'";
        $row = $db->FetchSingleRow($sql);
        if ($row == null)
            return json_encode([-1, "Tidak ditemukan data besar pembayaran /k66e9"]);

        $idbesarjtt = $row[0];
        $besarjtt = $row[1];

        // -- Cari tahu jumlah pembayaran cicilan dan diskon yang sudah terjadi -------------------
        $sql = "SELECT SUM(jumlah), SUM(info1) 
                  FROM jbsfina.penerimaanjtt 
                 WHERE idbesarjtt='$idbesarjtt'";
        $row = $db->FetchSingleRow($sql);
        $totalcicilan = $row[0];
        $totaldiskon = $row[1];

        // -- Cek jumlah cicilan dengan besar pembayaran yang mesti dilunasi --
        $ketjurnal = "";
        $lunas = 0;
        if ($totalcicilan + $totaldiskon + $jbayar + $jdiskon > $besarjtt)
        {
            $errmsg = "Maaf, pembayaran tidak dapat dilakukan! Jumlah bayaran cicilan lebih besar daripada pembayaran yang harus dilunasi! /k1fdf";
            return json_encode([-1, $errmsg]);
        }

        $lunas = 0;
        $ketsms = "";
        $ketjurnal = "";
        if ($totalcicilan + $totaldiskon + $jbayar + $jdiskon == $besarjtt)
        {
            $ketsms = "pelunasan $namapenerimaan";
            $ketjurnal = "Pelunasan $namapenerimaan siswa $namasiswa ($nis)";
            $lunas = 1; //udah lunas
        }
        else
        {
            $sql = "SELECT COUNT(replid) + 1 
                      FROM jbsfina.penerimaanjtt 
                     WHERE idbesarjtt = '$idbesarjtt'";
            $cicilan = $db->FetchSingle($sql, 0);

            $ketsms = "pembayaran ke-$cicilan $namapenerimaan";
            $ketjurnal = "Pembayaran ke-$cicilan $namapenerimaan siswa $namasiswa ($nis)";
            $lunas = 0;
        }

        // -- Ambil awalan dan cacah tahunbuku untuk bikin nokas -------------
        $sql = "SELECT awalan, cacah 
                  FROM jbsfina.tahunbuku 
                 WHERE replid = '$idtahunbuku'";
        $row = $db->FetchSingleRow($sql);
        if ($row == null)
            return json_encode([-1, "Tidak ditemukan data tahun buku /kbfh5"]);

        $awalan = $row[0];
        $cacah = $row[1];
        $cacah += 1; //increment cacah
        $nokas = $awalan . rpad($cacah, "0", 6); //form nokas

        $db->BeginTrans();

        $idjurnal = SimpanJurnal2($db, $idtahunbuku, $tcicilan, $ketjurnal, $nokas, "", $idpetugas, $petugas, "penerimaanjtt");

        SimpanDetailJurnal2($db, $idjurnal, "D", $rekkas, $jbayar);

        SimpanDetailJurnal2($db, $idjurnal, "K", $rekpiutang, $jcicilan);

        if ($jdiskon > 0)
            SimpanDetailJurnal2($db, $idjurnal, "D", $rekdiskon, $jdiskon);

        // -- increment cacah di tahunbuku -----------------------------------
        $sql = "UPDATE jbsfina.tahunbuku 
                   SET cacah=cacah+1 
                 WHERE replid='$idtahunbuku'";
        $db->QueryDb($sql);

        // -- simpan data cicilan di penerimaanjtt ---------------------------
        $sql = "INSERT INTO jbsfina.penerimaanjtt 
                   SET idbesarjtt='$idbesarjtt', idjurnal='$idjurnal', tanggal='$tcicilan', 
				       jumlah='$jbayar', keterangan='$kcicilan', petugas='$petugas', info1='$jdiskon',
				       sumberdana=$sumberdana";
        $db->QueryDb($sql);

        // -- jika lunas ubah statusnya di besarjtt ----------------------------
        if ($lunas)
        {
            $sql = "SET @DISABLE_TRIGGERS = 1;";
            $db->QueryDb($sql);

            $sql = "UPDATE jbsfina.besarjtt 
                       SET lunas=1 
                     WHERE replid='$idbesarjtt'";
            $db->QueryDb($sql);

            $sql = "SET @DISABLE_TRIGGERS = NULL;";
            $db->QueryDb($sql);
        }

        if ($sendnotif == 1)
        {
            CreateSMSPaymentInfo2($db, 'SISPAY',
                $departemen, $nis, $namasiswa,
                RegularDateFormat($tcicilan),
                FormatRupiah($jbayar),
                $ketsms);
        }

        $db->CommitTrans();

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kfgb8")]);
    }
    finally
    {
        $db->Close();
    }
}

function SimpanEditJtt()
{
    $db = new Db();
    try
    {
        $db->Open();

        $nis = $_REQUEST['nis'];
        $namasiswa = $_REQUEST['nama'];
        $idpembayaran = $_REQUEST['idpembayaran'];

        //$jcicilan = (int)UnformatRupiah($_REQUEST['jcicilan']);
        //$jdiskon = (int)UnformatRupiah($_REQUEST['jdiskon']);
        $departemen = RequestData("departemen", "");
        $jcicilan = RequestData('jcicilan', 'INVALID');
        $kcicilan = RequestData('kcicilan', '');
        $jdiskon = RequestData('jdiskon', 'INVALID');
        $alasan = RequestData('alasan', '');
        $selrekkas = RequestData('rekkas', ''); // selected rekening kas
        $sumberdana = RequestData('sumberdana', '***');
        $petugas = getUserName();

        if ($sumberdana == "***")
            $sumberdana = "NULL";
        else
            $sumberdana = "'$sumberdana'";

        if ($jcicilan == "INVALID" || $jdiskon == "INVALID")
            return json_encode([-1, "Data pembayaran tidak sesuai /kyggw"]);

        $jbayar = $jcicilan - $jdiskon;

        // -- ambil data-data pembayaran ---------------------------------
        $sql = "SELECT b.besar, b.lunas, p.idbesarjtt, p.idjurnal, p.jumlah, date_format(p.tanggal, '%d-%m-%Y') as tanggal, 
        	           p.keterangan, pn.nama as namapenerimaan, pn.rekkas, pn.rekpendapatan, pn.rekpiutang, pn.info1 AS rekdiskon,
			           p.info1 AS diskon, pn.replid AS idpenerimaan, CURDATE() AS tcicilan,
			           pn.departemen, pn.aktif 
		          FROM jbsfina.penerimaanjtt p, jbsfina.besarjtt b, jbsfina.datapenerimaan pn 
		         WHERE p.replid = '$idpembayaran' 
		           AND p.idbesarjtt = b.replid 
		           AND b.idpenerimaan = pn.replid";
        $row = $db->FetchSingleArray($sql);
        if ($row == null)
            return json_encode([-1, "Tidak ditemukan data pembayaran /khj8j"]);

        $idjurnal = $row['idjurnal'];
        $tanggal = $row['tanggal'];
        $keterangan = $row['keterangan'];
        $idpenerimaan = $row['idpenerimaan'];
        $namapenerimaan = $row['namapenerimaan'];
        $besar = $row['jumlah'];
        $besardiskon = $row['diskon'];
        $idbesarjtt = $row['idbesarjtt'];
        $besarjtt = $row['besar'];
        $lunas = $row['lunas'];
        $rekkas = $row['rekkas'];
        $rekpiutang = $row['rekpiutang'];
        $rekpendapatan = $row['rekpendapatan'];
        $rekdiskon = $row['rekdiskon'];
        //$jdiskon = $row['diskon'];
        //$jbayar = $besar;
        //$jcicilan = $jbayar + $jdiskon;
        $tcicilan = $row["tcicilan"];
        $dp_departemen = $row["departemen"];
        $dp_aktif = $row["aktif"];

        if ($dp_aktif == 0)
            return json_encode([-1, "Penerimaan $namapenerimaan tidak aktif /k9j52"]);

        if ($dp_departemen != $departemen)
            return json_encode([-1, "Penerimaan $namapenerimaan - $dp_departemen tidak sesuai dengan departemen $departemen /k7mm3"]);

        if ($jbayar == $besar && $jdiskon == $besardiskon)
        {
            //--------------------------------------------------------------
            // Hanya mengubah informasi pembayaran tanpa mengubah besarnya
            // -------------------------------------------------------------

            $db->BeginTrans();

            $sql = "UPDATE jbsfina.penerimaanjtt
				       SET tanggal = '$tcicilan', keterangan = '$kcicilan', alasan = '$alasan',
    					   petugas = '$petugas', sumberdana = $sumberdana
				     WHERE replid = $idpembayaran";
            $db->QueryDb($sql);

            // Ambil kode rekening dari jurnal bukan dari datapenerimaan
            $rekkas = AmbilKodeRekJurnal2($db, $idjurnal, "HARTA", $idpenerimaan);
            if ($rekkas != $selrekkas)
            {
                $sql = "UPDATE jurnaldetail 
					       SET koderek = '$selrekkas'
					     WHERE idjurnal = '$idjurnal'
					       AND koderek = '$rekkas'
					       AND kredit = 0";
                $db->QueryDb($sql);
            }

            $db->CommitTrans();
            //$db->RollbackTrans();

            return json_encode([1, "OK"]);
        }
        else
        {
            //----------------------------
            // Mengubah besar pembayaran
            // ---------------------------

            $sql = "SELECT SUM(jumlah), SUM(info1) 
                      FROM jbsfina.penerimaanjtt 
                     WHERE idbesarjtt = '$idbesarjtt' 
                       AND replid <> '$idpembayaran'";
            $row = $db->FetchSingleRow($sql);
            if ($row == null)
                return json_encode([-1, "Tidak ditemukan data pembayaran /kywvt"]);
            $totalcicilan = $row[0];
            $totaldiskon = $row[1];

            // 2020-10-05 Check SchoolPay Transaction
            $paymentExist = false;
            $sql = "SELECT COUNT(replid)
		              FROM jbsfina.paymenttrans
		             WHERE idpenerimaanjtt = $idpembayaran";
            $res = $db->QueryDb($sql);
            if ($row = mysqli_fetch_row($res))
                $paymentExist = $row[0] > 0;

            $jSaldo = 0;
            $debetAwal = 0;
            $idTabungan = 0;
            $idJurnalTabungan = 0;
            $rekKasTab = "";
            $rekUtangTab = "";
            $idPayment = 0;
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
                         WHERE p.idpenerimaanjtt = $idpembayaran";
                $res = $db->QueryDb($sql);

                $row = mysqli_fetch_array($res);
                $idDataTabungan = $row["iddatatabungan"];
                $idJurnalTabungan = $row["idjurnaltabcust"];
                $idTabungan = $row["idtabungan"];
                $rekKasTab = $row["rekkas"];
                $rekUtangTab = $row["rekutang"];
                $jenisTrans = $row["jenistrans"];
                $idPenerimaanJtt = $row["idpenerimaanjtt"];
                $idPayment = $row["idpayment"];

                // Cek Saldo
                $sql = "SELECT SUM(kredit) - SUM(debet)
                          FROM jbsfina.tabungan
                         WHERE nis = '$nis'
                           AND idtabungan = '$idDataTabungan'";
                $res = $db->QueryDb($sql);
                $row = mysqli_fetch_row($res);
                $jSaldo = $row[0];

                $sql = "SELECT debet
                          FROM jbsfina.tabungan
                         WHERE replid = $idTabungan";
                $res = $db->QueryDb($sql);
                $row = mysqli_fetch_row($res);
                $debetAwal = (int)$row[0];
            }

            $errmsg = "";
            if ($totalcicilan + $totaldiskon + $jbayar + $jdiskon > $besarjtt)
            {
                $errmsg = "Maaf, pembayaran tidak dapat dilakukan! Jumlah pembayaran cicilan lebih besar daripada bayaran yang harus dilunasi";
            }
            else if ($paymentExist)
            {
                if ($jSaldo + $debetAwal < $jbayar + $jdiskon)
                {
                    $errmsg = "Maaf, pembayaran tidak dapat dilakukan! Saldo tabungan tidak mencukupi untuk penarikan!";
                }
            }

            if ($errmsg != "")
                return json_encode([-1, $errmsg]);

            $lunas = 0;
            $ketjurnal = "";
            if ($totalcicilan + $totaldiskon + $jbayar + $jdiskon == $besarjtt)
            {
                $ketjurnal = "Pelunasan $namapenerimaan siswa $namasiswa ($nis)";
                $lunas = 1;
            }
            else
            {
                $cicilan = 0;
                $sql = "SELECT replid 
                          FROM jbsfina.penerimaanjtt 
                         WHERE idbesarjtt = '$idbesarjtt' 
                         ORDER BY tanggal, replid ASC";
                $res = $db->QueryDb($sql);
                while($row = mysqli_fetch_row($res))
                {
                    $cicilan++;
                    if ($row[0] == $idpembayaran)
                        break;
                }
                $ketjurnal = "Pembayaran ke-$cicilan $namapenerimaan siswa $namasiswa ($nis)";
                $lunas = 0;
            }

            // Ambil kode rekening dari jurnal bukan dari datapenerimaan
            $rekkas = AmbilKodeRekJurnal2($db, $idjurnal, "HARTA", $idpenerimaan);
            $rekpiutang = AmbilKodeRekJurnal2($db, $idjurnal, "PIUTANG", $idpenerimaan);
            $rekdiskon = AmbilKodeRekJurnal2($db, $idjurnal, "DISKON", $idpenerimaan);

            $db->BeginTrans();

            $sql = "UPDATE jbsfina.penerimaanjtt 
                       SET jumlah='$jbayar', keterangan='$kcicilan', tanggal='$tcicilan', 
			               alasan='$alasan', petugas='$petugas', info1='$jdiskon',
			               sumberdana = $sumberdana
			         WHERE replid='$idpembayaran'";
            $db->QueryDb($sql);

            $sql = "SELECT idjurnal FROM jbsfina.penerimaanjtt WHERE replid = '$idpembayaran'";
            $idjurnal = $db->FetchSingle($sql, 0);

            $sql = "UPDATE jbsfina.jurnal SET transaksi='$ketjurnal' WHERE replid = '$idjurnal'";
            $db->QueryDb($sql);

            $sql = "UPDATE jbsfina.jurnaldetail SET debet='$jbayar' WHERE idjurnal='$idjurnal' AND koderek='$rekkas' AND kredit=0";
            $db->QueryDb($sql);

            $sql = "UPDATE jbsfina.jurnaldetail SET koderek='$selrekkas' WHERE idjurnal='$idjurnal' AND koderek='$rekkas' AND kredit=0";
            $db->QueryDb($sql);

            $sql = "UPDATE jbsfina.jurnaldetail SET kredit='$jcicilan' WHERE idjurnal='$idjurnal' AND koderek='$rekpiutang' AND debet=0";
            $db->QueryDb($sql);

            $sql = "SELECT COUNT(replid) FROM jbsfina.jurnaldetail WHERE idjurnal='$idjurnal' AND koderek='$rekdiskon'";
            $nJurnalDiskon = $db->FetchSingle($sql, 0);

            if ($nJurnalDiskon == 0 && $jdiskon > 0)
                $sql = "INSERT INTO jbsfina.jurnaldetail SET debet='$jdiskon', idjurnal='$idjurnal', koderek='$rekdiskon', kredit=0";
            else
                $sql = "UPDATE jbsfina.jurnaldetail SET debet='$jdiskon' WHERE idjurnal='$idjurnal' AND koderek='$rekdiskon' AND kredit=0";
            $db->QueryDb($sql);

            $sql = "SET @DISABLE_TRIGGERS = 1;";
            $db->QueryDb($sql);

            $sql = "UPDATE jbsfina.besarjtt SET lunas=$lunas WHERE replid='$idbesarjtt'";
            $db->QueryDb($sql);

            $sql = "SET @DISABLE_TRIGGERS = NULL;";
            $db->QueryDb($sql);

            if ($paymentExist)
            {
                // 2020-10-05 SchoolPay Transaction

                $sql = "UPDATE jbsfina.tabungan
                           SET alasan = '$alasan',
                               debet = '$jbayar',
                               kredit = '0'
                         WHERE replid = $idTabungan";
                $db->QueryDb($sql);

                $sql = "UPDATE jbsfina.jurnaldetail
                           SET debet = '0', kredit = '$jbayar'
                         WHERE idjurnal = '$idJurnalTabungan'
                           AND koderek = '$rekKasTab'";
                $db->QueryDb($sql);

                $sql = "UPDATE jbsfina.jurnaldetail
                           SET debet = '$jbayar', kredit = '0'
                         WHERE idjurnal = '$idJurnalTabungan'
                           AND koderek = '$rekUtangTab'";
                $db->QueryDb($sql);

                $sql = "UPDATE jbsfina.paymenttrans
                           SET jumlah = '$jbayar'
                         WHERE replid = $idPayment";
                $db->QueryDb($sql);
            }

            $db->CommitTrans();

            return json_encode([1, "OK"]);
        }
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kpsm6")]);
    }
    finally
    {
        $db->Close();
    }
}
?>