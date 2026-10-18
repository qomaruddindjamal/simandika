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
function LoadValues($db)
{
    global $idpembayaran;
    global $nis, $namasiswa, $idjurnal, $tanggal, $keterangan, $idtabungan, $namatabungan;
    global $debet, $kredit, $rekkas, $rekutang, $action, $jbayar, $rekkastrans;
    global $sumberdana, $lokasidana;

    // -- ambil data-data pembayaran ---------------------------------
    $sql = "SELECT s.nis, s.nama, p.idjurnal, p.debet, p.kredit, DATE_FORMAT(p.tanggal, '%d-%m-%Y') AS tanggal, 
                   p.keterangan, pn.nama as namatabungan, pn.rekkas, pn.rekutang, pn.replid AS idtabungan,
                   IFNULL(p.sumberdana, '***') AS fsumberdana, IFNULL(p.lokasidana, '***') AS flokasidana
              FROM jbsfina.tabungan p, jbsakad.siswa s, jbsfina.datatabungan pn 
             WHERE p.replid = '$idpembayaran'
               AND p.nis = s.nis
               AND p.idtabungan = pn.replid";
    $row = $db->FetchSingleArray($sql);

    $nis = $row['nis'];
    $namasiswa = $row['nama'];
    $idjurnal = $row['idjurnal'];
    $tanggal = $row['tanggal'];
    $keterangan = $row['keterangan'];
    $idtabungan = $row['idtabungan'];
    $namatabungan = $row['namatabungan'];
    $debet = (int) $row['debet'];
    $kredit = (int) $row['kredit'];
    $rekkas = $row['rekkas'];
    $rekutang = $row['rekutang'];
    $action = ($debet == 0) ? "setor" : "tarik";
    $jbayar = ($debet == 0) ? $kredit : $debet;
    $sumberdana = $row['fsumberdana'];
    $lokasidana = $row['flokasidana'];

    $sql = "SELECT jd.koderek, jd.replid
              FROM jurnaldetail jd, rekakun r
             WHERE jd.koderek = r.kode
               AND r.kategori = 'HARTA'
               AND jd.idjurnal = '$idjurnal'";
    $row = $db->FetchSingleArray($sql);
    $rekkastrans = $row['koderek'];
    $idjurnalrekkas = $row['replid'];
}

