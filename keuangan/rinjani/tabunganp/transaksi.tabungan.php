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
require_once('../library/logger.php');
require_once('../util/peek.php');
require_once('../library/userinfo.php');
require_once('../include/errorhandler.php');
require_once('transaksi.tabungan.func.php');


$db = new Db;
$db->TryOpenExit(true);

$nip = $_REQUEST["userid"];
$nama = $_REQUEST["username"];
$idtahunbuku = $_REQUEST['idtahunbuku'];
$namatahunbuku = $_REQUEST['namatahunbuku'];
$departemen = $_REQUEST['departemen'];
$jsontabungan = $_REQUEST["jsontabungan"];

$ls = json_decode(base64_decode($jsontabungan));
$idtabungan = $ls[0];
$namatabungan = $ls[1];
$sendnotif = $ls[2];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Setoran & Tarikan Tabungan Pegawai</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/rupiah3.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="transaksi.tabungan.js?r=<?=filemtime('transaksi.tabungan.js')?>"></script>
</head>
<body style="background-color: #efefef; padding: 5px;">

<?php
$userInfo = UserInfo::Pegawai($db, $nip);
if ($userInfo->Exist == false)
{
    echo "<i>Tidak ditemukan data pegawai $nip /khnck</i>";
    exit();
}
?>

<input type="hidden" id="departemen" value="<?=$departemen?>">
<input type="hidden" id="idtahunbuku" value="<?=$idtahunbuku?>">
<input type="hidden" id="namatahunbuku" value="<?=$namatahunbuku?>">
<input type="hidden" id="idtabungan" value="<?=$idtabungan?>">
<input type="hidden" id="namatabungan" value="<?=$namatabungan?>">
<input type="hidden" id="userid" value="<?=$nip?>">
<input type="hidden" id="username" value="<?=$nama?>">

<div id="divSectionUser">

    <table border="0" width="100%">
    <tr>
        <td width="100">
<?php       $userFoto = $userInfo->FotoExist ? $userInfo->Foto64 : UserInfo::$DefaultFoto; ?>
            <img style="width: 80px; height: 80px;" class="avatar-circle"
                 src="data:image/jpg;base64,<?= $userFoto ?>">
        </td>
        <td>
            <span style="font-family: 'Segoe UI', serif; font-size: 24px; color: #333; font-weight: bold">
                <?= $userInfo->Nama ?>
            </span><br>
            <span style="font-family: 'Segoe UI', serif; font-size: 18px; color: #333;">
                <?= $userInfo->NIP ?>
            </span><br>
            <span style="font-family: 'Segoe UI', serif; font-size: 12px; color: #666;">
                <?= $userInfo->Bagian ?>
            </span>&nbsp;&nbsp;
            <img src="../images/ico/lihat.png" title="informasi siswa"
                 class="hide-in-report" style="cursor: pointer" onclick="showInfoPegawai()">
        </td>
        <td width="250" align="right" valign="bottom">
            <a href='JavaScript:refresh()'><img src='../images/ico/refresh.png' border='0' title='refresh'>&nbsp;refresh</a>&nbsp;&nbsp;
            <a href='JavaScript:cetak()'><img src='../images/ico/print.png' border='0' title='refresh'>&nbsp;cetak</a>
        </td>
    </tr>
    </table>
</div>

<br>

<div id="divSectionTabunganInfo">
<?php
    ShowInfoTabunganPegawai($db) ?>
</div>

<div id="divSectionTransaksi" class="rounded-box" style="margin-top: 5px;">

    <table border="0" cellpadding="5">
    <tr>
        <td valign="top"><span style='color: #333; font-size: 18px'>Transaksi:</span></td>
        <td valign="top">

            <input type="hidden" id="viewSetoranInput" value="0">

            <table id="tabSetoranInput" style="background-color: #d7f3fc"
                   border="0" cellpadding="5" cellspacing="0" align="center" width="360">

            <thead style="cursor: pointer" onclick="showSetoranInput()">
                <tr height="25">
                    <td colspan="2" align="center"><b>SETORAN TABUNGAN</b></td>
                </tr>
            </thead>
            <tbody style="display: none">
                <tr>
                    <td width="25%">Tabungan <?= $tag_mandatory ?></td>
                    <td><b><?= $namatabungan ?></b></td>
                </tr>
                <tr>
                    <td>Jumlah <?= $tag_mandatory ?></td>
                    <td>
                        <input type="text" id="jsetor" class="inputbox-money fw-bold" style="width: 150px"
                               onblur="Rupiah.FormatRupiah('jsetor');" onfocus="Rupiah.UnformatRupiah('jsetor');" >
                    </td>
                </tr>
                <tr>
                    <td>Sumber Dana</td>
                    <td>
