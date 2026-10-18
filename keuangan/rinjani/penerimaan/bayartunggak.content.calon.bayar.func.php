<?php
function SimpanBaruCsWjbTunggak()
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = $_REQUEST["departemen"];
        $nic = $_REQUEST['nic'];
        $idCalonSiswa = $_REQUEST["idcalonsiswa"];
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
        $sendnotif = RequestData("sendnotif", 0);

        if ($sumberdana == "***")
            $sumberdana = "NULL";
        else
            $sumberdana = "'$sumberdana'";

        if ($jcicilan == "INVALID" || $jdiskon == "INVALID")
            return json_encode([-1, "Data pembayaran tidak sesuai /ky9hw"]);

        $jbayar = $jcicilan - $jdiskon;

        //-- Ambil nama penerimaan -----------------------------------------------
        $sql = "SELECT nama, rekkas, rekpendapatan, rekpiutang, info1 AS rekdiskon, CURDATE() AS tcicilan,
                       departemen, aktif
			      FROM jbsfina.datapenerimaan 
			     WHERE replid = '$idpenerimaan'";
        $row = $db->FetchSingleRow($sql);
        if ($row == null)
            return json_encode([-1, "Tidak ditemukan data penerimaan /kuc2w"]);

        $namapenerimaan = $row[0];
        $rekpendapatan = $row[2];
        $rekpiutang = $row[3];
        $rekdiskon = $row[4];
        $tcicilan = $row[5];
        $dp_departemen = $row[6];
        $dp_aktif = $row[7];

        if ($dp_aktif == 0)
            return json_encode([-1, "Penerimaan $namapenerimaan tidak aktif /kbmmc"]);

        if ($dp_departemen != $departemen)
            return json_encode([-1, "Penerimaan $namapenerimaan - $dp_departemen tidak sesuai dengan departemen $departemen /kcfcc"]);

        //-- Cari tahu besar pembayaran ------------------------------------------
        $idbesarjtt = 0;
        $besarjtt = 0;
        $sql = "SELECT b.replid AS id, b.besar
  		   	      FROM jbsfina.besarjttcalon b
			     WHERE b.idcalon='$idCalonSiswa'
			       AND b.idpenerimaan='$idpenerimaan' 
			       AND b.info2='$idtahunbuku'";
        $row = $db->FetchSingleRow($sql);
        if ($row == null)
            return json_encode([-1, "Tidak ditemukan data besar pembayaran /kun32"]);

        $idbesarjtt = $row[0];
        $besarjtt = $row[1];

        // -- Cari tahu jumlah pembayaran cicilan dan diskon yang sudah terjadi -------------------
        $sql = "SELECT SUM(jumlah), SUM(info1) 
                  FROM jbsfina.penerimaanjttcalon 
                 WHERE idbesarjttcalon='$idbesarjtt'";
        $row = $db->FetchSingleRow($sql);
        $totalcicilan = $row[0];
        $totaldiskon = $row[1];

        // -- Cek jumlah cicilan dengan besar pembayaran yang mesti dilunasi --
        $ketjurnal = "";
        $lunas = 0;
        if ($totalcicilan + $totaldiskon + $jbayar + $jdiskon > $besarjtt)
        {
            $errmsg = "Maaf, pembayaran tidak dapat dilakukan! Jumlah bayaran cicilan lebih besar daripada pembayaran yang harus dilunasi! /k262a";
            return json_encode([-1, $errmsg]);
        }

        $lunas = 0;
        $ketsms = "";
        $ketjurnal = "";
        if ($totalcicilan + $totaldiskon + $jbayar + $jdiskon == $besarjtt)
        {
            $ketsms = "pelunasan $namapenerimaan";
            $ketjurnal = "Pelunasan $namapenerimaan calon siswa $namasiswa ($nic)";
            $lunas = 1; //udah lunas
        }
        else
        {
            $sql = "SELECT COUNT(replid) + 1 
                      FROM jbsfina.penerimaanjttcalon 
                     WHERE idbesarjttcalon = '$idbesarjtt'";
            $cicilan = $db->FetchSingle($sql, 0);

            $ketsms = "pembayaran ke-$cicilan $namapenerimaan";
            $ketjurnal = "Pembayaran ke-$cicilan $namapenerimaan calon siswa $namasiswa ($nic)";
            $lunas = 0;
        }

        // -- Ambil awalan dan cacah tahunbuku untuk bikin nokas -------------
        $sql = "SELECT replid, awalan, cacah 
                  FROM jbsfina.tahunbuku 
                 WHERE departemen = '$departemen'
                   AND aktif = 1";
        $row = $db->FetchSingleRow($sql);
        if ($row == null)
            return json_encode([-1, "Tidak ditemukan data tahun buku /kbfh5"]);

        $idtahunbuku_aktif = $row[0];
        $awalan = $row[1];
        $cacah = $row[2];
        $cacah += 1; //increment cacah
        $nokas = $awalan . rpad($cacah, "0", 6); //form nokas

        $db->BeginTrans();

        $idjurnal = SimpanJurnal2($db, $idtahunbuku_aktif, $tcicilan, $ketjurnal, $nokas, "", $idpetugas, $petugas, "penerimaanjttcalon");

        SimpanDetailJurnal2($db, $idjurnal, "D", $rekkas, $jbayar);

        SimpanDetailJurnal2($db, $idjurnal, "K", $rekpiutang, $jcicilan);

        if ($jdiskon > 0)
            SimpanDetailJurnal2($db, $idjurnal, "D", $rekdiskon, $jdiskon);

        // -- increment cacah di tahunbuku -----------------------------------
        $sql = "UPDATE jbsfina.tahunbuku 
                   SET cacah = cacah + 1 
                 WHERE replid = '$idtahunbuku_aktif'";
        $db->QueryDb($sql);

        // -- simpan data cicilan di penerimaanjtt ---------------------------
        $sql = "INSERT INTO jbsfina.penerimaanjttcalon 
                   SET idbesarjttcalon='$idbesarjtt', idjurnal='$idjurnal', tanggal='$tcicilan', 
				       jumlah='$jbayar', keterangan='$kcicilan', petugas='$petugas', info1='$jdiskon',
				       sumberdana=$sumberdana";
        $db->QueryDb($sql);

        // -- jika lunas ubah statusnya di besarjtt ----------------------------
        if ($lunas)
        {
            $sql = "SET @DISABLE_TRIGGERS = 1;";
            $db->QueryDb($sql);

            $sql = "UPDATE jbsfina.besarjttcalon 
                       SET lunas=1 
                     WHERE replid='$idbesarjtt'";
            $db->QueryDb($sql);

            $sql = "SET @DISABLE_TRIGGERS = NULL;";
            $db->QueryDb($sql);
        }

        if ($sendnotif == 1)
        {
            CreateSMSPaymentInfo2($db, 'CSISPAY',
                $departemen, $nic, $namasiswa,
                RegularDateFormat($tcicilan),
                FormatRupiah($jbayar),
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

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kfgb8")]);
    }
    finally
    {
        $db->Close();
    }
}

