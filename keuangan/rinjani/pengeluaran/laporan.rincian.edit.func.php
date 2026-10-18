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
function SimpanEdit()
{
    $db = new Db();
    try
    {
        $db->Open();

        /*
         * qsb.add("op", "simpan");
    qsb.add("jumlah", jumlah);
    qsb.addInput("idtransaksi", "idtransaksi");
    qsb.addInput("idjurnal", "idjurnal");
    qsb.addInput("keperluan", "keperluan");
    qsb.addInput("rekkas", "rekkas");
    qsb.addInput("pengguna", "pengguna");
    qsb.addInput("penerima", "penerima");
    qsb.addInput("keterangan", "keterangan");
    qsb.addInput("alasan", "alasan");
         */

        $jumlah = RequestData("jumlah", 0);
        $idtransaksi = RequestData("idtransaksi", 0);
        $idjurnal = RequestData("idjurnal", 0);
        $keperluan = RequestData("keperluan", "");
        $rekkas = RequestData("rekkas", "");
        $rekbeban = RequestData("rekbeban", "");
        $tanggal = RequestData("tanggal", date('Y-m-d'));
        $pengguna = RequestData("pengguna", "");
        $penerima = RequestData("penerima", "");
        $keterangan = RequestData("keterangan", "");
        $alasan = RequestData("alasan", "");
        $petugas = getUserName();

        $db->BeginTrans();
        $sql = "UPDATE jbsfina.pengeluaran 
			       SET tanggal='$tanggal', jumlah='$jumlah', 
				       keperluan='$keperluan', keterangan='$keterangan', petugas='$petugas', penerima='$penerima', 
				       namapemohon='$pengguna', alasan='$alasan' 
		         WHERE replid='$idtransaksi'";
        //Logger::LogOnce($sql);
        $db->ExecuteNonQuery($sql);

        $sql = "UPDATE jbsfina.jurnal 
                   SET transaksi='$keperluan' 
                 WHERE replid='$idjurnal'";
        //Logger::LogOnce($sql);
        $db->ExecuteNonQuery($sql);

        $sql = "DELETE FROM jbsfina.jurnaldetail 
                 WHERE idjurnal = '$idjurnal'";
        //Logger::LogOnce($sql);
        $db->ExecuteNonQuery($sql);

        SimpanDetailJurnal2($db, $idjurnal, "D", $rekbeban, $jumlah);
        SimpanDetailJurnal2($db, $idjurnal, "K", $rekkas, $jumlah);

        //$db->RollbackTrans();
        $db->CommitTrans();

        echo json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        $db->RollbackTrans();

        echo json_encode([-99, Msg::InfoError($ex->getMessage(), "")]);
    }
    finally
    {
        $db->Close();
    }
}
?>
