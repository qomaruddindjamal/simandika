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
require_once('../library/logger.php');
require_once('../library/rupiah.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('inputjurnal2.func.php');

if (getLevel() == 2)
{
    echo "<script>";
    echo "alert('Maaf, anda tidak berhak mengakses halaman ini!');";
    echo "window.history.back();";
    echo "</script>";
    exit();
}

$db = new Db;
$db->TryOpenExit(true);

$MAX_INPUT_JURNAL = 15;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Input Jurnal</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/stringutil.js"></script>
    <script language="javascript" src="../script/dateutil.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/rupiah3.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="inputjurnal2.js?r=<?=filemtime('inputjurnal2.js')?>"></script>
</head>
<body style="margin: 0">
<div id="toast-container"></div>
<table border="0" width="100%" align="center">
<tr>
    <td align="left" valign="top">

    <table border="0" width="95%" align="center">
    <tr>
        <td align="right">
            <img class="help-icon-1"  src="../images/help32.png" title="bantuan" onclick="showHelp()">
            <span class="pageTitle">Input Jurnal Umum</span><br>
            <a class="pageLink" href="jurnalumum.php">Jurnal</a>&nbsp&gt;&nbsp
            <span class="pageLinkCurrent">Input Jurnal Umum</span>
        </td>
    </table>
    <br />

    <table border="0" cellpadding="0" cellspacing="0" style="margin-left: 50px">
    <tr height="35">
        <td width="100">
            <b>Departemen:</b>
        </td>
        <td width="*">
            <?php
            $departemen = isset($_REQUEST["departemen"]) ? $_REQUEST["departemen"] : "";
            ShowSelectDepartemenInputJurnal($db);
            ?>
        </td>
    </tr>
    <tr height="35">
        <td width="100">
            <b>Tahun Buku:</b>
        </td>
        <td width="*">
            <div id="dvTahunBuku">
                <?php
                ShowTahunBukuInputJurnal($db);
                ?>
            </div>
        </td>
    </tr>
    <tr height="35">
        <td width="100">
            <b>Tanggal:</b>
        </td>
        <td width="*">
            <input type="text" id="txTglJurnal" readonly size="15"
                   value="<?= LongDateFormat(date("Y-m-d")) ?>"
                   onclick="showPilihTanggal()"
                   class="inputbox" style="background-color:#ddd; width: 150px;">&nbsp;
            <input type="hidden" id="tglJurnal" value="<?= date("Y-m-d") ?>">
            <a href="#" onclick="showPilihTanggal()">
                <img src="../images/ico/calendar.png" border="0" id="bttutup"/>
            </a>
        </td>
    </tr>
    <tr height="35">
        <td width="100">
            <b>Keperluan:</b>
        </td>
        <td width="*">
            <textarea rows="3" cols="50" id="keperluan" class="inputbox"></textarea>
        </td>
    </tr>
    <tr height="35">
        <td width="100">
            Keterangan:
        </td>
        <td width="*">
            <textarea rows="3" cols="50" id="keterangan" class="inputbox"></textarea>
        </td>
    </tr>
    </table>
    <br><br>

    <input type="hidden" id="maxInputJurnal" value="<?= $MAX_INPUT_JURNAL ?>">
    <table border="0" id="table" cellpadding="0" cellspacing="0" align="left" style="margin-left: 50px">
    <tr>
        <td class="header" width="50" align="center">No</td>
        <td class="header" width="500">Rekening</td>
        <td class="header" width="200" align="right">Debet</td>
        <td class="header" width="200" align="right">Kredit</td>
    </tr>
<?php
    for($i = 1; $i <= $MAX_INPUT_JURNAL; $i++)
    {   ?>
        <tr height="32">
            <td class="numberColumn" align="center"><?= $i ?></td>
            <td>
                <input type="text" id="koderek<?= $i ?>" onclick="pilihrek(<?= $i ?>)" class="inputbox" style="background-color: #efefef; width: 120px;" readonly>
                <input type="text" id="namarek<?= $i ?>" onclick="pilihrek(<?= $i ?>)" class="inputbox" style="background-color: #efefef; width: 300px;" readonly>
                <img src="../images/ico/lihat.png" style="cursor: pointer" border="0" title="cari rekening" onclick="pilihrek(<?= $i ?>)" />
                <img src="../images/ico/hapus.png" style="cursor: pointer" border="0" title="hapus" onclick="hapusrek(<?= $i ?>)" />
            </td>
            <td align="right">
                <input type="text" id="debet<?= $i ?>" class="inputbox-money fw-bold"
                       onblur="Rupiah.FormatRupiah('debet<?= $i ?>'); hitungJumlah('debet', <?= $i ?>)"
                       onfocus="Rupiah.UnformatRupiah('debet<?= $i ?>')"
                       style="width: 200px; text-align: right">
            </td>
            <td align="right">
                <input type="text" id="kredit<?= $i ?>" class="inputbox-money fw-bold"
                       onblur="Rupiah.FormatRupiah('kredit<?= $i ?>'); hitungJumlah('kredit', <?= $i ?>)"
                       onfocus="Rupiah.UnformatRupiah('kredit<?= $i ?>')"
                       style="width: 200px; text-align: right">
            </td>
        </tr>
<?php
    }
?>
    <tr height="50">
        <td colspan="2" align="right" style="background-color: #dedede">
            <span style="font-size: 14px; font-weight: bold; margin-right: 10px;">T O T A L</span>
            <input type="hidden" id="totalstatus" value="0">
        </td>
        <td align="right" style="background-color: #dedede">
            <input type="text" id="totaldebet" class="inputbox-money fw-bold" readonly
                   onblur="Rupiah.FormatRupiah('totaldebet')"
                   onfocus="Rupiah.UnformatRupiah('totaldebet')"
                   style="width: 200px; text-align: right; background-color: #efefef;">
        </td>
        <td align="right" style="background-color: #dedede">
            <input type="text" id="totalkredit" class="inputbox-money fw-bold" readonly
                   onblur="Rupiah.FormatRupiah('totalkredit')"
                   onfocus="Rupiah.UnformatRupiah('totalkredit')"
                   style="width: 200px; text-align: right; background-color: #efefef;">
        </td>
    </tr>
    </table>

    </td>
</tr>
</table>
<br>

<div>
    <input type="button" class="dialogButtonPositive" style="margin-left: 50px; width: 80px; height: 30px;"
           id="btSimpan"
           value="Simpan" onclick="simpanJurmalUmum()">
    <span id="spInfo"></span>
</div>
<?php
if (isset($_SESSION["state"]))
{
    $state = $_SESSION["state"];
    if ($state == "InputJurnalSuccess")
    {
        unset($_SESSION["state"]);

        echo "<script type='application/javascript'>";
        echo "$(document).ready(function () {";
        echo "showToast('Tersimpan', 2000, 'success', 'top');";
        echo "});";
        echo "</script>";
    }
}
?>

<div id="divHelpDialog" class="help-dialog"></div>
</body>
</html>

