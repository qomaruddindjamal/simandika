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
require_once('../library/rupiah.php');
require_once('../library/userinfo.php');
require_once('../include/db.onfunc.php');
require_once('pembayaran.jtt.dialog.func.php');

$db = new Db();
$db->TryOpenExit();

$idPenerimaan = $_REQUEST["idpenerimaan"];
$penerimaan = $_REQUEST["penerimaan"];
$idBesarJtt = $_REQUEST["idbesarjtt"];
$idTahunBuku = $_REQUEST["idtahunbuku"];
$idKategori = $_REQUEST["idkategori"];
$nis = $_REQUEST["nis"];
$nama = $_REQUEST["nama"];

$besar = "0";
$cicilan = "0";
$keterangan = "";
$lunas = 0;
$idJurnal = 0;

$title = "Atur Besar Pembayaran";
if ($idBesarJtt != 0)
{
    $title = "Ubah Besar Pembayaran";
    LoadValues($db, $idBesarJtt);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$title?></title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js?<?=filemtime('../style/tables.js')?>"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/rupiah3.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="pembayaran.jtt.dialog.js?r=<?= filemtime('pembayaran.jtt.dialog.js') ?>"></script>
</head>
<body style="padding: 10px">
<input type="hidden" id="idpenerimaan" value="<?=$idPenerimaan?>">
<input type="hidden" id="penerimaan" value="<?=$penerimaan?>">
<input type="hidden" id="idbesarjtt" value="<?=$idBesarJtt?>">
<input type="hidden" id="idtahunbuku" value="<?=$idTahunBuku?>">
<input type="hidden" id="idkategori" value="<?=$idKategori?>">
<input type="hidden" id="nis" value="<?=$nis?>">
<input type="hidden" id="nama" value="<?=$nama?>">

<?php
UserInfo::ShowSimpleSiswaAvatar($db, $nis);
?>

<table cellpadding="5" cellspacing="0">
<tr>
    <td colspan="2" align="center">
        <span class="consolasFont">--- Besar Pembayaran ---</span><br>
        <span class="dialogTitle"><?=$penerimaan?></span>
    </td>
</tr>
<tr>
    <td width="120">Besar Pembayaran<?= $tag_mandatory?></td>
    <td width="500">
        <input id="besar" type="text" class="inputbox-money bg-light-blue" style="width: 250px"
               value="<?= FormatRupiah($besar) ?>"
               onblur="Rupiah.FormatRupiah('besar')"
               onfocus="Rupiah.UnformatRupiah('besar')" >
    </td>
</tr>
<tr>
    <td>Besar Cicilan<?= $tag_mandatory?></td>
    <td>
        <input id="cicilan" type="text" class="inputbox-money bg-light-green" style="width: 250px"
               value="<?= FormatRupiah($cicilan) ?>"
               onblur="Rupiah.FormatRupiah('cicilan')"
               onfocus="Rupiah.UnformatRupiah('cicilan')">
    </td>
</tr>
<?php
if ($idBesarJtt == 0) {
    ?>
    <tr>
        <td>Cicilan Pertama</td>
        <td>
            <input type="checkbox" id="cicilanpertama">&nbsp;set cicilan pertama Rp 0<br>
            <span style="color: #666; font-style: italic">set cicilan pertama Rp 0 supaya muncul di Laporan Tunggakan</span>
        </td>
    </tr>
    <?php
}
?>
<tr>
    <td>Keterangan</td>
    <td>
        <textarea rows="3" cols="40" class="inputbox" id="keterangan"><?=$keterangan?></textarea>
    </td>
</tr>
<?php
if ($idBesarJtt != 0) {
    ?>
    <tr>
        <td>Alasan Perubahan Data<?= $tag_mandatory?></td>
        <td>
            <textarea rows="3" cols="40" class="inputbox" id="alasan"></textarea>
        </td>
    </tr>
    <?php
}
?>
<tr>
    <td colspan="2" align="center">
        <br>
        <input type="button" id="btSimpan" class="dialogButtonPositive" value="Simpan" onclick="simpanBesarJtt()">
        <input type="button" class="dialogButtonNegative" value="Tutup" onclick="window.close()">
        <br><br>
        <span id="spInfo" style="color: blue"></span>
    </td>
</tr>
</table>
</body>
</html>
