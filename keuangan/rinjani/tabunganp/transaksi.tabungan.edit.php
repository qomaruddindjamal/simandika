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
require_once('../library/msg.php');
require_once('../library/rupiah.php');
require_once('../util/peek.php');
require_once('transaksi.tabungan.edit.func.php');
require_once('transaksi.tabungan.func.php');

$db = new Db();
$db->TryOpenExit();

$idpembayaran = $_REQUEST["idpembayaran"];
LoadValues($db);

$title = ($action == "setor") ? "Setoran" : "Penarikan";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Ubah <?= $title ?> Tabungan</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js?<?=filemtime('../style/tables.css')?>"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/rupiah3.js"></script>
    <script language="javascript" src="transaksi.tabungan.edit.js?r=<?= filemtime('transaksi.tabungan.edit.js') ?>"></script>
</head>
<body style="padding: 10px">
<span class="dialogTitle">Ubah <?= $title ?> Tabungan</span><br><br>

<input type="hidden" id="action" value="<?=$action?>">
<input type="hidden" id="nip" value="<?=$nip?>">
<input type="hidden" id="debet" value="<?=$debet?>">
<input type="hidden" id="kredit" value="<?=$kredit?>">
<input type="hidden" id="idjurnal" value="<?=$idjurnal?>">
<input type="hidden" id="idtabungan" value="<?=$idtabungan?>">
<input type="hidden" id="rekkastrans" value="<?=$rekkastrans?>">
<input type="hidden" id="rekutang" value="<?=$rekutang?>">
<input type="hidden" id="idpembayaran" value="<?=$idpembayaran ?>">
<input type="hidden" id="deflokasidana" value="<?=$lokasidana?>">

<table border="0" cellpadding="5" cellspacing="0" width="100%">
<tr style="height: 30px;">
    <td width="150">Tabungan</td>
    <td width="600"><b><?=$namatabungan?></b></td>
</tr>
<tr>
    <td>Pegawai</td>
    <td>
        <b><?="$namapegawai ($nip)"?></b>
    </td>
</tr>
<tr>
    <td>
        <?= $title ?>
        <?= $tag_mandatory ?>
    </td>
    <td>
        <input type="text" class="inputbox-money fw-bold" style="width: 200px;" maxlength="16"
               id="jbayar" value="<?= FormatRupiah($jbayar) ?>"
               onblur="Rupiah.FormatRupiah('jbayar')" onfocus="Rupiah.UnformatRupiah('jbayar')">
    </td>
</tr>
<?php
if ($action == "setor")
{
    echo "<tr>";
    echo "<td>Sumber Dana</td>";
    echo "<td>";
    ShowSelectSumberDanaTabunganPegawai($db);
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Penyimpanan<br><i><span style='color: #999'>(lokasi dana)</span></i></td>";
    echo "<td>";
    ShowSelectLokasiDanaTabunganPegawai($db, 'lokasidana');
    echo "</td>";
    echo "</tr>";
}
else
{
    echo "<tr>";
    echo "<td>Pengambilan<br><i><span style='color: #999'>(lokasi dana)</span></i></td>";
    echo "<td>";
    echo "<input type='hidden' id='sumberdana' value='***'>";
    echo "<span id='spPengambilan'>";
    ShowSelectLokasiPengambilanTabunganPegawai($db, $nip, $idtabungan, $lokasidana);
    echo "</span>";
    echo "</td>";
    echo "</tr>";
}
?>
<tr>
    <td valign="top">Keterangan</td>
    <td>
        <textarea id="keterangan" rows="2" cols="28" class="inputbox"><?=$keterangan ?></textarea>
    </td>
</tr>
<tr>
    <td valign="top">Alasan Perubahan Data<?= $tag_mandatory ?></td>
    <td>
        <textarea id="alasan" rows="2" cols="28" class="inputbox"></textarea>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <br>
        <input type="button" class="dialogButtonPositive" id="btSimpan" value="Simpan" onclick="simpanEditTabungan()">
        <input type="button" class="dialogButtonNegative" id="btTutup" value="Tutup" onclick="window.close()"><br>
        <span id="spInfo" style="color: blue"></span>
    </td>
</tr>
</table>

</body>
</html>
