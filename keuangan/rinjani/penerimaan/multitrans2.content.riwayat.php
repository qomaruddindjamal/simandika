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
require_once('../library/departemen.php');
require_once('../library/msg.php');
require_once('../library/rupiah.php');
require_once('../util/peek.php');
require_once('../library/userinfo.php');
require_once('../include/errorhandler.php');
require_once('pembayaran.jtt.func.php');
require_once('pembayaran.skr.func.php');
require_once('pembayaran.cswjb.func.php');
require_once('pembayaran.csskr.func.php');

$db = new Db;
$db->TryOpenExit(true);

$idpenerimaan = $_REQUEST["idpenerimaan"];
$idtahunbuku = $_REQUEST["idtahunbuku"];
$userId = $_REQUEST["userid"];
$userReplid = $_REQUEST["userreplid"];
$kategori = $_REQUEST["kategori"];

if ($kategori == "JTT")
    $title = "Riwayat Pembayaran Iuran Wajib";
else if ($kategori == "SKR")
    $title = "Riwayat Pembayaran Iuran Sukarela";
else if ($kategori == "CSWJB")
    $title = "Riwayat Pembayaran Iuran Wajib Calon Siswa";
else if ($kategori == "CSSKR")
    $title = "Riwayat Pembayaran Iuran Sukarela Calon Siswa";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?= $title ?></title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript">
        $(document).ready(function ()
        {
            $("#dvRiwayat .hide-in-report").remove();

            if ($("#spSisaPembayaran").length)
                $("#spSisaPembayaran").html($("#sisapembayaran").val());
        });
    </script>

</head>
<body style="background-color: #efefef; padding: 5px;">

<?php
if ($kategori == "JTT")
    $payInfo = GetPaymentInfoJtt($db, $idpenerimaan, $idtahunbuku, $userId);
else if ($kategori == "SKR")
    $payInfo = GetPaymentInfoSkr($db, $idpenerimaan, $idtahunbuku);
else if ($kategori == "CSWJB")
    $payInfo = GetPaymentInfoCsWjb($db, $idpenerimaan, $idtahunbuku, $userReplid);
else if ($kategori == "CSSKR")
    $payInfo = GetPaymentInfoCsSkr($db, $idpenerimaan, $idtahunbuku);

if ($payInfo->Exist == false)
{
    echo "<i>Tidak ditemukan data penerimaan /krw70</i>";
    exit();
}
?>
<div id="divSectionPayment">
<table class="rounded-box" width="100%" cellpadding="5">
<tr>
    <td>
        <span style="font-family: Verdana, serif; font-size: 20px; color: #333; font-weight: bold;">
<?php       echo $payInfo->Penerimaan ?>
        </span><br><br>
    </td>
</tr>
<tr>
    <td>

        <div id="dvRiwayat">
<?php
        if ($kategori == "JTT")
            ShowRiwayatPembayaranJtt($db, $payInfo);
        else if ($kategori == "SKR")
            ShowRiwayatPembayaranSkr($db, $userId, $idpenerimaan, $idtahunbuku);
        else if ($kategori == "CSWJB")
            ShowRiwayatPembayaranJttCalon($db, $payInfo);
        else if ($kategori == "CSSKR")
            ShowRiwayatPembayaranCsSkr($db, $userReplid, $idpenerimaan, $idtahunbuku);
?>
        </div>

    </td>
</tr>
</table>
</div>

</body>
</html>