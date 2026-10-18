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
                  FROM jbsfina.penerimaaniurancalon p, jbsfina.jurnal j, jbsfina.jurnaldetail jd, rekakun rk
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
        echo Msg::InfoError($ex->getMessage(), "kxwhs");
    }

}

function ShowSelectSumberDanaCsSkr($db)
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
        echo Msg::InfoError($ex->getMessage(), "kuyw1");
    }
}

function ShowSelectRekKasCsSkr($db)
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
        echo Msg::InfoError($ex->getMessage(), "kp0xq");
    }
}

function SimpanBaruCsSkr()
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = RequestData("departemen", "");
        $nic = RequestData("nic", "");
        $idCalonSiswa = RequestData("idcalonsiswa", 0);
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
        $ketjurnal = "Pembayaran $penerimaan calon siswa $namasiswa ($nic)";

        $idjurnal = SimpanJurnal2($db, $idtahunbuku, $tanggal, $ketjurnal, $nokas, "", $idpetugas, $petugas, "penerimaaniurancalon");

        SimpanDetailJurnal2($db, $idjurnal, "D", $rekkas, $jumlah);

        SimpanDetailJurnal2($db, $idjurnal, "K", $rekpendapatan, $jumlah);

        // -- increment cacah di tahunbuku -----------------------------------
        $sql = "UPDATE jbsfina.tahunbuku 
                   SET cacah=cacah+1 
                 WHERE replid='$idtahunbuku'";
        //Logger::LogOnce($sql);
        $db->QueryDb($sql);

        // -- simpan data cicilan di penerimaanjtt ---------------------------
        $sql = "INSERT INTO jbsfina.penerimaaniurancalon
				   SET idpenerimaan='$idpenerimaan', idcalon='$idCalonSiswa', idjurnal='$idjurnal', jumlah='$jumlah',
					   tanggal = CURDATE(), keterangan = '$keterangan', petugas='$petugas', sumberdana=$sumberdana";
        //Logger::LogOnce($sql);
        $db->QueryDb($sql);

        if ($sendnotif == 1)
        {
            $tanggal = date("Y-m-d");
            CreateSMSPaymentInfo2($db, 'CSISPAY',
                $departemen, $nic, $namasiswa,
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

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kwb3a")]);
    }
    finally
    {
        $db->Close();
    }
}

function SimpanEditCsSkr()
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = RequestData("departemen", "");
        $nic = RequestData("nic", "");
        $idCalonSiswa = RequestData("idcalonsiswa", 0);
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
            return json_encode([-1, "Data pembayaran tidak sesuai /kn4j3"]);

        $db->BeginTrans();

        if ($jumlah == $origjumlah)
        {
            //--------------------------------------------------------------
            // Hanya mengubah informasi pembayaran tanpa mengubah besarnya
            // -------------------------------------------------------------
            $sql = "UPDATE jbsfina.penerimaaniurancalon
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
            $sql = "UPDATE jbsfina.penerimaaniurancalon
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

            $db->CommitTrans();
            //$db->RollbackTrans();
        }

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kevnu")]);
    }
    finally
    {
        $db->Close();
    }
}
?>