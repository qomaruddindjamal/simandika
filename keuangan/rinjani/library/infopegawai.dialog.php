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
require_once('../include/config.php');
require_once('../library/msg.php');
require_once('../include/db.onpage.php');
require_once('../library/userinfo.php');

if (!isset($_REQUEST["nip"]))
    exit();

$nip = $_REQUEST["nip"];
$sql = "SELECT p.nip, p.nama, p.bagian, p.gelarawal, p.gelarakhir,
               IF(p.foto IS NULL, 0, 1) AS fotoexist, IF(p.foto IS NULL, '', TO_BASE64(p.foto)) as foto64,
               p.panggilan, p.agama, p.kelamin, p.tmplahir, DATE_FORMAT(p.tgllahir, '%d %M %Y') AS ftgllahir,
               IFNULL(p.alamat, '-') AS falamat, IFNULL(p.telpon, '-') AS ftelpon, 
               IFNULL(p.handphone, '-') AS fhandphone, IFNULL(p.email, '-') AS femail
          FROM jbssdm.pegawai p
         WHERE p.nip = '$nip'";
OpenDb();
$res = QueryDb($sql);
if (mysqli_num_rows($res) == 0)
{
    CloseDb();
    echo "Tidak ditemukan data pegawai dengan NIP $nip";
    exit();
}

$row = mysqli_fetch_array($res);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Informasi Pegawai</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
</head>
<body>

<table border="0" width="100%" cellpadding="5">
<tr><td align="left" valign="top">

    <table border="0" width="100%">
    <tr>
        <td width="130">
<?php       $userFoto = $row["fotoexist"] == 1 ? $row["foto64"] : UserInfo::$DefaultFoto; ?>
            <img style="width: 100px; height: 100px;" class="avatar-circle"
                 src="data:image/jpg;base64,<?= $userFoto ?>">
        </td>
        <td>
            <span style="font-family: 'Segoe UI', serif; font-size: 24px; color: #333; font-weight: bold">
                <?= $row["nama"] ?>
            </span><br>
            <span style="font-family: 'Segoe UI', serif; font-size: 18px; color: #333;">
                <?= $row["nip"] ?>
            </span><br>
            <span style="font-family: 'Segoe UI', serif; font-size: 12px; color: #666;">
                <?= $row["bagian"] ?>
            </span>&nbsp;&nbsp;
        </td>
    </tr>
    </table>
    <br>

    <table border="0" cellpadding="2">
    <tr>
        <td width="400">
            <span style="font-size: 12px; color: #999">Lengkap</span><br>
            <span style="font-size: 18px; color: #333"><?= $row["gelardepan"] . " " . $row["nama"] . " " . $row["gelarakhir"] ?></span>
        </td>
        <td width="200">
            <span style="font-size: 12px; color: #999">Panggilan</span><br>
            <span style="font-size: 18px; color: #333"><?=$row["panggilan"]?></span>
        </td>
    </tr>
    </table>

    <table border="0" cellpadding="2">
    <tr>
        <td width="400">
            <span style="font-size: 12px; color: #999">Tempat dan Tanggal Lahir</span><br>
            <span style="font-size: 18px; color: #333"><?= $row["tmplahir"] . ", " . $row["ftgllahir"] ?></span>
        </td>
        <td width="150">
            <span style="font-size: 12px; color: #999">Kelamin</span><br>
            <span style="font-size: 18px; color: #333">
<?php           if (strtoupper($row["kelamin"]) == "P")
                    echo "Perempuan";
                else
                    echo "Laki-laki";   ?>
            </span>
        </td>
        <td width="150">
            <span style="font-size: 12px; color: #999">Agama</span><br>
            <span style="font-size: 18px; color: #333"><?=$row["agama"]?></span>
        </td>
    </tr>
    </table>

    <br>
    <table border="0" cellpadding="2">
    <tr>
        <td width="200">
            <span style="font-size: 12px; color: #999">Handphone</span><br>
            <span style="font-size: 18px; color: #333"><?= $row["fhandphone"]  ?></span>
        </td>
        <td width="250">
            <span style="font-size: 12px; color: #999">Email</span><br>
            <span style="font-size: 18px; color: #333"><?= $row["femail"]  ?></span>
        </td>
    </tr>
    </table>

    <br>
    <table border="0" cellpadding="2">
    <tr>
        <td width="550">
            <span style="font-size: 12px; color: #999">Alamat</span><br>
            <span style="font-size: 18px; color: #333">
                <?= $row["falamat"] ?>
            </span>
        </td>
    </tr>
    </table>

    </td>
</tr>
</table>
</body>
</html>
