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
require_once('../include/errorhandler.php');
require_once('autotrans2.setting.manage.func.php');

if (getLevel() == 2)
{ ?>
    <script language="javascript">
        alert('Maaf, anda tidak berhak mengakses halaman ini!');
        document.location.href = "penerimaan.php";
    </script>
    <?php 	exit();
} // end if

$departemen = $_REQUEST["departemen"];
$idAutoTrans = $_REQUEST["idautotrans"];

$title = $idAutoTrans == 0 ? "Pengaturan Batch Payment " : "Ubah Pengaturan Batch Payment";

OpenDb();

$kelompok = 1;
$smsinfo = 0;
if ($idAutoTrans != 0)
{
    $sql = "SELECT judul, urutan, keterangan, kelompok, smsinfo
              FROM jbsfina.autotrans
             WHERE replid = $idAutoTrans";
    $res = QueryDb($sql);
    $row = mysqli_fetch_row($res);
    $judul = $row[0];
    $urutan = $row[1];
    $keterangan = $row[2];
    $kelompok = $row[3];
    $smsinfo = $row[4];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="../style/style.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$title?></title>
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
    <script language="javascript" src="autotrans2.setting.manage.js?<?=filemtime('autotrans2.setting.manage.js')?>"></script>
</head>

<body>

<table border="0" cellpadding="10" width="100%">
<tr><td>

<form name="main" action="autotrans2.setting.manage.save.php" onsubmit="return saveForm()">
<span style="font-size: 18px"><?=$title?></span><br><br>
<input type="hidden" id="idautotrans" name="idautotrans" value="<?=$idAutoTrans?>">
<input type="hidden" id="departemen" name="departemen" value="<?=$departemen?>">
<input type="hidden" id="lsPenerimaan" name="lsPenerimaan" value="">
<table border="0" cellpadding="2" cellspacing="0" width="100%" height="100%">
<tr>
    <td width="100">Departemen:</td>
    <td><input type="text" id="dept" name="dept" class="inputbox inputbox-readonly" value="<?=$departemen?>" readonly style="width: 300px; font-size: 12px;"></td>
</tr>
<tr>
    <td>Judul:<?=$tag_mandatory?></td>
    <td><input type="text" id="judul" name="judul" class="inputbox" value="<?=$judul?>" style="width: 300px; font-size: 12px;"></td>
</tr>
<tr>
    <td>Kelompok:<?=$tag_mandatory?></td>
    <td>
        <select id="kelompok" name="kelompok" class="inputbox">
            <option value="1" <?= $kelompok == 1 ? "selected" : "" ?>>Siswa</option>
            <option value="2" <?= $kelompok == 2 ? "selected" : "" ?>>Calon Siswa</option>
        </select>
    </td>
</tr>
<tr>
    <td>Urutan:<?=$tag_mandatory?></td>
    <td><input type="text" id="urutan" name="urutan" class="inputbox" value="<?=$urutan?>" style="width: 50px; font-size: 12px;"></td>
</tr>
<tr>
    <td valign="top">Keterangan:</td>
    <td><textarea id="keterangan" name="keterangan" class="inputbox" rows="3" cols="45"><?=$keterangan?></textarea></td>
</tr>
<tr>
    <td>Notifikasi:</td>
    <td align="left">
        <input type="checkbox" id="smsinfo" name="smsinfo" class="inputbox" <?= $smsinfo == 1 ? "checked" : "" ?>>
        kirim ke Jendela Sekolah | Telegram | SMS
    </td>
</tr>
</table>

<br>
<a href="#" onclick="tambahPenerimaan()"><img src="../images/ico/tambah.png" title="tambah">&nbsp;pilih transaksi penerimaan</a><br><br>
<table id="tabDaftar" class="tab" border="1" cellpadding="5" style="border-collapse: collapse; border-width: 1px;" cellspacing="0" width="100%" height="100%">
<thead>
<tr style="height: 30px;">
    <td class="header" width="30" align="center">No</td>
    <td class="header" width="220">Penerimaan</td>
    <td class="header" width="120" align="right">Besar Cicilan</td>
    <td class="header" width="*">Keterangan</td>
    <td class="header" width="60" align="center">Urutan</td>
    <td class="header" width="60" align="center">Aktif</td>
    <td class="header" width="60">&nbsp;</td>
</tr>
</thead>
<tbody>
<?php
if ($idAutoTrans == 0)
{
    echo "<tr height='70' class='no-row'>";
    echo "<td colspan='7' align='center' valign='middle'><i>pilih transaksi penerimaan</td>";
    echo "</tr>";
}
else
{
    $sql = "SELECT ad.replid, ad.idpenerimaan, dp.nama, ad.besar, ad.aktif, ad.urutan, ad.keterangan
              FROM jbsfina.autotransdata ad, jbsfina.datapenerimaan dp
             WHERE ad.idpenerimaan = dp.replid
               AND ad.idautotrans = $idAutoTrans
             ORDER BY ad.urutan";
    $res = QueryDb($sql);
    $ix = 0;
    while($row = mysqli_fetch_array($res))
    {
        $imgAktif = $row["aktif"] == 1 ? "../images/ico/aktif.png" : "../images/ico/nonaktif.png";

        AddAutoTransData($row["replid"], $row["idpenerimaan"], $row["aktif"], $row["besar"], $row["urutan"], $row["keterangan"]);
        ?>
        <tr id='tabDaftarRow-<?=$ix?>' style='height: 25px'>
            <td align='center' class="numberColumn"><?=$ix + 1?></td>
            <td align='left'><?=$row['nama']?></td>
            <td align='right'><?=FormatRupiah($row['besar'])?></td>
            <td align='left'><?=$row["keterangan"]?></td>
            <td align='center'><?=$row["urutan"]?></td>
            <td align='center'><a onclick='setAktif(<?=$ix?>)' style='cursor: pointer'><img id='imgAktif-<?=$ix?>' src='<?=$imgAktif?>'></a></td>
            <td align='center'><a onclick='hapusData(<?=$ix?>)' style='cursor: pointer'><img src='../images/ico/hapus.png' title='hapus'></a></td>
        </tr>
<?php
        $ix += 1;
    }

    $json = json_encode($lsAutoTransData);
    echo "<input type='hidden' id='jsonData' value='$json'>";
}
?>
</tbody>
</table>

<br><br>
<div style="text-align: center">
    <input type="submit" class="dialogButtonPositive" value="Simpan" style="height: 30px; width: 80px;">&nbsp;
    <input type="button" class="dialogButtonNegative" value="Tutup" style="height: 30px; width: 80px;" onclick="tutup()">
</div>
</form>

</td></tr>
</table>

</body>
</html>
<?php
if ($idAutoTrans != 0) {
    ?>
    <script language="JavaScript">
        var jsonData = $("#jsonData").val();
        lsPenerimaan = JSON.parse(jsonData);
    </script>
    <?php
}
?>
<?php
CloseDb();
?>
