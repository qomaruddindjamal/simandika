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
require_once('../include/db.onpage.php');
require_once('../library/departemen.php');
require_once('../library/msg.php');
require_once('../library/rupiah.php');
require_once('../library/jurnal.php');
require_once('../library/logger.php');
require_once('../library/smsmanager.func.php');
require_once('../include/errorhandler.php');
require_once('autotrans2.payment.save.func.php');

if (1 != (int)$_SESSION["autotransstep"])
{
    echo "Maaf, halaman ini tidak bisa dimuat ulang!";
    exit();
}

$departemen = $_REQUEST['departemen'];
$kelompok = $_REQUEST['kelompok'];
$idtahunbuku = $_REQUEST['idtahunbuku'];
$studentid = $_REQUEST['noid'];
$studentname = $_REQUEST['nama'];
$ktransaksi = $_REQUEST['ktransaksi'];
$ktransaksi = str_replace("'", "`", $ktransaksi);
$ktransaksi = str_replace('"', '`', $ktransaksi);
$smsinfo = isset($_REQUEST['smsinfo']) ? 1 : 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="../style/style.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Batch Payment</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/rupiah2.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="autotrans2.payment.save.js?<?=filemtime('autotrans2.payment.save.js')?>"></script>
</head>

<body style="margin: 10px">

<table border="0" width="95%" align="center">
<tr>
    <td align="right">
        <span class="pageTitle">Batch Payment</span>
    </td>
</tr>
<tr>
    <td align="right">
        <a class="pageLink" href="penerimaan.php"><b>Penerimaan</b></a>&nbsp;&gt;&nbsp;
        <span class="pageLinkCurrent">Batch Payment</td>
</tr>
<tr>
    <td align="left">&nbsp;</td>
</tr>
</table><br />

<?php
OpenDb();

$success = true;
BeginTrans();

$transactions = array();

$ndata = $_REQUEST["ndata"];
for($i = 1; $i <= $ndata; $i++)
{
    if (!isset($_REQUEST["chPayment-$i"]))
        continue;

    $kate = $_REQUEST["kategori-$i"];
    if ($kate == "JTT")
        $success = SaveIuranWajibSiswa($i);
    elseif ($kate == "SKR")
        $success = SaveIuranSukarelaSiswa($i);
    elseif ($kate == "CSWJB")
        $success = SaveIuranWajibCalonSiswa($i);
    elseif ($kate == "CSSKR")
        $success = SaveIuranSukarelaCalonSiswa($i);

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
    CommitTrans();
    ?>

    <br><br>
    <font style="font-size: 18px; color: blue">Transaksi telah berhasil disimpan</font><br>
    Cetak Tanda Bukti Pembayaran:&nbsp;&nbsp;
    <input type="button" class="dialogButtonPositive" value="  Sederhana  " onclick="PrintCompact()" style='height: 30px'>&nbsp;
    <input type="button" class="dialogButtonPositive" value="  Detail  " onclick="PrintDetail()" style='height: 30px'>&nbsp;
    <a href="autotrans2.payment.php?departemen=<?=$departemen?>" style="font-weight: normal; color: blue; text-decoration: underline">kembali</a>
    <br>
    <input type="hidden" name="departemen" id="departemen" value="<?=$departemen?>">
    <input type="hidden" name="kelompok" id="kelompok" value="<?=$kelompok?>">
    <input type="hidden" name="idtahunbuku" id="idtahunbuku" value="<?=$idtahunbuku?>">
    <input type="hidden" name="studentid" id="studentid" value="<?=$studentid?>">
    <input type="hidden" name="ktransaksi" id="ktransaksi" value="<?=$ktransaksi?>">
<?php
    CountTotalPayment();
?>
    <div id="divReportCompact" style="visibility: hidden">
<?php
    CreateDivPrintReportCompact();
?>
    </div>
    <div id="divReportDetail" style="visibility: hidden">
<?php
    CreateDivPrintReportDetail();
?>
    </div>
<?php
    $_SESSION["autotransstep"] = 2;
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