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
require_once('../include/db.onpage.php');
require_once('../library/departemen.php');
require_once('../library/msg.php');
require_once('../library/logger.php');
require_once('../util/peek.php');
require_once('../library/userinfo.php');
require_once('../include/errorhandler.php');
require_once('multi.pengeluaran.content.func.php');

$db = new Db;
$db->TryOpenExit(true);

$_SESSION["multipaystep"] = 1;

$departemen = $_REQUEST['departemen'];
$idtahunbuku = $_REQUEST['idtahunbuku'];
$tahunbuku = $_REQUEST['tahunbuku'];


OpenDb();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Multiple Transactions</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/tooltips.rinjani.css?<?=filemtime('../style/tooltips.rinjani.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/rupiah2.js"></script>
    <script language="javascript" src="../script/rupiah3.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/stringutil.js"></script>
    <script language="javascript" src="../script/dateutil.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/tooltips.rinjani.js?r=<?= filemtime('../script/tooltips.rinjani.js') ?>"></script>
    <script language="javascript" src="multi.pengeluaran.content.js?<?=filemtime('multi.pengeluaran.content.js')?>"></script>
</head>

<body style="margin: 0;">
<input type="hidden" id="departemen" value="<?=$departemen?>">
<input type="hidden" id="idtahunbuku" value="<?=$idtahunbuku?>">
<input type="hidden" id="tahunbuku" value="<?=$tahunbuku?>">

<table border="0" cellpadding="6" cellspacing="0" width="100%">
<tr>
    <td align="left" valign="top" width="35%">

    <table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tr>
        <td align="left" valign="top">
            <div id="divSelectPayment">
<?php          	ShowSelectPengeluaran() ?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="left" valign="top">
            <div id="divPaymentInfo" style="overflow: auto; margin-top: 10px; height: 350px; background-color: #ffffff">

            </div>
        </td>
    </tr>
    </table>

    </td>
    <td align="left" valign="top" width="*">

        <fieldset>
            <legend><strong>Daftar Pengeluaran</strong></legend>
            <div id="divPaymentBox" style="background-color: #eee; height: 340px; overflow: auto;">

                <form name="mainForm" id="mainForm" method="POST" action="multi.pengeluaran.content.save.php" onsubmit="return ValidateSave()">
                    <input type='hidden' name="nflagrow" id="nflagrow" value="0">
                    <input type="hidden" name="departemen" id="departemen" value="<?=$departemen?>">
                    <input type="hidden" name="idtahunbuku" id="idtahunbuku" value="<?=$idtahunbuku?>">
                    <table border="1" id="tabPaymentList" cellpadding="2" cellspacing="0" style="border-width: 1px; border-collapse: collapse; border-color: #ddd" width="810">
                        <thead>
                        <tr height="20">
                            <td class="header" width="170" align="center">Pengeluaran</td>
                            <td class="header" width="350" align="center">Transaksi	</td>
                            <td class="header" width="100" align="center">Rek. Kas</td>
                            <td class="header" width="120" align="center">Jumlah</td>
                            <td class="header" width="60" align="center">&nbsp;</td>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                        <tr height="30" style="background-color: #ccc">
                            <td colspan="3" align="right"><strong>T O T A L</strong></td>
                            <td align="right">
                                <span id="spanTotalInfo" style="font-weight: bold"></span>
                            </td>
                            <td>&nbsp;</td>
                        </tr>
                        </tfoot>
                    </table>

            </div>
            <table border="0" cellpadding="2" cellspacing="0">
                <tr>
                    <td width="120" align="right" valign="bottom">
                        <input type="submit" value="Simpan" class="dialogButtonPositive" style="height: 45px; width: 100px;">
                    </td>
                    <td align="left" valign="top">
                        &nbsp;
                    </td>
                </tr>
            </table>
            </form>
        </fieldset>

    </td>
</tr>
</table>

<div id="divDialog"></div>

<div id="tooltip" class="tooltip hidden" aria-hidden="true">
    <button class="tooltip-close">&times;</button>
    <div class="tooltip-arrow"></div>
    <div class="tooltip-content"></div>
</div>

</body>
</html>
<?php
CloseDb();
?>