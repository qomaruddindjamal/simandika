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
require_once('../../include/sessioninfo.php');
require_once('../../include/sessionchecker.php');
require_once('../../library/common.func.php');
require_once('../../include/config.php');
require_once('../../include/db.onfunc.php');
require_once('../../library/departemen.php');
require_once('../../include/errorhandler.php');
require_once('../../include/getheader2.php');

$db = new Db();
$db->TryOpenExit();

$departemen = $_REQUEST["departemen"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Rekapitulasi Penerimaan</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../../style/style.css">
    <script language="javascript" src="../../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../../script/stringutil.js"></script>
    <script language="javascript" src="../../script/dateutil.js"></script>
    <script type="application/javascript">
        $(document).ready(function ()
        {
            let temp = window.opener.getPageContent("departemen");
            $("#spDepartemen").html(temp);

            temp = window.opener.getPageContent("kategori");
            $("#spKategori").html(temp);

            temp = dateutil_formatInaDate(window.opener.getPageContent("tanggal1")) +
                   " s/d " +
                   dateutil_formatInaDate(window.opener.getPageContent("tanggal2"));
            $("#spTanggal").html(temp);

            temp = window.opener.getPageContent("namalaporan");
            $("#spNamaLaporan").html(temp);

            temp = window.opener.getPageContent("petugas");
            $("#spPetugas").html(temp);

            temp = window.opener.getPageContent("laporan");
            $("#dvLaporan").html(temp);

            window.print();
        });
    </script>
</head>

<body>
<table border="0" cellpadding="10" cellpadding="5" width="780" align="left">
<tr>
    <td align="left" valign="top">
<?php
    if ($departemen == "ALL")
        getHeader2($db,'yayasan');
    else
        getHeader2($db, $departemen);
?>
    <center><font size="4"><strong>REKAPITULASI PENERIMAAN</strong></font><br /> </center><br /><br />
    <div style="margin-bottom: 5px;">
        <span style="display: inline-block; width: 100px;">Departemen:</span>
        <span id="spDepartemen"></span>
    </div>
    <div style="margin-bottom: 5px;">
        <span style="display: inline-block; width: 100px;">Kategori:</span>
        <span id="spKategori"></span>
    </div>
    <div style="margin-bottom: 5px;">
        <span style="display: inline-block; width: 100px;">Tanggal:</span>
        <span id="spTanggal"></span>
    </div>
    <div style="margin-bottom: 5px;">
        <span style="display: inline-block; width: 100px;">Laporan:</span>
        <span id="spNamaLaporan"></span>
    </div>
    <div style="margin-bottom: 5px;">
        <span style="display: inline-block; width: 100px;">Petugas:</span>
        <span id="spPetugas"></span>
    </div>

    </td>
</tr>
<tr>
    <td align="left" valign="top">
        <div id="dvLaporan"></div>
    </td>
</tr>
</table>

</body>
</html>