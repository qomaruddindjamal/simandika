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
require_once('carijurnal.content.func.php');

$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : 1;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Cari Jurnal</title>
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
    <script language="javascript" src="carijurnal.content.js?r=<?=filemtime('carijurnal.content.js')?>"></script>
</head>
<body style="margin: 0">

<?php
$lsRet = CountDataJurnal();
if ($lsRet[0] < 0)
{
    echo $lsRet[1];
    exit();
}

$nData = $lsRet[1];
$nRowPerPage = 10;
$nPage = ceil($nData / $nRowPerPage);
?>
<input type="hidden" id="departemen" value="<?= $_REQUEST["departemen"] ?>">
<input type="hidden" id="kriteria" value="<?= $_REQUEST["kriteria"] ?>">
<input type="hidden" id="namakriteria" value="<?= $_REQUEST["namakriteria"] ?>">
<input type="hidden" id="keyword" value="<?= $_REQUEST["keyword"] ?>">
<input type="hidden" id="idtahunbuku" value="<?= $_REQUEST["idtahunbuku"] ?>">
<input type="hidden" id="tahunbuku" value="<?= $_REQUEST["tahunbuku"] ?>">
<input type="hidden" id="tanggal1" value="<?= $_REQUEST["tanggal1"] ?>">
<input type="hidden" id="tanggal2" value="<?= $_REQUEST["tanggal2"] ?>">
<input type="hidden" id="npage" value="<?= $nPage ?>">
<input type="hidden" id="ndata" value="<?= $nData ?>">

<table width="100%">
<tr>
    <td align="center">

        <table border="0" width="95%" align="center">
        <tr>
            <td align="right">
                <a href="JavaScript:currPage()"><img src="../images/ico/refresh.png" border="0" title="refresh"/>&nbsp;refresh</a>&nbsp;&nbsp;
                <a href="JavaScript:cetak()"><img src="../images/ico/print.png" border="0" title="cetak"/>&nbsp;cetak</a>&nbsp;&nbsp;
            </td>
        </tr>
        </table>

    </td>
</tr>
<tr>
    <td align="center">

    <div id="dvContent">

    </div>

</td></tr>
</table>
</body>
</html>