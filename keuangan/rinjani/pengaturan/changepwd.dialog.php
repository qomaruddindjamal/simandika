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
require_once('changepwd.dialog.func.php');

$db = new Db();
$db->TryOpenExit();

$login = getIdUser();

$nip = "";
$nama = "";
if ($login == 'landlord' || $login == 'LANDLORD')
{
    $nip = "";
    $nama = "Administator";
}
else
{
    $sql = "SELECT p.nip, p.nama 
              FROM jbssdm.pegawai p 
             WHERE p.nip = '$login'";
    $result = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($result))
    {
        $nip = $row[0];
        $nama = $row[1];
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Ganti Password</title>
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
    <script language="javascript" src="changepwd.dialog.js?r=<?= filemtime('changepwd.dialog.js') ?>"></script>
</head>
<body style="padding: 10px">
<span class="dialogTitle">Ganti Password</span><br><br>

<table cellpadding="5" cellspacing="0">
<tr style="height: 30px;">
    <td style="width: 90px;">Login<?=$tag_mandatory?></td>
    <td>
        <input type="hidden" id="login" value="<?=$login?>">
        <input type="hidden" id="nip" value="<?=$nip?>">
        <input type="text" size="23" id="nama" value="<?=$nama?>" class="inputbox" style="background-color: #ededed" readonly>
    </td>
</tr>
<tr>
    <td>Password Lama<?=$tag_mandatory?></td>
    <td>
        <input type="password" style="width: 165px" maxlength="100" id="passwordlama" class="inputbox">
    </td>
</tr>
<tr>
    <td>Password Baru<?=$tag_mandatory?></td>
    <td>
        <input type="password" style="width: 165px" maxlength="100" id="passwordbaru" class="inputbox">
    </td>
</tr>
<tr>
    <td>Konfirmasi<?=$tag_mandatory?></td>
    <td>
        <input type="password" style="width: 165px" maxlength="100" id="konfirmasi" class="inputbox">
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <br>
        <input type="button" class="dialogButtonPositive" value="Ganti Password" id="btSimpan" onclick="simpan()">
        <input type="button" class="dialogButtonNegative" value="Tutup" id="btTutup" onclick="window.close()">
        <br>
        <span id="spInfo"></span>
    </td>
</tr>
</table>

</body>
</html>
