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
require_once("../library/msg.php");
require_once("../library/rupiah.php");
require_once("../util/peek.php");
require_once('../include/db.onpage.php');
require_once('../include/db.onfunc.php');
require_once('laporan.rincian.edit.func.php');

$db = new Db();
$db->TryOpenExit();

$idtransaksi = $_REQUEST["idtransaksi"];
$departemen = $_REQUEST["departemen"];

$sql = "SELECT idpengeluaran, keperluan, keterangan, namapemohon, penerima, 
               date_format(tanggal, '%Y-%m-%d') AS tanggal, jumlah, idjurnal 
          FROM jbsfina.pengeluaran 
         WHERE replid = '$idtransaksi'";
$result = $db->QueryDb($sql);
$row = mysqli_fetch_array($result);

$idpengeluaran = $row['idpengeluaran'];
$keperluan = $row['keperluan'];
$keterangan = $row['keterangan'];
$penerima = $row['penerima'];
$pemohon = $row['namapemohon'];
$tanggal = $row['tanggal'];
$jumlah = $row['jumlah'];
$idjurnal = $row['idjurnal'];

$rekdebet = "";
$sql = "SELECT koderek
          FROM jbsfina.jurnaldetail
         WHERE idjurnal = $idjurnal
           AND kredit = 0
           AND debet <> 0";
$result = $db->QueryDb($sql);
if ($row = mysqli_fetch_row($result))
    $rekdebet = $row[0];

$rekkredit = "";
$sql = "SELECT koderek
          FROM jbsfina.jurnaldetail
         WHERE idjurnal = $idjurnal
           AND debet = 0
           AND kredit <> 0";
$result = $db->QueryDb($sql);
if ($row = mysqli_fetch_row($result))
    $rekkredit = $row[0];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Ubah Pengeluaran</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js?<?=filemtime('../style/tables.css')?>"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/rupiah3.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/dialogbox.js"></script>
    <script language="javascript" src="laporan.rincian.edit.js?r=<?= filemtime('laporan.rincian.edit.js') ?>"></script>
</head>
<body style="padding: 10px">
<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idtransaksi" value="<?=$idtransaksi?>">
<input type="hidden" id="idjurnal" value="<?=$idjurnal?>">
<span style="font-size: 18px">Ubah Pengeluaran</span><br><br>

<table border="0" cellpadding="5" cellspacing="0">
<tr>
    <td width="60">Jumlah:<?= $tag_mandatory ?></td>
    <td>
        <input type="text" class="inputbox-money fw-bold bg-light-blue" id="jumlah" value="<?= FormatRupiah($jumlah) ?>" style="width: 180px" maxlength="18"
               onblur="Rupiah.FormatRupiah('jumlah')"
               onfocus="Rupiah.UnformatRupiah('jumlah')">
    </td>
</tr>
<tr>
    <td>Keperluan:<?= $tag_mandatory ?></td>
    <td>
        <input type="text" class="inputbox" id="keperluan" style="width: 300px" value="<?= $keperluan ?>" maxlength="255">
    </td>
</tr>
<tr>
    <td>Tanggal:<?= $tag_mandatory ?></td>
    <td>
        <input type="text" id="txTglJurnal" readonly size="15"
               value="<?= LongDateFormat($tanggal) ?>"
               onclick="showPilihTanggal()"
               class="inputbox" style="background-color:#ddd; width: 150px;">&nbsp;
        <input type="hidden" id="tglJurnal" value="<?= $tanggal ?>">
        <a href="#" onclick="showPilihTanggal()">
            <img src="../images/ico/calendar.png" border="0" id="bttutup"/>
        </a>
    </td>
</tr>
<tr>
    <td>Sumber Dana:<?= $tag_mandatory ?></td>
    <td>
        <input type="hidden" id="rekbeban" value="<?=$rekdebet?>">
        <select id="rekkas" class="inputbox" style="width: 250px">
<?php   $sql = "SELECT kode, nama
                  FROM jbsfina.rekakun
                 WHERE kategori = 'HARTA' 
                 ORDER BY kode";
        $res = $db->QueryDb($sql);
        while($row = mysqli_fetch_row($res))
        {
            $sel = $rekkredit == $row[0] ? "selected" : "";
            echo "<option value='$row[0]' $sel>$row[0] $row[1]</option>";
        }   ?>
        </select>
    </td>
</tr>
<tr>
    <td>Pengguna:<?= $tag_mandatory ?></td>
    <td>
        <input type="text" class="inputbox" id="pengguna" style="width: 180px" value="<?= $pemohon ?>" maxlength="100">
        <input type="button" class="but" value="(..)" style="height: 28px" onclick="showSelectPengguna()">
    </td>
</tr>
<tr>
    <td>Penerima:</td>
    <td>
        <input type="text" class="inputbox" id="penerima" style="width: 180px" value="<?= $penerima ?>" maxlength="100">
        <input type="button" class="but" value="(..)" style="height: 28px" onclick="showSelectPenerima()">
    </td>
</tr>
<tr>
    <td>Keterangan:</td>
    <td>
        <textarea rows="2" cols="40" class="inputbox" id="keterangan"><?= $keterangan ?></textarea>
    </td>
</tr>
<tr>
    <td>Alasan Perubahan Data:<?= $tag_mandatory ?></td>
    <td>
        <textarea rows="2" cols="40" class="inputbox" id="alasan"></textarea>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <input type="button" id="btSimpan" class="dialogButtonPositive" style="width: 80px; height: 30px" value="Simpan" onclick="simpan()" >
        <input type="button" class="dialogButtonNegative" style="width: 80px; height: 30px" value="Tutup" onclick="window.close()" >
    </td>
</tr>
</table>

<div id="divDialog"></div>

</body>
</html>
