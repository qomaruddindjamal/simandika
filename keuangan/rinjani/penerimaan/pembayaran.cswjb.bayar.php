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
require_once('../library/logger.php');
require_once('../library/userinfo.php');
require_once('../util/peek.php');
require_once('../include/db.onfunc.php');
require_once('pembayaran.cswjb.bayar.func.php');

$db = new Db();
$db->TryOpenExit();

$idPembayaran = $_REQUEST["idpembayaran"];
$idPenerimaan = $_REQUEST["idpenerimaan"];
$penerimaan = $_REQUEST["penerimaan"];
$idBesarJtt = $_REQUEST["idbesarjtt"];
$idTahunBuku = $_REQUEST["idtahunbuku"];
$idKategori = $_REQUEST["idkategori"];
$idCalonSiswa = $_REQUEST["idcalonsiswa"];
$nic = $_REQUEST["nic"];
$nama = $_REQUEST["nama"];
$departemen = $_REQUEST["departemen"];
$defrekkas = $_REQUEST["rekkas"];
$defcicilan = $_REQUEST["jcicilan"];
$sendnotif = $_REQUEST["sendnotif"];
$defrekkas = "";

$title = "";
$jcicilan = 0;
$jdiskon = 0;
$jbayar = 0;
if ($idPembayaran == 0)
{
    $title = "Bayar Cicilan";

    $jcicilan = $defcicilan;
    $jbayar = $jcicilan;
}
else
{
    $title = "Ubah Cicilan";

    $sql = "SELECT jd.koderek
              FROM jbsfina.penerimaanjttcalon p, jbsfina.jurnal j, jbsfina.jurnaldetail jd, rekakun rk
             WHERE p.replid = '$idPembayaran'
               AND p.idjurnal = j.replid
               AND j.replid = jd.idjurnal
               AND jd.koderek = rk.kode
               AND rk.kategori = 'HARTA'";
    //Logger::LogOnce($sql);
    $defrekkas = $db->FetchSingle($sql, 0);

    // -- ambil data-data pembayaran ---------------------------------
    $sql = "SELECT cs.nopendaftaran, b.idcalon, b.besar, b.lunas, p.idbesarjttcalon, cs.nama, p.idjurnal, p.jumlah, date_format(p.tanggal, '%d-%m-%Y') as tanggal, 
                   p.keterangan, pn.nama as namapenerimaan, pn.rekkas, pn.rekpendapatan, pn.rekpiutang, pn.info1 AS rekdiskon,
                   p.info1 AS diskon, pn.replid AS idpenerimaan, IFNULL(p.sumberdana, '***') AS sumberdana
              FROM penerimaanjttcalon p, besarjttcalon b, jbsakad.calonsiswa cs, datapenerimaan pn 
             WHERE p.replid = '$idPembayaran' 
               AND p.idbesarjttcalon = b.replid 
               AND b.idcalon = cs.replid 
               AND b.idpenerimaan = pn.replid";
    //Logger::LogOnce($sql);
    $row = $db->FetchSingleArray($sql);

    $nic = $row['nopendaftaran'];
    $namasiswa = $row['nama'];
    $idjurnal = $row['idjurnal'];
    $tanggal = $row['tanggal'];
    $keterangan = $row['keterangan'];
    $idpenerimaan = $row['idpenerimaan'];
    $namapenerimaan = $row['namapenerimaan'];
    $besar = $row['jumlah'];
    $besardiskon = $row['diskon'];
    $idbesarjtt = $row['idbesarjtt'];
    $besarjtt = $row['besar'];
    $lunas = $row['lunas'];
    $rekkas = $row['rekkas'];
    $rekpiutang = $row['rekpiutang'];
    $rekpendapatan = $row['rekpendapatan'];
    $rekdiskon = $row['rekdiskon'];
    $jdiskon = $row['diskon'];
    $jbayar = $besar;
    $jcicilan = $jbayar + $jdiskon;
    $sumberdana = $row["sumberdana"];
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
    <script language="javascript" src="../script/vldr.js?r=<?= filemtime('../script/vldr.js') ?>"></script>
    <script language="javascript" src="pembayaran.cswjb.bayar.js?r=<?= filemtime('pembayaran.cswjb.bayar.js') ?>"></script>
</head>
<body style="padding: 10px">
<input type="hidden" id="idcalonsiswa" value="<?=$idCalonSiswa?>">
<input type="hidden" id="idpembayaran" value="<?=$idPembayaran?>">
<input type="hidden" id="idpenerimaan" value="<?=$idPenerimaan?>">
<input type="hidden" id="penerimaan" value="<?=$penerimaan?>">
<input type="hidden" id="idtahunbuku" value="<?=$idTahunBuku?>">
<input type="hidden" id="idkategori" value="<?=$idKategori?>">
<input type="hidden" id="idbesarjtt" value="<?=$idBesarJtt?>">
<input type="hidden" id="nic" value="<?=$nic?>">
<input type="hidden" id="nama" value="<?=$nama?>">
<input type="hidden" id="departemen" value="<?=$departemen?>">

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
        <td width="150">Cicilan<?= $tag_mandatory?></td>
        <td width="500">
            <input id="jcicilan" type="text" class="inputbox-money bg-light-blue"
                   style="width: 250px"
                   value="<?= FormatRupiah($jcicilan) ?>"
                   onblur="hitungJumlahBayar(); Rupiah.FormatRupiah('jcicilan')"
                   onfocus="Rupiah.UnformatRupiah('jcicilan')">
        </td>
    </tr>
    <tr>
        <td>Diskon</td>
        <td>
            <input id="jdiskon" type="text" class="inputbox-money bg-light-green"
                   style="width: 250px"
                   value="<?= FormatRupiah($jdiskon) ?>"
                   onblur="hitungJumlahBayar(); Rupiah.FormatRupiah('jdiskon')"
                   onfocus="Rupiah.UnformatRupiah('jdiskon')">
        </td>
    </tr>
    <tr>
        <td>Bayar</td>
        <td>
            <input type="text" id="jbayar"  class="inputbox-money bg-light-gray fw-bold"
                   style="width: 250px"
                   readonly="readonly" value="<?= FormatRupiah($jbayar) ?>"/>
        </td>
    </tr>
    <tr>
        <td>Rek. Kas<?= $tag_mandatory?></td>
        <td>
<?php
            ShowSelectRekKasCsWjb($db);
?>
        </td>
    </tr>
    <tr>
        <td>Sumber Dana<?= $tag_mandatory?></td>
        <td>
<?php
            ShowSelectSumberDanaCsWjb($db);
?>
        </td>
    </tr>
    <tr>
        <td>Keterangan</td>
        <td>
            <textarea rows="3" cols="35" class="inputbox" id="kcicilan"><?=$keterangan?></textarea>
        </td>
    </tr>
<?php
    if ($idPembayaran > 0)
    {   ?>
        <tr>
            <td>Alasan Perubahan Data<?= $tag_mandatory?></td>
            <td>
                <textarea rows="3" cols="35" class="inputbox" id="alasan"><?= $alasan ?></textarea>
            </td>
        </tr>
        <?php
    }
?>
    <tr>
        <td>Notifikasi</td>
        <td>
            <?php   $checked = ($sendnotif == 1) ? "checked" : "";  ?>
            <input type="checkbox" id="sendnotif" <?= $checked ?>> kirim ke Jendela Sekolah | Telegram | SMS
        </td>
    </tr>
    <tr>
        <td colspan="2" align="center">
            <br>
            <input type="button" id="btSimpan" class="dialogButtonPositive" value="Simpan" onclick="simpanBayarCsWjb()">
            <input type="button" class="dialogButtonNegative" value="Tutup" onclick="window.close()">
            <br><br>
            <span id="spInfo"></span>
        </td>
    </tr>
</table>
</body>
</html>