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
require_once('../include/sessionchecker.php');
require_once('../include/sessioninfo.php');
require_once('../library/common.func.php');
require_once('../include/config.php');
require_once('../library/rupiah.php');
require_once('../include/db.onfunc.php');
require_once('../library/departemen.php');
require_once('../include/errorhandler.php');

$db = new Db();
$db->TryOpenExit();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Konfigurasi SchoolPay</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="userpos.js?r=<?=filemtime('userpos.js')?>"></script>
</head>

<body>

<table border="0" width="95%" align="center">
<tr>
    <td align="right" valign="top">

        <img class="help-icon-1" src="../images/help32.png" title="bantuan" onclick="showHelp()">
        <span class="pageTitle">Staf Vendor</span><br>
        <a class="pageLink" href="schoolpay.php"><b>SchoolPay</b></a>&nbsp;&gt;&nbsp;
        <span class="pageLinkCurrent">Staf Vendor

    </td>
</tr>
</table>
<br>

<table border="0" width="100%" align="center">
<tr>
    <td align="center" valign="top" width="*">

        <div style="width: 1200px; text-align: right">
            <a href="#" onclick="location.reload();"><img src="../images/ico/refresh.png" border="0">&nbsp;refresh</a>&nbsp;&nbsp;
<?php
            if (getLevel() != 2) { ?>
                <a href="#" onclick="tambahUser()"><img src="../images/ico/tambah.png" border="0">&nbsp;tambah staf</a>
<?php       } ?>
        </div>

        <table id="table" class="tab" border="1" cellpadding="5" style="border-width: 1px; border-collapse: collapse; border-color: #dddddd">
        <tr style="height: 30px">
            <td class="header" width="30" align="center">No</td>
            <td class="header" width="150" align="center">User Id</td>
            <td class="header" width="200" align="center">User Name</td>
            <td class="header" width="200" align="center">Vendor</td>
            <td class="header" width="200" align="center">Login Terakhir</td>
            <td class="header" width="100" align="center">Status</td>
            <td class="header" width="180" align="center">Keterangan</td>
            <td class="header" width="80">&nbsp</td>
        </tr>
<?php
        $no = 0;
        $sql = "SELECT * FROM jbsfina.userpos ORDER BY nama";
        $res = $db->QueryDb($sql);
        while($row = mysqli_fetch_array($res))
        {
            $userReplid = $row["replid"];
            $userId = $row["userid"];

            $cnt = 0;
            $vendorList = "";
            $sql = "SELECT v.nama, vu.tingkat 
                      FROM jbsfina.vendor v, jbsfina.vendoruser vu
                     WHERE vu.vendorid = v.vendorid
                       AND vu.userid = '$userId'
                     ORDER BY v.nama";
            $res2 = $db->QueryDb($sql);
            while($row2 = mysqli_fetch_row($res2))
            {
                $cnt += 1;

                if ($vendorList <> "")
                    $vendorList .= "<br>";

                $vendorList .= "$cnt. $row2[0]";
                if ($row2[1] == 1)
                    $vendorList .= " (Manager)";
                else
                    $vendorList .= " (Operator)";
            }

            if ($vendorList == "")
                $vendorList = "(belum ada data vendor)";

            $lastLogin = "(belum pernah login)";

            $sql = "SELECT DATE_FORMAT(ul.logtime, '%d-%b-%Y %H:%i') AS logtime, ul.localip, ul.device, v.nama AS vendor
                      FROM jbsfina.userposlog ul, jbsfina.vendor v 
                     WHERE ul.vendorid = v.vendorid
                       AND userid = '$userId'
                     ORDER BY ul.logtime DESC
                     LIMIT 1";
            $res2 = $db->QueryDb($sql);
            if ($row2 = mysqli_fetch_row($res2))
                $lastLogin = "$row2[0]<br>IP: $row2[1]<br>DEV: $row2[2]<br>VENDOR: $row2[3]";

            if (getLevel() != 2)
            {
                $aktif = "<a href='#' onclick='setUserAktif($userReplid, 1)'><img src='../images/ico/nonaktif.png' border='0' title='set aktif'></a>";
                if ($row["aktif"] == 1)
                    $aktif = "<a href='#' onclick='setUserAktif($userReplid, 0)'><img src='../images/ico/aktif.png' border='0' title='set non aktif'></a>";
            }
            else
            {
                $aktif = "<img src='../images/ico/nonaktif.png' border='0' title='set aktif'>";
                if ($row["aktif"] == 1)
                    $aktif = "<img src='../images/ico/aktif.png' border='0' title='set non aktif'>";
            }

            $no += 1;
            ?>
            <tr>
                <td align="center" valign="top" class="numberColumn"><?=$no?></td>
                <td align="left" valign="top"><?=$userId?></td>
                <td align="left" valign="top"><?=$row["nama"]?></td>
                <td align="left" valign="top"><?=$vendorList?></td>
                <td align="left" valign="top"><?=$lastLogin?></td>
                <td align="center" valign="top">
                    <span id="spAktif<?=$userReplid?>">
                        <?=$aktif?>
                    </span>
                </td>
                <td align="left" valign="top"><?=$row["keterangan"]?></td>
                <td align="center" valign="top">
<?php           if (getLevel() != 2) { ?>
                    <a href="#" onclick="editUser(<?= $userReplid ?>)" ><img src="../images/ico/ubah.png" border="0" alt=""/></a>
                    <a href="#" onclick="hapusUser('<?= $userId ?>')"><img src="../images/ico/hapus.png" border="0" alt=""/></a>
<?php           } ?>
                </td>
            </tr>
<?php
        }
?>
        </table>

    </td>
</tr>
</table>

<div id="divHelpDialog" class="help-dialog"></div>

</body>
</html>
