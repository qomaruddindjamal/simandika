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
require_once('pembayaran.csskr.bayar.func.php');

$db = new Db();
$db->TryOpenExit();

$departemen = $_REQUEST["departemen"];
$idPembayaran = $_REQUEST["idpembayaran"];
$idPenerimaan = $_REQUEST["idpenerimaan"];
$penerimaan = $_REQUEST["penerimaan"];
$idTahunBuku = $_REQUEST["idtahunbuku"];
$nic = $_REQUEST["nic"];
$idCalonSiswa = $_REQUEST["idcalonsiswa"];
$nama = $_REQUEST["nama"];
$rekkas = $_REQUEST["rekkas"];
$rekpendapatan = $_REQUEST["rekpendapatan"];
$sendnotif = $_REQUEST["sendnotif"];
$jumlah = "0";
$keterangan = "";
$sumberdana = "";
$idjurnal = "0";

$title = "Pembayaran Iuran Sukarela";
if ($idPembayaran != 0)
{
    $title = "Ubah Pembayaran Iuran Sukarela";
    LoadValues($db, $idPembayaran);

    $defrekkas = $rekkas;
}
else
{
    $defrekkas = $rekkas;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$title?></title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
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
    <script language="javascript" src="pembayaran.csskr.bayar.js?r=<?= filemtime('pembayaran.csskr.bayar.js') ?>"></script>
</head>
<body style="padding: 10px">
<input type="hidden" id="departemen" value="<?=$departemen?>">
<input type="hidden" id="idpembayaran" value="<?=$idPembayaran?>">
<input type="hidden" id="idpenerimaan" value="<?=$idPenerimaan?>">
<input type="hidden" id="penerimaan" value="<?=$penerimaan?>">
<input type="hidden" id="idtahunbuku" value="<?=$idTahunBuku?>">
<input type="hidden" id="nic" value="<?=$nic?>">
<input type="hidden" id="idcalonsiswa" value="<?=$idCalonSiswa?>">
<input type="hidden" id="nama" value="<?=$nama?>">
<input type="hidden" id="rekpendapatan" value="<?=$rekpendapatan?>">
<input type="hidden" id="origjumlah" value="<?=$jumlah?>">
<input type="hidden" id="origrekkas" value="<?=$rekkas?>">
<input type="hidden" id="idjurnal" value="<?=$idjurnal?>">

<?php
UserInfo::ShowSimpleCalonSiswaAvatar($db, $nic);
?>
<table cellpadding="5" cellspacing="0">
<tr>
    <td colspan="2" align="center">
        <span class="consolasFont">--- Terima Pembayaran ---</span><br>
        <span class="dialogTitle"><?=$penerimaan?></span>
    </td>
</tr>
<tr>
    <td width="150">Jumlah<?=$tag_mandatory?></td>
    <td width="500">
        <input id="jumlah" type="text" class="inputbox-money bg-light-blue" style="width: 250px"
               value="<?= FormatRupiah($jumlah) ?>"
               onblur="Rupiah.FormatRupiah('jumlah')"
               onfocus="Rupiah.UnformatRupiah('jumlah')" >
    </td>
</tr>
<tr>
    <td>Rek. Kas<?=$tag_mandatory?></td>
    <td>
        <?php

        ShowSelectRekKasCsSkr($db);
        ?>
    </td>
</tr>
<tr>
    <td>Sumber Dana<?=$tag_mandatory?></td>
    <td>
        <?php
        ShowSelectSumberDanaCsSkr($db);
        ?>
    </td>
</tr>
<tr>
    <td>Keterangan</td>
    <td>
        <textarea rows="3" cols="40" class="inputbox" id="keterangan"><?=$keterangan?></textarea>
    </td>
</tr>
<?php
if ($idPembayaran != 0)
{
    ?>
    <tr>
        <td>Alasan Perubahan Data<?=$tag_mandatory?></td>
        <td>
            <textarea rows="3" cols="40" class="inputbox" id="alasan"></textarea>
        </td>
    </tr>
    <?php
}
?>
<?php
if ($idPembayaran == 0)
{
    ?>
    <tr>
        <td>Notifikasi</td>
        <td>
            <?php $checked = ($sendnotif == 1) ? "checked" : ""; ?>
            <input type="checkbox" id="sendnotif" <?= $checked ?>> kirim ke Jendela Sekolah | Telegram | SMS
        </td>
    </tr>
<?php
}
?>
<tr>
    <td colspan="2" align="center">
        <br>
        <input type="button" id="btSimpan" class="dialogButtonPositive" value="Simpan" onclick="simpanBayarCsSkr()">
        <input type="button" class="dialogButtonNegative" value="Tutup" onclick="window.close()">
        <br><br>
        <span id="spInfo" style="color: blue"></span>
    </td>
</tr>
</table>
</body>
</html>