function SimpanEditCsWjbTunggak()
{
    $db = new Db();
    try
    {
        $db->Open();

        $nic = $_REQUEST['nic'];
        $idCalonSiswa = $_REQUEST["idcalonsiswa"];
        $namaCalon = $_REQUEST['nama'];
        $idpembayaran = $_REQUEST['idpembayaran'];

        //$jcicilan = (int)UnformatRupiah($_REQUEST['jcicilan']);
        //$jdiskon = (int)UnformatRupiah($_REQUEST['jdiskon']);
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
        $sql = "SELECT b.besar, b.lunas, p.idbesarjttcalon, p.idjurnal, p.jumlah, date_format(p.tanggal, '%d-%m-%Y') as tanggal, 
        	           p.keterangan, pn.nama as namapenerimaan, pn.rekkas, pn.rekpendapatan, pn.rekpiutang, pn.info1 AS rekdiskon,
			           p.info1 AS diskon, pn.replid AS idpenerimaan, CURDATE() AS tcicilan 
		          FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsfina.datapenerimaan pn 
		         WHERE p.replid = '$idpembayaran' 
		           AND p.idbesarjttcalon = b.replid 
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
        $idbesarjttcalon = $row['idbesarjttcalon'];
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

        if ($jbayar == $besar && $jdiskon == $besardiskon)
        {
            //--------------------------------------------------------------
            // Hanya mengubah informasi pembayaran tanpa mengubah besarnya
            // -------------------------------------------------------------

            $db->BeginTrans();

            $sql = "UPDATE jbsfina.penerimaanjttcalon
				       SET tanggal = '$tcicilan', keterangan = '$kcicilan', alasan = '$alasan',
    					   petugas = '$petugas', sumberdana = $sumberdana
				     WHERE replid = $idpembayaran";
            $db->QueryDb($sql);

            // Ambil kode rekening dari jurnal bukan dari datapenerimaan
            $rekkas = AmbilKodeRekJurnal2($db, $idjurnal, "HARTA", $idpenerimaan);
            if ($rekkas != $selrekkas)
            {
                $sql = "UPDATE jbsfina.jurnaldetail 
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
                      FROM jbsfina.penerimaanjttcalon 
                     WHERE idbesarjttcalon = '$idbesarjttcalon' 
                       AND replid <> '$idpembayaran'";
            $row = $db->FetchSingleRow($sql);
            if ($row == null)
                return json_encode([-1, "Tidak ditemukan data pembayaran /kywvt"]);
            $totalcicilan = $row[0];
            $totaldiskon = $row[1];

            $errmsg = "";
            if ($totalcicilan + $totaldiskon + $jbayar + $jdiskon > $besarjtt)
            {
                $errmsg = "Maaf, pembayaran tidak dapat dilakukan! Jumlah pembayaran cicilan lebih besar daripada bayaran yang harus dilunasi";
            }

            if ($errmsg != "")
                return json_encode([-1, $errmsg]);

            $lunas = 0;
            $ketjurnal = "";
            if ($totalcicilan + $totaldiskon + $jbayar + $jdiskon == $besarjtt)
            {
                $ketjurnal = "Pelunasan $namapenerimaan siswa $namaCalon ($nic)";
                $lunas = 1;
            }
            else
            {
                $cicilan = 0;
                $sql = "SELECT replid 
                          FROM jbsfina.penerimaanjttcalon 
                         WHERE idbesarjttcalon = '$idbesarjttcalon' 
                         ORDER BY tanggal, replid ASC";
                $res = $db->QueryDb($sql);
                while($row = mysqli_fetch_row($res))
                {
                    $cicilan++;
                    if ($row[0] == $idpembayaran)
                        break;
                }
                $ketjurnal = "Pembayaran ke-$cicilan $namapenerimaan siswa $namaCalon ($nic)";
                $lunas = 0;
            }

            // Ambil kode rekening dari jurnal bukan dari datapenerimaan
            $rekkas = AmbilKodeRekJurnal2($db, $idjurnal, "HARTA", $idpenerimaan);
            $rekpiutang = AmbilKodeRekJurnal2($db, $idjurnal, "PIUTANG", $idpenerimaan);
            $rekdiskon = AmbilKodeRekJurnal2($db, $idjurnal, "DISKON", $idpenerimaan);

            $db->BeginTrans();

            $sql = "UPDATE jbsfina.penerimaanjttcalon 
                       SET jumlah='$jbayar', keterangan='$kcicilan', tanggal='$tcicilan', 
			               alasan='$alasan', petugas='$petugas', info1='$jdiskon',
			               sumberdana = $sumberdana
			         WHERE replid='$idpembayaran'";
            $db->QueryDb($sql);

            $sql = "SELECT idjurnal FROM jbsfina.penerimaanjttcalon WHERE replid = '$idpembayaran'";
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

            $sql = "UPDATE jbsfina.besarjttcalon SET lunas=$lunas WHERE replid='$idbesarjttcalon'";
            $db->QueryDb($sql);

            $sql = "SET @DISABLE_TRIGGERS = NULL;";
            $db->QueryDb($sql);

            $db->CommitTrans();
            //$db->RollbackTrans();

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