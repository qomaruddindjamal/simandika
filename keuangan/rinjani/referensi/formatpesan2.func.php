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
require_once ("../include/config.php");
require_once ("../include/db.onfunc.php");
require_once ("../library/departemen.php");
require_once ("../library/msg.php");

function ShowSelectDepartemen_FPN($db)
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
        echo Msg::InfoError($ex->getMessage(), "kxb3j");
    }
}

function FetchPesanNotifikasi()
{
    $db = new Db();
    try
    {
        $db->Open();

        $seldept = $_REQUEST["departemen"];

        $sql = "SELECT COUNT(replid)
				  FROM jbsfina.formatsms
				 WHERE departemen = '$seldept'
				   AND jenis = 'SISPAY'";
        $ndata = $db->ExecuteScalar($sql, 0);
        if ($ndata == 0)
        {
            $format = "Terima kasih, kami telah menerima pembayaran dari {NAMA} tanggal {TANGGAL} sebesar {BESAR} untuk {PEMBAYARAN} - Bag. Keuangan";
            $sql = "INSERT INTO jbsfina.formatsms
					   SET jenis = 'SISPAY', departemen = '$seldept', format = '$format'";
            $db->QueryDb($sql);
        }

        $sql = "SELECT format
				  FROM jbsfina.formatsms
				 WHERE departemen = '$seldept'
				   AND jenis = 'SISPAY'";
        $sisformatsms = $db->ExecuteScalar($sql, "");

        //-----------------------
        $sql = "SELECT COUNT(replid)
				  FROM jbsfina.formatsms
				 WHERE departemen = '$seldept'
				   AND jenis = 'CSISPAY'";
        $ndata = $db->ExecuteScalar($sql, 0);
        if ($ndata == 0)
        {
            $format = "Terima kasih, kami telah menerima pembayaran dari {NAMA} tanggal {TANGGAL} sebesar {BESAR} untuk {PEMBAYARAN} - Bag. Keuangan";
            $sql = "INSERT INTO jbsfina.formatsms
					   SET jenis = 'CSISPAY', departemen = '$seldept', format = '$format'";
            $db->QueryDb($sql);
        }

        $sql = "SELECT format
				  FROM jbsfina.formatsms
				 WHERE departemen = '$seldept'
				   AND jenis = 'CSISPAY'";
        $csisformatsms = $db->ExecuteScalar($sql, "");

        // ---------------------
        $sql = "SELECT COUNT(replid)
                  FROM jbsfina.formatsms
                 WHERE departemen = '$seldept'
                   AND jenis = 'SISTUNG'";
        $ndata = $db->ExecuteScalar($sql, 0);
        if ($ndata == 0)
        {
            $format = "Kami informasikan {NAMA} masih memiliki tunggakan sebesar {TUNGGAKAN} untuk {PEMBAYARAN} - Bag. Keuangan";
            $sql = "INSERT INTO jbsfina.formatsms
                       SET jenis = 'SISTUNG', departemen = '$seldept', format = '$format'";
            $db->QueryDb($sql);
        }

        // ---------------------------------
        $sql = "SELECT format
                  FROM jbsfina.formatsms
                 WHERE departemen = '$seldept'
                   AND jenis = 'SISTUNG'";
        $tunggakformatsms = $db->ExecuteScalar($sql, "");

        $sql = "SELECT COUNT(replid)
                  FROM jbsfina.formatsms
                 WHERE departemen = '$seldept'
                   AND jenis = 'SISTAB'";
        $ndata = $db->ExecuteScalar($sql, 0);
        if ($ndata == 0)
        {
            $format = "Kami informasikan transaksi tabungan dari {NAMA} tanggal {TANGGAL} sebesar {BESAR} untuk {PEMBAYARAN} saldo {SALDO} keterangan {KETERANGAN} - Bag. Keuangan";
            $sql = "INSERT INTO jbsfina.formatsms
                       SET jenis = 'SISTAB', departemen = '$seldept', format = '$format'";
            $db->QueryDb($sql);
        }

        $sql = "SELECT format
                  FROM jbsfina.formatsms
                 WHERE departemen = '$seldept'
                   AND jenis = 'SISTAB'";
        $tabunganformatsms = $db->ExecuteScalar($sql, "");

        // ---------------------------------
        // SchoolPay Cashless Payment
        $sql = "SELECT COUNT(replid)
                  FROM jbsfina.formatsms
                 WHERE departemen = '$seldept'
                   AND jenis = 'SCHOOLPAY'";
        $ndata =  $db->ExecuteScalar($sql, 0);
        if ($ndata == 0)
        {
            $format = "Kami informasikan transaksi pembayaran non tunai dari {NAMA} tanggal {TANGGAL} sebesar {BESAR}, saldo tersisa {SALDO}, nomor {TRANSID}";
            $sql = "INSERT INTO jbsfina.formatsms
                       SET jenis = 'SCHOOLPAY', departemen = '$seldept', format = '$format'";
            $db->QueryDb($sql);
        }

        $sql = "SELECT format
                  FROM jbsfina.formatsms
                 WHERE departemen = '$seldept'
                   AND jenis = 'SCHOOLPAY'";
        $paymentformatsms = $db->ExecuteScalar($sql, "");

        $ls = [ $sisformatsms, $csisformatsms, $tunggakformatsms, $tabunganformatsms, $paymentformatsms ];
        $json64 = base64_encode(json_encode($ls));

        return json_encode([1, $json64]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();

        echo json_encode([-99, Msg::InfoError($ex->getMessage(), "k75gr")]) ;
    }
    finally
    {
        $db->Close();
    }
}

function SimpanPesanNotifikasi()
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = $_REQUEST['departemen'];
        $sisformatsms = SafeInput($_REQUEST['sisformatsms']);
        $csisformatsms = SafeInput($_REQUEST['csisformatsms']);
        $tabunganformatsms = SafeInput($_REQUEST['tabunganformatsms']);
        $tungformatsms = SafeInput($_REQUEST['tungformatsms']);
        $paymentformatsms = SafeInput($_REQUEST['paymentformatsms']);

        $sql = "SELECT COUNT(*)
                  FROM jbsfina.formatsms
                 WHERE jenis = 'SISPAY'
                   AND departemen = '$departemen'";
        $ndata = $db->ExecuteScalar($sql, 0);
        if ($ndata > 0)
        {
            $sql = "UPDATE jbsfina.formatsms
	                   SET format = '$sisformatsms'
	                 WHERE jenis = 'SISPAY'
        	           AND departemen = '$departemen'";
        }
        else
        {
            $sql = "INSERT INTO jbsfina.formatsms
	                   SET format = '$sisformatsms', jenis = 'SISPAY', departemen = '$departemen'";
        }
        $db->QueryDb($sql);

        //---
        $sql = "SELECT COUNT(*)
                  FROM jbsfina.formatsms
                 WHERE jenis = 'CSISPAY'
                   AND departemen = '$departemen'";
        $ndata = $db->ExecuteScalar($sql, 0);
        if ($ndata > 0)
        {
            $sql = "UPDATE jbsfina.formatsms
                       SET format = '$csisformatsms'
                     WHERE jenis = 'CSISPAY'
                       AND departemen = '$departemen'";
        }
        else
        {
            $sql = "INSERT INTO jbsfina.formatsms
	                   SET format = '$csisformatsms', jenis = 'CSISPAY', departemen = '$departemen'";
        }
        $db->QueryDb($sql);

        //----
        $sql = "SELECT COUNT(*)
                  FROM jbsfina.formatsms
                 WHERE jenis = 'SISTUNG'
                   AND departemen = '$departemen'";
        $ndata = $db->ExecuteScalar($sql, 0);
        if ($ndata > 0)
        {
            $sql = "UPDATE jbsfina.formatsms
                       SET format = '$tungformatsms'
                     WHERE jenis = 'SISTUNG'
                       AND departemen = '$departemen'";
        }
        else
        {
            $sql = "INSERT INTO jbsfina.formatsms
	                   SET format = '$tungformatsms', jenis = 'SISTUNG', departemen = '$departemen'";
        }
        $db->QueryDb($sql);

        // --
        $sql = "SELECT COUNT(*)
                  FROM jbsfina.formatsms
                 WHERE jenis = 'SISTAB'
                   AND departemen = '$departemen'";
        $ndata = $db->ExecuteScalar($sql, 0);
        if ($ndata > 0)
        {
            $sql = "UPDATE jbsfina.formatsms
                       SET format = '$tabunganformatsms'
                     WHERE jenis = 'SISTAB'
                       AND departemen = '$departemen'";
        }
        else
        {
            $sql = "INSERT INTO jbsfina.formatsms
	           SET format = '$tabunganformatsms', jenis = 'SISTAB', departemen = '$departemen'";
        }
        $db->QueryDb($sql);

        // --
        $sql = "SELECT COUNT(*)
                  FROM jbsfina.formatsms
                 WHERE jenis = 'SCHOOLPAY'
                   AND departemen = '$departemen'";
        $ndata = $db->ExecuteScalar($sql, 0);
        if ($ndata > 0)
        {
            $sql = "UPDATE jbsfina.formatsms
                       SET format = '$paymentformatsms'
                     WHERE jenis = 'SCHOOLPAY'
                       AND departemen = '$departemen'";
        }
        else
        {
            $sql = "INSERT INTO jbsfina.formatsms
	                   SET format = '$paymentformatsms', jenis = 'SCHOOLPAY', departemen = '$departemen'";
        }
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();

        echo json_encode([-99, Msg::InfoError($ex->getMessage(), "knn2q")]) ;
    }
    finally
    {
        $db->Close();
    }
}
?>