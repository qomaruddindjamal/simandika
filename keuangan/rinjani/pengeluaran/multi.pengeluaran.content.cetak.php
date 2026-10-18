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
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('../include/getheader.php');

$departemen = $_REQUEST['departemen'];
$petugas = getUserName();

OpenDb();

$sql = "SELECT replid, nama, alamat1 FROM jbsumum.identitas WHERE departemen='$departemen'";
$result = QueryDb($sql);
$row = @mysqli_fetch_array($result);
$idHeader = $row['replid'];
$namaHeader = $row['nama'];
$alamatHeader = $row['alamat1'];

CloseDb();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Cetak Tanda Bukti Pembayaran</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/colors.css?<?=filemtime('../style/colors.css')?>">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script type="application/x-javascript">
        $(function() {
            $("#divReportDetail").html(window.opener.GetReportDetail());
            $("#divReportDetail2").html(window.opener.GetReportDetail());

            window.print();
        });
    </script>
</head>
<body style="margin: 0">

<table border="0" cellpadding="10" cellspacing="0" width="780">
<tr>
    <td align="center" width='15%'>
        <img src='<?= "../library/gambar.php?replid=$idHeader&table=jbsumum.identitas" ?>' height='30' />
    </td>
    <td align="left">
        <font style='font-size:14px'><strong><?=$namaHeader?></strong></font><br>
        <font style='font-size:10px'><?=$alamatHeader?></font>
    </td>
</tr>
<tr>
    <td align="center" colspan='2'>
        <font size="1"><strong>TANDA BUKTI PENGELUARAN</strong></font>
    </td>
</tr>
<tr>
    <td align="left" valign="top" colspan="2">
        <div id="divReportDetail"></div>
    </td>
</tr>
<tr>
    <td align="left" valign="top" colspan="2">

    <table border="0" cellpadding="0" cellspacing="0" width="85%">
    <tr>
        <td width="75%" valign='top'>

        <table border="1" cellpadding="2" cellspacing="0" style="border-width:1px" width="100%">
        <tr>
            <td valign="top">
                &#149;&nbsp;<em>Tgl cetak: <?= date('d/m/Y H:i:s') ?></em><br>
                &#149;&nbsp;<em>Petugas: <?= $petugas ?></em><br>
            </td>
        </tr>
        </table>

        </td>
        <td align="center">
            Yang menerima<br /><br /><br /><br /><br />
            ( <?=getUserName() ?> )
        </td>
    </tr>
    </table>

    </td>
</tr>
<tr>
    <td align="left" valign="top" colspan="2">
        <hr width="740" style="border-style:dashed; line-height:1px; color:#666;" />
    </td>
</tr>
<tr>
    <td align="center" colspan='2'>
        <font size="1"><strong>TANDA BUKTI PENGELUARAN</strong></font>
    </td>
</tr>
<tr>
    <td align="left" valign="top" colspan="2">
        <div id="divReportDetail2"></div>
    </td>
</tr>
<tr>
    <td align="left" valign="top" colspan="2">

    <table border="0" cellpadding="0" cellspacing="0" width="85%">
    <tr>
        <td width="75%" valign='top'>

        <table border="1" cellpadding="2" cellspacing="0" style="border-width:1px" width="100%">
        <tr>
            <td valign="top">
                &#149;&nbsp;<em>Tgl cetak: <?= date('d/m/Y H:i:s') ?></em><br>
                &#149;&nbsp;<em>Petugas: <?= $petugas ?></em><br>
            </td>
        </tr>
        </table>

        </td>
        <td align="center">
            Yang menyerahkan<br /><br /><br /><br /><br />
            ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
        </td>
    </tr>
    </table>

    </td>
</tr>
</table>

</body>
</html>