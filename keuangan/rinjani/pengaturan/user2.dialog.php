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
require_once('../library/logger.php');
require_once('../include/config.php');
require_once('../include/db.onfunc.php');
require_once('user2.dialog.func.php');

$db = new Db();
$db->TryOpenExit();

$idUser = RequestData("iduser", 0);

$nip = "";
$nama = "";
$departemen = "";
$orig_departemen = "";
$status_user = "";
$keterangan = "";

$title = "Tambah Pengguna";
if ($idUser != 0)
{
    $title = "Ubah Pengguna";

    LoadValues($db, $idUser);
    $orig_departemen = $departemen;
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
    <script language="javascript" src="../script/tables.js?<?=filemtime('../style/tables.css')?>"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="user2.dialog.js?r=<?= filemtime('user2.dialog.js') ?>"></script>
</head>
<body style="padding: 10px">
<span class="dialogTitle"><?=$title?></span><br><br>
<input type="hidden" id="iduser" value="<?=$idUser?>">
<input type="hidden" id="orig_departemen" value="<?=$departemen?>">

<table cellpadding="5" cellspacing="0">
<tr style="height: 30px;">
    <td style="width: 60px;">Login<?=$tag_mandatory?></td>
    <td>
        <input type="text" size="12" id="nip" value="<?=$nip?>" class="inputbox" style="background-color: #ededed" readonly>
        <input type="text" size="23" id="nama" value="<?=$nama?>" class="inputbox" style="background-color: #ededed" readonly>
<?php   if ($idUser == 0) { ?>
        <input type="button" class="but" value="(..)" style="height: 28px;" onclick="openSearchPegawai()">
        <input type="hidden" id="haslogin" value="0">
<?php   } else { ?>
        <input type="hidden" id="haslogin" value="1">
<?php   } ?>
    </td>
</tr>
<tr id="trPassword" style="display: none;">
    <td>Password<?=$tag_mandatory?></td>
    <td>
        <input type="password" style="width: 165px" maxlength="100" id="password" class="inputbox">
    </td>
</tr>
<tr id="trKonfirmasi" style="display: none;">
    <td>Konfirmasi<?=$tag_mandatory?></td>
    <td>
        <input type="password" style="width: 165px" maxlength="100" id="konfirmasi" class="inputbox">
    </td>
</tr>
<tr>
    <td>Tingkat<?=$tag_mandatory?></td>
    <td>
        <select id="status_user" class="inputbox" style="width:165px" onChange="change_tingkat();">
            <option value="1" <?=IntIsSelected($status_user, 1) ?> >Manajer Keuangan</option>
            <option value="2" <?=IntIsSelected($status_user, 2) ?> >Staf Keuangan</option>
        </select>
    </td>
</tr>
<tr>
    <td>Departemen<?=$tag_mandatory?></td>
    <td>
        <span id="spDepartemen">
<?php   ShowSelectDepartemenDaftarPengguna($db) ?>
        </span>
    </td>
</tr>
<tr>
    <td>Keterangan</td>
    <td>
        <textarea rows="3" cols="40" class="inputbox" id="keterangan"><?=$keterangan?></textarea>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <br>
        <input type="button" class="dialogButtonPositive" value="Simpan" id="btSimpan" onclick="simpan()">
        <input type="button" class="dialogButtonNegative" value="Tutup" id="btTutup" onclick="window.close()">
        <br>
        <span id="spInfo"></span>
    </td>
</tr>
</table>

</body>
</html>
