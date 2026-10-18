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
function ShowSelectDepartemen_TB($db)
{
    global $departemen;

    try
    {
        echo "<select name='departemen' id='departemen' onChange='change_dep()' class='inputbox' style='width:200px'>";
        $dep = getDepartemen($db, getAccess());
        foreach($dep as $value)
        {
            if ($departemen == "") $departemen = $value;
            $sel = $departemen == $value ? "selected" : "";
            echo "<option value='$value' $sel>$value</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "ktza4");
    }
}

function ShowSelectRetainedEarning()
{
    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT kode, nama FROM rekakun WHERE kategori='MODAL' ORDER BY kode";
        $res = $db->QueryDb($sql);

        echo "<select name='rekre' id='rekre' class='inputbox' style='width:200px'>";
        while ($row = mysqli_fetch_row($res))
        {
            echo "<option value='$row[0]'>$row[0] $row[1]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();

        echo Msg::InfoError($ex->getMessage(), "ktza4");
    }
    finally
    {
        $db->Close();
    }
}


function ProsesTutupBuku1()
{
    $db = new Db();
    try
    {
        $db->Open();

        $dept = $_REQUEST['departemen'];
        $ttutup = $_REQUEST['ttutup'];

        $sql = "SELECT COUNT(replid) 
                  FROM jbsfina.tahunbuku 
                 WHERE aktif = 1 
                   AND departemen = '$dept'";
        $n = $db->ExecuteScalar($sql, 0);
        if ($n == 0)
        {
            $errmsg = "Belum ada tahun buku untuk departemen $dept!<br>Tentukan terlebih dahulu tahun buku awal di menu Tahun Buku";
            return ([-1, $errmsg]);
        }

        $sql = "SELECT replid, tanggalmulai 
                  FROM jbsfina.tahunbuku 
                 WHERE aktif = 1 
                   AND departemen = '$dept'";
        $row = $db->FetchSingleRow($sql);
        $idtahunbuku = $row[0];
        $tanggal1 = $row[1];
        $tanggal2 = MySqlDateFormat($ttutup);

        $sql = "SELECT SUM(jd.debet - jd.kredit) 
				  FROM jbsfina.jurnal j, jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
				 WHERE j.replid = jd.idjurnal AND jd.koderek = ra.kode AND j.idtahunbuku = $idtahunbuku 
				   AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
				   AND ra.kategori IN ('HARTA', 'PIUTANG', 'INVENTARIS')";
        $aktiva = (float) $db->FetchSingle($sql, 0);

        $sql = "SELECT SUM(jd.kredit - jd.debet) 
				  FROM jbsfina.jurnal j, jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
				 WHERE j.replid = jd.idjurnal AND jd.koderek = ra.kode AND j.idtahunbuku = $idtahunbuku 
				   AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' AND ra.kategori IN ('UTANG', 'PENDAPATAN', 'MODAL')";
        $pasiva = (float) $db->FetchSingle($sql, 0);

        $sql = "SELECT SUM(jd.debet - jd.kredit) 
				  FROM jbsfina.rekakun ra, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
				 WHERE jd.idjurnal = j.replid AND jd.koderek = ra.kode AND j.idtahunbuku = $idtahunbuku 
				   AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' AND ra.kategori = 'BIAYA'";
        $pasiva = $pasiva - (float) $db->FetchSingle($sql, 0);

        //$aktiva = 100;
        if ($aktiva != $pasiva)
        {
            $errmsg = "Laporan neraca tidak seimbang! Anda perlu memeriksa kembali data-data transaksi agar laporan neraca menjadi seimbang";
            return json_encode([-1, $errmsg]);
        }

        $_SESSION["TBSTEP"] = 2;
        $_SESSION["TBDEPT"] = $dept;
        $_SESSION["TBTTUTUP"] = $ttutup;

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "ktza4") ]);
    }
    finally
    {
        $db->Close();
    }
}

