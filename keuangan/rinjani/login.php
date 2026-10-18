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
require_once('include/sessioninfo.php');
require_once('include/config.php');
require_once('include/appversion.php');

if (isset($_SESSION['namakeuangan']))
{
    header("Location: main.php");
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>JIBAS Keuangan Rinjani</title>
    <link href="images/jibas.ico" rel="shortcut icon" />
    <link rel="stylesheet" type="text/css" href="style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="style/toast.css">
    <link rel="stylesheet" type="text/css" href="login.css?<?=filemtime('login.css')?>">
    <link rel="stylesheet" href="script/bgstretcher.css" />
    <link rel="stylesheet" type="text/css" href="script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="script/bgstretcher.js"></script>
    <script language="javascript" src="script/qsbuilder.js"></script>
    <script language="javascript" src="script/toast.js"></script>
    <script language="javascript" src="login.js?<?=filemtime('login.js')?>"></script>
</head>
<body onload="onResize()" onresize="onResize()">
<div style="position:relative; z-index:2;">

    <table width="100%" border="0">
    <tr>
        <td width="100%">

            <div id="Main" align="center" style="width:511px; height:234px">

                <table id="Table_01" width="510" height="206" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td rowspan="4"><img src="../../images/imfront_keuangan2025.png" style="width: 145px;"></td>
                    <td height="70" valign="bottom" align="left">
                        <span style="font-family: 'Segoe UI', sans-serif; font-size: 16px; color:#fff; font-weight:bold;">
                            SISTEM INFORMASI
                            <span style="color: black">KEUANGAN</span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td width="363" height="24" valign="top" align="left">

                        <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td style="padding-right:4px">
                                <input type="text" id="login" class="inputbox"  style="width:80px;" placeholder="Login">
                            </td>
                            <td style="padding-right:4px">
                                <input type="password" id="password" class="inputbox" style="width:80px;" placeholder="Password" >
                            </td>
                            <td style="padding-right:4px">
                                <input type="button" class="dialogButtonPositive" value="Login"
                                       onclick="processLogin()" >
                            </td>
                            <td>
                                <a title="Kembali ke Menu Utama" href="../../" style="color:#33ddff; font-weight:bold; font-family:Arial; font-size:12px; text-decoration:underline">Menu Utama</a>
                            </td>
                        </tr>
                        </table>

                        <br>
                        <a title="Versi sebelumnya" href="../index.php"
                           style="color: #ddd; font-weight: normal">versi sebelumnya</a>

                    </td>
                </tr>
                <tr>
                    <td width="363" height="18">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                </table>



            </div>



        </td>
    </tr>
    </table>



    <div id="toast-container"></div>

    <div class="divVersionInfo">
        &nbsp;&nbsp;Basis data: <?= $db_host ?>&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;Versi: <?= "$VERSION - $BUILDDATE" ?>
    </div>

</div>




</body>
</html>