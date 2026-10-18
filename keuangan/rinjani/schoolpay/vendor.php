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
require_once('../library/stringbuilder.php');
require_once('../include/errorhandler.php');
require_once('vendor.func.php');

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
    <script language="javascript" src="../script/request.factory.js?r=<?=filemtime('../script/request.factory.js')?>"></script>
    <script language="javascript" src="vendor.js?r=<?=filemtime('vendor.js')?>"></script>
</head>

<body>

<table border="0" width="95%" align="center">
<tr>
    <td align="right" valign="top">

        <img class="help-icon-1" src="../images/help32.png" title="bantuan" onclick="showHelp()">
        <span class="pageTitle">Vendor</span><br>
        <a class="pageLink" href="schoolpay.php"><b>SchoolPay</b></a>&nbsp;&gt;&nbsp;
        <span class="pageLinkCurrent">Vendor

    </td>
</tr>
</table>
<br>

<table border="0" width="100%" align="center">
<tr>
    <td align="center" valign="top" width="*">

        <div style="width: 1300px; text-align: right">
            <a href="#" onclick="location.reload();"><img src="../images/ico/refresh.png" border="0">&nbsp;refresh</a>&nbsp;&nbsp;
<?php       if (getLevel() != 2) { ?>
                <a href="#" onclick="tambahVendor()"><img src="../images/ico/tambah.png" border="0">&nbsp;tambah vendor</a>
<?php       } ?>
        </div>

        <table id="table" border="1" cellpadding="5" class="tab" style="border-width: 1px; border-collapse: collapse; border-color: #dddddd">
        <tr style="height: 30px">
            <td class="header" width="30" align="center">No</td>
            <td class="header" width="150" align="center">Vendor Id</td>
            <td class="header" width="200" align="center">Nama</td>
            <td class="header" width="300" align="center">Staf</td>
            <td class="header" width="100" align="center">Status</td>
            <td class="header" width="100" align="center">Terima Iuran</td>
            <td class="header" width="100" align="center">Validasi</td>
            <td class="header" width="100" align="center">Notif SMS | TGram | JS</td>
            <td class="header" width="180" align="center">Keterangan</td>
            <td class="header" width="80">&nbsp</td>
        </tr>
<?php
        $no = 0;
        $sql = "SELECT * FROM jbsfina.vendor ORDER BY nama";
        $res = $db->QueryDb($sql);
        while($row = mysqli_fetch_array($res))
        {
            $no += 1;
            $vendorReplid = $row["replid"];
            $vendorId = $row["vendorid"];

            if (getLevel() != 2)
            {
                $aktif = "<a href='#' onclick='setVendorAktif($vendorReplid, 1)'><img src='../images/ico/nonaktif.png' border='0' title='set aktif'></a>";
                if ($row["aktif"] == 1)
                    $aktif = "<a href='#' onclick='setVendorAktif($vendorReplid, 0)'><img src='../images/ico/aktif.png' border='0' title='set non aktif'></a>";
            }
            else
            {
                $aktif = "<img src='../images/ico/nonaktif.png' border='0' title='set aktif'>";
                if ($row["aktif"] == 1)
                    $aktif = "<img src='../images/ico/aktif.png' border='0' title='set non aktif'>";
            } ?>
            <tr>
                <td align="center" valign="top" class="numberColumn"><?=$no?></td>
                <td align="left" valign="top"><?=$vendorId?></td>
                <td align="left" valign="top"><?=$row["nama"]?></td>
                <td align="left" valign="top">
                    <span id="spDaftarPetugas<?=$vendorId?>">
<?php                   ShowDaftarPetugas($db, $vendorId); ?>
                    </span>
<?php               if (getLevel() != 2) { ?>
                        <a href="#" onclick="tambahPetugas('<?=$vendorId?>')" style="color: blue; font-weight: normal;">&nbsp;(+) tambah staf</a>
<?php               } ?>
                </td>
                <td align="center" valign="top">
                    <span id="spAktif<?=$vendorReplid?>">
                        <?=$aktif?>
                    </span>
                </td>
                <td align="center" valign="top">
<?php               if ($row["terimaiuran"] == 1)
                        echo "<img src='../images/ico/check.png' style='height: 16px;' border='0'>";
                    else
                        echo "&nbsp;" ?>
                </td>
                <td align="left" valign="top">
<?php           // 2023-09-25
                if ($row["valmethod"] == 1)
                    echo "PIN Siswa";
                else
                    echo "PIN dan Persetujuan Siswa" ?>
                </td>
                <td align="center" valign="top">
<?php           if ($row["kirimpesan"] == 1)
                    echo "<img src='../images/ico/check.png' style='height: 16px;' border='0'>";
                else
                    echo "&nbsp;" ?>
                </td>
                <td align="left" valign="top"><?=$row["keterangan"]?></td>
                <td align="center" valign="top">
<?php           if (getLevel() != 2) { ?>
                    <a href="#" onclick="editVendor(<?= $vendorReplid ?>)" ><img src="../images/ico/ubah.png" border="0" alt=""/></a>
                    <a href="#" onclick="hapusVendor('<?= $vendorId ?>')"><img src="../images/ico/hapus.png" border="0" alt=""/></a>
<?php           } ?>
                </td>
            </tr>
<?php
        } ?>
            </table>

    </td>
</tr>
</table>

<div id="divHelpDialog" class="help-dialog"></div>

</body>
</html>