function ProsesTutupBuku2()
{
    $db = new Db();
    try
    {
        $db->Open();

        $dept = $_REQUEST['departemen'];
        $ttutup = $_REQUEST['ttutup'];
        $tahunbuku = SafeInput($_REQUEST['tahunbuku']);
        $tawal = $_REQUEST['tawal'];
        $awalan = SafeInput($_REQUEST['awalan']);
        $rekre = $_REQUEST['rekre'];
        $keterangan = SafeInput($_REQUEST['keterangan']);
        $idpetugas = getIdUser();
        $petugas = getUserName();

        $sql = "SELECT replid, tanggalmulai 
                  FROM jbsfina.tahunbuku 
                 WHERE aktif = 1 
                   AND departemen = '$dept'";
        //Logger::LogOnce($sql);
        $row = $db->FetchSingleRow($sql);
        if ($row == null)
        {
            return json_encode([-1, "Tidak ditemukan data tahun buku /kxx8j"]);
        }

        $idtahunbuku = $row[0];
        $tanggal1 = $row[1];
        $tanggal2 = $ttutup;
        //$tanggal2 = MySqlDateFormat($ttutup);

        $sql = "SELECT COUNT(replid) 
                  FROM jbsfina.tahunbuku 
                 WHERE tahunbuku = '$tahunbuku' 
                   AND departemen = '$dept'";
        //Logger::LogOnce($sql);
        $n = $db->FetchSingle($sql, 0);
        if ($n > 0)
        {
            $errmsg = "Nama tahun buku '$tahunbuku' sudah dipakai sebelumnya di departemen $dept! Gunakan nama tahun buku lainnya";
            return json_encode([-1, $errmsg]);
        }

        $sql = "SELECT COUNT(replid) 
                  FROM jbsfina.tahunbuku 
                 WHERE awalan = '$awalan' 
                   AND departemen = '$dept'";
        //Logger::LogOnce($sql);
        $n = $db->FetchSingle($sql, 0);
        if ($n > 0)
        {
            $errmsg = "Kode awalan '$awalan' sudah dipakai sebelumnya di departemen $dept! Gunakan kode awalan lainnya";
            return json_encode([-1, $errmsg]);
        }

        $n_aktiva = 0;
        $n_pasiva = 0;

        // 2024-06-26
        $aktiva = array();

        $sql = "SELECT jd.koderek, SUM(jd.debet - jd.kredit) 
		 		  FROM jbsfina.jurnal j, jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
				 WHERE j.replid = jd.idjurnal AND jd.koderek = ra.kode AND j.idtahunbuku = '$idtahunbuku' 
				   AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' AND ra.kategori IN ('HARTA', 'PIUTANG', 'INVENTARIS') 
				 GROUP BY jd.koderek, ra.nama ORDER BY jd.koderek";
        //Logger::LogOnce($sql);
        $result = $db->QueryDb($sql);
        while($row = mysqli_fetch_row($result))
        {
            $aktiva[$n_aktiva]["kode"] = $row[0];
            $aktiva[$n_aktiva]["jumlah"] = (float)$row[1];
            $n_aktiva++;
        }

        // 2024-06-26
        $pasiva = array();

        $sql = "SELECT jd.koderek, sum(jd.kredit - jd.debet) 
				  FROM jbsfina.jurnal j, jbsfina.jurnaldetail jd, jbsfina.rekakun ra 
				 WHERE j.replid = jd.idjurnal AND jd.koderek = ra.kode AND j.idtahunbuku = '$idtahunbuku' 
				   AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' AND ra.kategori = 'UTANG' 
				 GROUP BY jd.koderek, ra.nama ORDER BY jd.koderek";
        //Logger::LogOnce($sql);
        $result = $db->QueryDb($sql);
        while($row = mysqli_fetch_row($result))
        {
            $pasiva[$n_pasiva]["kode"] = $row[0];
            $pasiva[$n_pasiva]["jumlah"] = (float)$row[1];
            $n_pasiva++;
        }

        $sql = "SELECT SUM(jd.kredit - jd.debet) 
				  FROM jbsfina.rekakun ra, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
				 WHERE jd.idjurnal = j.replid AND jd.koderek = ra.kode AND j.idtahunbuku = '$idtahunbuku' 
				   AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' AND ra.kategori IN ('PENDAPATAN', 'MODAL')";
        //Logger::LogOnce($sql);
        $income = (float) $db->FetchSingle($sql, 0);

        $sql = "SELECT SUM(jd.debet - jd.kredit) 
				  FROM jbsfina.rekakun ra, jbsfina.jurnal j, jbsfina.jurnaldetail jd 
				 WHERE jd.idjurnal = j.replid AND jd.koderek = ra.kode AND j.idtahunbuku = '$idtahunbuku' 
				   AND j.tanggal BETWEEN '$tanggal1' AND '$tanggal2' AND ra.kategori='BIAYA'";
        //Logger::LogOnce($sql);
        $outcome = (float) $db->FetchSingle($sql, 0);

        $re = $income - $outcome;

        $db->BeginTrans();

        $sql = "UPDATE jbsfina.tahunbuku 
                   SET aktif = 0 
                 WHERE departemen = '$dept'";
        //Logger::LogOnce($sql);
        $db->QueryDb($sql);

        // Bikin Tahun Buku Baru
        //$tawal = MySqlDateFormat($tawal);
        $sql = "INSERT INTO jbsfina.tahunbuku 
                   SET tahunbuku='$tahunbuku', tanggalmulai='$tawal', awalan='$awalan', 
					   aktif=1, keterangan='$keterangan', departemen='$dept'";
        //Logger::LogOnce($sql);
        $db->QueryDb($sql);

        // Ambil Id Tahun Buku Baru
        $idtahunbaru = $db->InsertId();

        $cacah = 1; //cacah
        $nokas = $awalan . rpad($cacah, "0", 6); //form nokas

        // Simpan ke jurnal
        $idjurnal = SimpanJurnal2($db, $idtahunbaru, $tawal, "Saldo Awal Tahun Buku $tahunbuku Dept $dept", $nokas, "", $idpetugas, $petugas, "saldoawal");

        // Save Aktiva
        for($i = 0; $i < count($aktiva); $i++)
        {
            $kode = $aktiva[$i]["kode"];
            $jumlah = $aktiva[$i]["jumlah"];

            if ($jumlah > 0)
                SimpanDetailJurnal2($db, $idjurnal, "D", $kode, $jumlah);
            else
                SimpanDetailJurnal2($db, $idjurnal, "K", $kode, abs($jumlah));
        }

        // Save Pasiva
        for($i = 0; $i < count($pasiva); $i++)
        {
            $kode = $pasiva[$i]["kode"];
            $jumlah = $pasiva[$i]["jumlah"];

            if ($jumlah > 0)
                SimpanDetailJurnal2($db, $idjurnal, "K", $kode, $jumlah);
            else
                SimpanDetailJurnal2($db, $idjurnal, "D", $kode, abs($jumlah));
        }

        if ($re > 0)
            SimpanDetailJurnal2($db, $idjurnal, "K", $rekre, $re);
        else
            SimpanDetailJurnal2($db, $idjurnal, "D", $rekre, abs($re));

        $sql = "UPDATE jbsfina.tahunbuku 
                   SET cacah = cacah + 1, info1 = '$idjurnal' 
                 WHERE replid = '$idtahunbaru'";
        //Logger::LogOnce($sql);
        $db->QueryDb($sql);

        $db->CommitTrans();
        //$db->RollbackTrans();

        $_SESSION["TBSTEP"] = 3;

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->RollbackTrans();

        $db->LogLastErrorIfExist();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k0zgq") ]);
    }
    finally
    {
        $db->Close();
    }
}
?>