<?php                   ShowSelectSumberDanaTabunganPegawai($db) ?>
                    </td>
                </tr>
                <tr>
                    <td>Penyimpanan<br><i><span style='color: #999'>(lokasi dana)</span></i></td>
                    <td>
<?php                   ShowSelectLokasiDanaTabunganPegawai($db, 'lokasidanasetor') ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="left">Keterangan</td>
                    <td>
                        <textarea id="keterangansetor" name="keterangansetor" rows="2" cols="25" class="inputbox"></textarea>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="left">Notifikasi</td>
                    <td>
                        <?php $checked = ($sendnotif == 1) ? "checked" : ""; ?>
                        <input type="checkbox" id="sendnotifsetor" <?= $checked ?>> kirim ke JS | TGRAM | SMS
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="left">&nbsp;</td>
                    <td align="left">
                        <input type="button" name="simpansetor" id="simpansetor" class="dialogButtonPositive" value="SETOR" style="height: 30px; width: 120px;"
                               onclick="simpanSetoran()" >
                    </td>
                </tr>
            </tbody>
            </table>
        </td>
        <td valign="top">

            <input type="hidden" id="viewTarikanInput" value="0">

            <table border="0" id="tabTarikanInput" style="background-color: #fceccb" cellpadding="5" cellspacing="0" align="center" width="360">
            <thead style="cursor: pointer" onclick="showTarikanInput()">
                <tr height="25">
                    <td colspan="2" align="center"><b>TARIKAN TABUNGAN</b></td>
                </tr>
            </thead>
            <tbody style="display: none">
                <tr>
                    <td width="25%">Tabungan <?= $tag_mandatory ?></td>
                    <td><b><?= $namatabungan ?></b></td>
                </tr>
                <tr>
                    <td>Jumlah <?= $tag_mandatory ?></td>
                    <td>
                        <input type="text" id="jtarik" class="inputbox-money fw-bold" style="width: 150px"
                               onblur="Rupiah.FormatRupiah('jtarik');" onfocus="Rupiah.UnformatRupiah('jtarik');" >
                    </td>
                </tr>
                <tr>
                    <td>Pengambilan<br><i><span style='color: #999'>(lokasi dana)</span></i></td>
                    <td>
                        <span id="spPengambilan">
                            memuat ..
                        </span>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="left">Keterangan</td>
                    <td>
                        <textarea id="keterangantarik" rows="2" cols="25" class="inputbox"></textarea>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="left">Notifikasi</td>
                    <td>
                        <?php $checked = ($sendnotif == 1) ? "checked" : ""; ?>
                        <input type="checkbox" id="sendnotiftarik" <?= $checked ?>> kirim ke JS | TGRAM | SMS
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="left">&nbsp;</td>
                    <td align="left">
                        <input type="button" id="simpantarik" class="dialogButtonOrange" value="TARIK" style="height: 30px; width: 120px;"
                               onclick="simpanTarikan()">
                    </td>
                </tr>
            </tbody>
            </table>

        </td>
    </tr>
    </table>

</div>

<div id="divSectionRiwayat" class="rounded-box" style="margin-top: 5px; padding: 20px;">
<table cellpadding='0' cellspacing='0' border='0' style='width: 100%'>
<tr><td>
        <div id='dvTabTabunganList'>
<?php   $page = 1;
        $totalPage = 0;
        $nData = 0;
        ShowTransaksiTabunganPegawai($db) ?>
        </div>
</td></tr>
<tr><td>
        <div id='dvPageControl'>
<?php   ShowPageControl() ?>
        </div>
</td></tr>
</table>
</div>

<div id="toast-container"></div>
</body>
</html>