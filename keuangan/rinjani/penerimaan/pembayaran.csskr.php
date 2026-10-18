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
require_once('pembayaran.csskr.func.php');

$db = new Db;
$db->TryOpenExit(true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pembayaran Iuran Sukarela Calon Siswa</title>
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
    <script language="javascript" src="pembayaran.csskr.js?r=<?=filemtime('pembayaran.csskr.js')?>"></script>
</head>
<body style="background-color: #efefef; padding: 5px;">

<?php
$userInfo = UserInfo::CalonSiswa($db, $userId);
if ($userInfo->Exist == false)
{
    echo "<i>Tidak ditemukan data calon siswa $userId /kv5y1</i>";
    exit();
}

$payInfo = GetPaymentInfoCsSkr($db, $idpenerimaan, $idtahunbuku);
if ($payInfo->Exist == false)
{
    echo "<i>Tidak ditemukan data penerimaan /knjcw</i>";
    exit();
}
?>

<input type="hidden" id="idkategori" value="<?=$idkategori?>">
<input type="hidden" id="departemen" value="<?=$departemen?>">
<input type="hidden" id="idtahunbuku" value="<?=$idtahunbuku?>">
<input type="hidden" id="idpenerimaan" value="<?=$idpenerimaan?>">
<input type="hidden" id="penerimaan" value="<?=$payInfo->Penerimaan?>">
<input type="hidden" id="userid" value="<?=$userId?>">
<input type="hidden" id="username" value="<?=$userName?>">
<input type="hidden" id="usergroup" value="<?=$userGroup?>">
<input type="hidden" id="departemen" value="<?= $userInfo->Departemen?>">
<input type="hidden" id="rekkas" value="<?= $payInfo->RekKas ?>">
<input type="hidden" id="rekpendapatan" value="<?= $payInfo->RekPendapatan ?>">
<input type="hidden" id="sendnotif" value="<?= $payInfo->SendNotif ?>">
<input type="hidden" id="idcalonsiswa" value="<?= $userInfo->IdCalonSiswa ?>">

<div id="divSectionUser">
<?php   UserInfo::ShowCalonSiswaAvatar($userInfo); ?>
</div>

<br>

<div id="divSectionPayment">

    <table class="rounded-box" width="100%" cellpadding="5">
    <tr>
        <td>
        <span style="font-family: Verdana, serif; font-size: 20px; color: #333; font-weight: bold;">
            <?= $payInfo->Penerimaan ?>
        </span><br><br>
        </td>
    </tr>
    <tr>
        <td>

            <div id="dvRiwayatIuran">
<?php           ShowRiwayatPembayaranCsSkr($db, $userInfo->IdCalonSiswa, $payInfo->IdPenerimaan, $payInfo->IdTahunBuku); ?>
            </div>

        </td>
    </tr>
    </table>

</div>

<div id="toast-container"></div>

</body>
</html>