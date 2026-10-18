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
$nRowPerPage = 10;

function DefaultFormatNotif($db)
{
    global $departemen;

    $defFormat = "Kami informasikan {NAMA} masih memiliki tunggakan sebesar {TUNGGAKAN} untuk {PEMBAYARAN} - Bag. Keuangan";

    $sql = "SELECT COUNT(replid)
              FROM jbsfina.formatsms
             WHERE departemen = '$departemen'
               AND jenis = 'SISTUNG'";
    $ndata = $db->FetchSingle($sql, 0);
    if ($ndata == 0)
    {
        $sql = "INSERT INTO jbsfina.formatsms
                   SET jenis = 'SISTUNG', departemen = '$departemen', format = '$defFormat'";
        $db->QueryDb($sql);
    }

    $sql = "SELECT format
              FROM jbsfina.formatsms
             WHERE departemen = '$departemen'
               AND jenis = 'SISTUNG'";
    $format = $db->FetchSingle($sql, $defFormat);

    return $format;
}

function SendNotif()
{
    $db = new Db();
    try
    {
        $db->Open();

        $db->BeginTrans();

        $departemen = RequestData("departemen", "");
        $idPenerimaan = RequestData("idpenerimaan", 0);
        $namaPenerimaan = RequestData("namapenerimaan", "");
        $formatNotif = RequestData("formatnotif", "");
        $nData = RequestData("ndata", 0);

        for($i = 1; $i <= $nData; $i++)
        {
            $tunggakan = RequestData("tunggakan-$i", 0);
            $nis = RequestData("nis-$i", "");
            $nama = RequestData("nama-$i", "");

            $notif = str_replace("{NAMA}", $nama, $formatNotif);
            $notif = str_replace("{NIS}", $nis, $notif);
            $notif = str_replace("{TUNGGAKAN}", FormatRupiah($tunggakan), $notif);
            $notif = str_replace("{PEMBAYARAN}", $namaPenerimaan, $notif);
            $notif = str_replace("'", "`", $notif);

            //Logger::LogOnce($notif);

            CreateSMSTunggakan2($db, "SISTUNG", $departemen, $nis, $nama, $notif);
        }

        //$db->RollbackTrans();
        $db->CommitTrans();

        return json_encode([1, "Berhasil menyiapkan $nData pesan notifikasi tunggakan"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        $db->RollbackTrans();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kmvjs")]);
    }
    finally
    {
        $db->Close();
    }
}
?>