function SimpanEditTabungan()
{
    $db = new Db();
    try
    {
        $db->Open();
        $db->BeginTrans();

        $idpembayaran = RequestData("idpembayaran", 0);
        $nis = RequestData("nis", "");
        $idjurnal = RequestData("idjurnal", 0);
        $idtabungan = RequestData("idtabungan", 0);
        $debet = RequestData("debet", 0);
        $kredit = RequestData("kredit", 0);
        $action = RequestData("action", 0);
        $jbayar = RequestData("jbayar", 0);
        $keterangan = RequestData("keterangan", 0);
        $alasan = RequestData("alasan", 0);
        $rekkastrans = RequestData("rekkastrans", "");
        $rekutang = RequestData("rekutang", "");

        $sumberDana = RequestData("sumberdana", "***");
        $sumberDanaValue = $sumberDana == "***" ? "NULL" : "'$sumberDana'";

        $lokasiDana = RequestData("lokasidana", "***");
        $lokasiDanaValue = $lokasiDana == "***" ? "NULL" : "'$lokasiDana'";

        $petugas = getUserName();

        if (($debet == 0 && $jbayar == $kredit) || ($kredit == 0 && $jbayar == $debet))
        {
            //--------------------------------------------------------------
            // Hanya mengubah informasi pembayaran tanpa mengubah besarnya
            // -------------------------------------------------------------

            $sql = "UPDATE jbsfina.tabungan
                       SET keterangan='$keterangan',
                           alasan='$alasan',
                           petugas='$petugas',
                           sumberdana = $sumberDanaValue,
                           lokasidana = $lokasiDanaValue
                     WHERE replid=$idpembayaran";
            $db->QueryDb($sql);
            $db->CommitTrans();

            return json_encode([1, "OK"]);
        }
        else
        {
            //----------------------------
            // Mengubah besar pembayaran
            // ---------------------------

            if ($action == "tarik")
            {
                // Cek Saldo
                $sql = "SELECT SUM(debet), SUM(kredit)
                          FROM jbsfina.tabungan
                         WHERE nis = '$nis'
                           AND idtabungan = '$idtabungan'";
                $result = $db->QueryDb($sql);
                $row = mysqli_fetch_row($result);
                $jsetor = (int)$row[1];
                $jtarik = (int)$row[0];
                $jsaldo = $jsetor - $jtarik;

                $sql = "SELECT debet
                          FROM jbsfina.tabungan
                         WHERE replid = $idpembayaran";
                $result = $db->QueryDb($sql);
                $row = mysqli_fetch_row($result);
                $debetawal = (int)$row[0];

                $jsaldo = $jsaldo + $debetawal;
                if ($jsaldo < $jbayar)
                    return json_encode([-1, "Saldo tabungan tidak mencukupi untuk penarikan!"]);
            }
            else
            {
                // Cek Saldo
                $sql = "SELECT kredit
                          FROM jbsfina.tabungan
                         WHERE replid = $idpembayaran";
                $result = $db->QueryDb($sql);
                $row = mysqli_fetch_row($result);
                $kreditawal = (int)$row[0];

                if ($jbayar < $kreditawal)
                {
                    $sql = "SELECT SUM(debet), SUM(kredit)
                              FROM jbsfina.tabungan
                             WHERE nis = '$nis'
                               AND idtabungan = '$idtabungan'";
                    $result = $db->QueryDb($sql);
                    $row = mysqli_fetch_row($result);
                    $jsetor = (int)$row[1];
                    $jtarik = (int)$row[0];

                    $jsetor = $jsetor - $kreditawal + $jbayar;
                    if ($jsetor < $jtarik)
                        return json_encode([-1, "Saldo tabungan akan menjadi NEGATIF!"]);
                }
            }

            if ($action == "setor")
            {
                $debet = 0;
                $kredit = $jbayar;
            }
            else
            {
                $debet = $jbayar;
                $kredit = 0;
            }

            $sql = "UPDATE jbsfina.tabungan
                       SET keterangan='$keterangan',
                           alasan='$alasan',
                           petugas='$petugas',
                           debet='$debet',
                           kredit='$kredit',
                           sumberdana = $sumberDanaValue,
                           lokasidana = $lokasiDanaValue
                     WHERE replid=$idpembayaran";
            //Logger::LogOnce($sql);
            $db->QueryDb($sql);

            $sql = "SELECT idjurnal 
                      FROM jbsfina.tabungan 
                     WHERE replid = '$idpembayaran'";
            $idjurnal = $db->FetchSingle($sql, 0);

            $sql = "UPDATE jbsfina.jurnaldetail
                       SET debet='$kredit', kredit='$debet'
                     WHERE idjurnal='$idjurnal'
                       AND koderek='$rekkastrans'";
            $db->QueryDb($sql);

            $sql = "UPDATE jbsfina.jurnaldetail
                       SET debet='$debet', kredit='$kredit'
                     WHERE idjurnal='$idjurnal'
                       AND koderek='$rekutang'";
            $db->QueryDb($sql);

            $db->CommitTrans();

            return json_encode([1, "OK"]);
        }
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k8sxw")]);
    }
    finally
    {
        $db->Close();
    }
}

function ShowSelectLokasiPengambilanTabunganSiswa($db, $nis, $idTabungan, $defLokasiDana)
{
    try
    {
        $sql = "SELECT kode, nama
                  FROM jbsfina.lokasidana
                 ORDER BY urutan";
        $res = $db->QueryDb($sql);
        $lsLokasi = [];
        while($row = mysqli_fetch_row($res))
        {
            $lsLokasi[$row[0]] = $row[1];
        }

        $sql = "SELECT lokasidana, SUM(kredit - debet)
                  FROM jbsfina.tabungan
                 WHERE idtabungan = $idTabungan
                   AND nis = '$nis'
                 GROUP BY lokasidana";
        $res = $db->QueryDb($sql);

        echo "<select id='lokasidanatarik' class='inputbox' style='width:210px'>";
        while($row = mysqli_fetch_row($res))
        {
            $jumlah = FormatRupiah($row[1]);

            if (is_null($row[0]))
            {
                $kode = "***";
                $nama = "(tidak ada data)";
            }
            else
            {
                $kode = $row[0];
                $nama = $lsLokasi[$kode];
            }

            $lsInfo = [$kode, $row[1]];
            $jsonInfo64 = base64_encode(json_encode($lsInfo));

            $sel = $kode == $defLokasiDana ? "selected" : "";
            echo "<option value='$jsonInfo64' $sel>$nama - $jumlah</option>";
        }
        echo "</select>";
        echo "&nbsp;<img src='../images/ico/refresh.png' style='cursor: pointer' title='refresh' onclick='fetchLokasiPengambilan()'>";

    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kcm42");
    }
}
?>