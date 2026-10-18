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
require_once('bayartunggak.content.siswa.func.php');

$idtahunbuku = $_REQUEST['idtahunbuku'];
$namatahunbuku = $_REQUEST['namatahunbuku'];
$idkategori = $_REQUEST['idkategori'];
$namakategori = $_REQUEST['namakategori'];
$idpenerimaan = $_REQUEST['idpenerimaan'];
$namapenerimaan = $_REQUEST['namapenerimaan'];
$departemen = $_REQUEST['departemen'];
$nis = $_REQUEST["nis"];
$nama = $_REQUEST["nama"];

$db = new Db();
$db->TryOpenExit();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Pembayaran Sisa Tunggakan</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="bayartunggak.content.siswa.js?r=<?=filemtime('bayartunggak.content.siswa.js')?>"></script>
</head>

<body style="margin: 10px;">
<input type="hidden" id="idtahunbuku" value="<?=$idtahunbuku ?>" />
<input type="hidden" id="namatahunbuku" value="<?=$namatahunbuku ?>" />
<input type="hidden" id="idkategori" value="<?=$idkategori ?>" />
<input type="hidden" id="namakategori" value="<?=$namakategori ?>" />
<input type="hidden" id="idpenerimaan" value="<?=$idpenerimaan ?>" />
<input type="hidden" id="namapenerimaan" value="<?=$namapenerimaan ?>" />
<input type="hidden" id="departemen" value="<?=$departemen ?>" />
<input type="hidden" id="userid" value="<?=$nis ?>" />
<input type="hidden" id="username" value="<?=$nama ?>" />


<?php
$userInfo = UserInfo::Siswa($db, $nis);
if ($userInfo->Exist == false)
{
    echo "<i>Tidak ditemukan data siswa $nis /khnck</i>";
    exit();
}

$payInfo = GetPaymentInfoJtt_Tunggak($db, $idpenerimaan, $idtahunbuku, $nis);
if ($payInfo->Exist == false)
{
    echo "<i>Tidak ditemukan data penerimaan /krw70</i>";
    exit();
}
?>

<div id="divSectionUser">
<?php UserInfo::ShowSiswaAvatar($userInfo); ?>
</div>

<br>

<div id="divSectionPayment">
<table class="rounded-box" width="100%" cellpadding="5">
    <tr>
        <td>
            <span style="font-family: Verdana, serif; font-size: 20px; color: #333; font-weight: bold;">
<?php       echo $payInfo->Penerimaan . " ($namatahunbuku)" ?>
            </span><br><br>

            <div id="dvBesarJttInfo">
<?php       ShowBesarJttInfo_Tunggak($payInfo); ?>
            </div>

        </td>
    </tr>
    <tr>
        <td>

            <div id="dvRiwayatJtt">
<?php       ShowRiwayatPembayaranJtt_Tunggak($db, $payInfo);    ?>
            </div>

        </td>
    </tr>
</table>
</div>

<div id="toast-container"></div>

</body>
</html>