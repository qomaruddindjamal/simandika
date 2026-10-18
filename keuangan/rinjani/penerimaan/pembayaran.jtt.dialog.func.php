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
require_once ("../library/msg.php");

function LoadValues($db, $idBesarJtt)
{
    global $besar, $keterangan, $cicilan, $idJurnal, $lunas;

    try
    {
        $sql = "SELECT b.besar, b.keterangan, b.lunas, b.info1 AS idjurnal, cicilan
	              FROM jbsfina.besarjtt b
		         WHERE b.replid = $idBesarJtt";
        $res = $db->ExecuteReader($sql);
        if ($row = mysqli_fetch_row($res))
        {
            $besar = $row[0];
            $keterangan = $row[1];
            $lunas = $row[2];
            $idJurnal = $row[3];
            $cicilan = $row[4];
        }
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k0ug7");
    }
}

function SimpanBesarJtt()
{
    $db = new Db();
    try
    {
        $db->Open();

        $pengguna = getUserName();
        $idpenerimaan = $_REQUEST["idpenerimaan"];
        $idbesarjtt = $_REQUEST['idbesarjtt'];
        $besar = $_REQUEST['besar'];
        $cicilan = $_REQUEST['cicilan'];
        $nis = $_REQUEST['nis'];
        $namasiswa = $_REQUEST["nama"];
        $idtahunbuku = $_REQUEST["idtahunbuku"];
        $keterangan = RequestData("keterangan", "");
        $alasan = RequestData("alasan", "");
        $cicilanpertama = RequestData("cicilanpertama", 0);

        // Ambil informasi kode rekening berdasarkan jenis penerimaan
        $sql = "SELECT rekkas, rekpiutang, rekpendapatan, nama 
                  FROM jbsfina.datapenerimaan 
                 WHERE replid='$idpenerimaan'";
        //Logger::LogOnce($sql);
        $row = $db->FetchSingleRow($sql);
        if ($row == null)
            return json_encode([-1, "Tidak ditemukan data penerimaan /kp5cd"]);

        $rekkas = $row[0];
        $rekpiutang = $row[1];
        $rekpendapatan = $row[2];
        $namapenerimaan = $row[3];

        if ($idbesarjtt > 0)
        {
            // Ubah besar pembayaran
            // cari tahu total pembayaran yang telah dilakukan
            $sql = "SELECT sum(jumlah), count(replid) 
                      FROM jbsfina.penerimaanjtt 
                     WHERE idbesarjtt = '$idbesarjtt'";
            //Logger::LogOnce($sql);
            $row = $db->FetchSingleRow($sql);
            if ($row == null)
                return json_encode([-1, "Tidak ditemukan data penerimaan /kfb6u"]);

            $totalbayaran = (float)$row[0];
            $nbayaran = (int)$row[1];
            if ($totalbayaran > $besar)
            {
                // total pembayaran yang dilakukan lebih besar dari besar pembayaran yang diinput
                $errmsg = "Maaf, besar pembayaran yang harus dilunasi lebih kecil dari jumlah pembayaran cicilan yang telah dilakukan! /kvz3q";
                return json_encode([-1, $errmsg]);
            }

            $sql = "SELECT info1, info2, besar 
                      FROM jbsfina.besarjtt 
                     WHERE replid = '$idbesarjtt'";
            //Logger::LogOnce($sql);
            $row = $db->FetchSingleRow($sql);
            if ($row == null)
                return json_encode([-1, "Tidak ditemukan data besar pembayaran /knjhd"]);

            $idjurnal_jtt = (int)$row[0];
            $idtahunbuku_jtt = (int)$row[1];
            $besar_jtt = (float)$row[2];
            $selisih = $besar - $besar_jtt;

            if ($selisih == 0)
            {
                // hanya update keterangan atau cicilan
                $sql = "UPDATE jbsfina.besarjtt 
                           SET cicilan = '$cicilan', keterangan = '$keterangan', 
                               pengguna = '$pengguna', info3 = '$alasan' 
                         WHERE replid = '$idbesarjtt'";
                //Logger::LogOnce($sql);
                $db->QueryDb($sql);
            }
            else
            {
                $db->BeginTrans();

                $lunas = 0; // belum lunas
                if ($besar == 0)
                    $lunas = 2; // gratis
                else if ($totalbayaran == $besar)
                    $lunas = 1;  // lunas

                // update besarjtt
                $sql = "UPDATE jbsfina.besarjtt 
                           SET besar = '$besar', cicilan = '$cicilan', keterangan = '$keterangan', 
                               lunas = '$lunas', pengguna = '$pengguna', info3 = '$alasan' 
                         WHERE replid = '$idbesarjtt'";
                //Logger::LogOnce($sql);
                $db->QueryDb($sql);

                $sql = "UPDATE jbsfina.jurnaldetail 
                           SET debet = '$besar' 
                         WHERE idjurnal = '$idjurnal_jtt' 
                           AND koderek = '$rekpiutang' 
                           AND debet = $besar_jtt";
                //Logger::LogOnce($sql);
                $db->QueryDb($sql);

                $sql = "UPDATE jbsfina.jurnaldetail 
                           SET kredit = '$besar' 
                         WHERE idjurnal = '$idjurnal_jtt' 
                           AND koderek = '$rekpendapatan' 
                           AND kredit = $besar_jtt";
                //Logger::LogOnce($sql);
                $db->QueryDb($sql);

                $db->CommitTrans();
                //$db->RollbackTrans();
            }
        }
        else
        {
            // Pendataan Baru Besar Jtt

            //Ambil awalan dan cacah tahunbuku untuk bikin nokas;
            $sql = "SELECT awalan, cacah 
                      FROM jbsfina.tahunbuku 
                     WHERE replid = '$idtahunbuku'";
            //Logger::LogOnce($sql);
            $row = $db->FetchSingleRow($sql);
            if ($row == null)
                return json_encode([-1, "Tidak ditemukan data tahun buku /k1t1r"]);
            $awalan = $row[0];
            $cacah = $row[1];

            $cacah += 1; // Increment cacah
            $nokas = $awalan . rpad($cacah, "0", 6); // Form nomor kas

            // tanggal & petugas pendata & keterangan
            $tcicilan = date("Y-m-d");
            $idpetugas = getIdUser();
            $petugas = getUserName();
            $infoJurnal = "Pendataan besar pembayaran $namapenerimaan siswa $namasiswa ($nis)";

            // status lunas
            $lunas = 0; // belum lunas
            if ($besar == 0)
                $lunas = 2; // GRATIS

            $db->BeginTrans();

            $idjurnal = SimpanJurnal2($db, $idtahunbuku, $tcicilan, $infoJurnal, $nokas, "", $idpetugas, $petugas, "penerimaanjtt");

            $sql = "INSERT INTO jbsfina.besarjtt 
                       SET nis = '$nis', idpenerimaan = '$idpenerimaan', besar = '$besar', cicilan = '$cicilan', 
                           keterangan = '$keterangan', lunas = $lunas, pengguna = '$pengguna', info1 = '$idjurnal', 
                           info2 = '$idtahunbuku'";
            $db->QueryDb($sql);

            $idbesarjtt = $db->InsertId();

            SimpanDetailJurnal2($db, $idjurnal, "D", $rekpiutang, $besar);

            SimpanDetailJurnal2($db, $idjurnal, "K", $rekpendapatan, $besar);

            $sql = "UPDATE jbsfina.tahunbuku 
                       SET cacah = cacah + 1 
                     WHERE replid = $idtahunbuku";
            //Logger::LogOnce($sql);
            $db->QueryDb($sql);

            if ($cicilanpertama == 1)
            {
                // SET cicilan pertama = 0

                // SET CICILAN PERTAMA Rp 0
                $infoJurnal = "Pendataan besar pembayaran $namapenerimaan siswa $namasiswa ($nis)";

                // -- Ambil awalan untuk bikin nokas -------------
                $cacah += 1; //increment cacah
                $nokas = $awalan . rpad($cacah, "0", 6); //form nokas

                // -- Simpan ke jurnal -----------------------------------------------
                $idjurnal = SimpanJurnal2($db, $idtahunbuku, $tcicilan, $infoJurnal, $nokas, "", $idpetugas, $petugas, "penerimaanjtt");

                //-- Simpan ke jurnaldetail ------------------------------------------
                SimpanDetailJurnal2($db, $idjurnal, "D", $rekkas, 0);
                SimpanDetailJurnal2($db, $idjurnal, "K", $rekpiutang, 0);

                // -- simpan data cicilan di penerimaanjtt ---------------------------
                $sql = "INSERT INTO jbsfina.penerimaanjtt 
                           SET idbesarjtt = '$idbesarjtt', idjurnal = '$idjurnal', tanggal = '$tcicilan', 
				               jumlah = '0', keterangan='', petugas = '$petugas', info1='0'";
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

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k09vw")]);
    }
    finally
    {
        $db->Close();
    }
}
?>