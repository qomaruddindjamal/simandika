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
require_once('../include/sessioninfo.php');
require_once('../include/sessionchecker.php');
require_once('../library/common.func.php');
require_once('../include/config.php');
require_once('../include/db.onfunc.php');
require_once('../include/db.onpage.php');
require_once('../library/departemen.php');
require_once('../library/msg.php');
require_once('../library/jurnal.php');
require_once('../library/logger.php');
require_once('../library/rupiah.php');
require_once('../library/smsmanager.func.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('transprev.content.save.func.php');

if (1 != (int)$_SESSION["multipaystep"])
{
    echo "Maaf, halaman ini tidak bisa dimuat ulang!";
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Multiple Transactions</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/rupiah2.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/validator.js"></script>
    <script language="javascript" src="transprev.content.save.js?<?=filemtime('transprev.content.save.js')?>"></script>
</head>
<body style="margin: 10px;">
<?php
$departemen = $_REQUEST['departemen'];
$kelompok = $_REQUEST['kelompok'];
$idtahunbuku = $_REQUEST['idtahunbuku'];
$idtahunbukuaktif = $_REQUEST['idtahunbukuaktif'];
$studentid = $_REQUEST['noid'];
$studentname = $_REQUEST['nama'];
$ktransaksi = $_REQUEST['ktransaksi'];
$ktransaksi = str_replace("'", "`", $ktransaksi);
$ktransaksi = str_replace('"', '`', $ktransaksi);
$smsinfo = isset($_REQUEST['smsinfo']) ? 1 : 0;

OpenDb();

$success = true;
BeginTrans();

$transactions = array();

$nrow = $_REQUEST['nflagrow'];
for($i = 0; $i < $nrow; $i++)
{
    $tmp = "flagrow$i";
    if ($_REQUEST[$tmp] == 0)
        continue;

    $tmp = "i_kate_$i";
    $kate = $_REQUEST[$tmp];

    if ($kate == "JTT")
        $success = SaveIuranWajibSiswa($i);
    elseif ($kate == "CSWJB")
        $success = SaveIuranWajibCalonSiswa($i);

    if (!$success)
        break;
}

// 2020-09-12: Simpan $transaction ke table multitransinfo utk digunakan di SchoolPay
if ($success)
    SaveMultiTransInfo();

if ($success && $smsinfo == 1)
    $success = CreateSMSReport();

if ($success)
{
    //RollbackTrans();
    CommitTrans();
    ?>
    <br><br>
    <font style="font-size: 18px; color: blue">Transaksi telah berhasil disimpan</font><br>
    Cetak Tanda Bukti Pembayaran:&nbsp;&nbsp;
    <input type="button" class="dialogButtonPositive" value="Sederhana" onclick="PrintCompact()" style='height: 30px'>&nbsp;
    <input type="button" class="dialogButtonPositive" value="Detail" onclick="PrintDetail()" style='height: 30px'> <br>
    <input type="hidden" name="departemen" id="departemen" value="<?=$departemen?>">
    <input type="hidden" name="kelompok" id="kelompok" value="<?=$kelompok?>">
    <input type="hidden" name="idtahunbuku" id="idtahunbuku" value="<?=$idtahunbuku?>">
    <input type="hidden" name="studentid" id="studentid" value="<?=$studentid?>">
    <input type="hidden" name="ktransaksi" id="ktransaksi" value="<?=$ktransaksi?>">
<?php
    CountTotalPayment(); ?>
    <div id="divReportCompact" style="visibility: hidden">
<?php
        CreateDivPrintReportCompact(); ?>
    </div>
    <div id="divReportDetail" style="visibility: hidden">
<?php
        CreateDivPrintReportDetail(); ?>
    </div>
<?php   $_SESSION["multipaystep"] = 2;
}
else
{
    RollbackTrans(); ?>
    <br><br>
    <font style="font-size: 18px; color: red">Gagal menyimpan data. Tidak ada data transaksi yang tersimpan.</font><br>
<?php
}
CloseDb();
?>
</body>
</html>