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
require_once('../include/errorhandler.php');
require_once('../include/getheader.php');

OpenDb();

$departemen = $_REQUEST["departemen"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Jenis Tabungan</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script type="application/javascript">
        $(document).ready(function ()
        {
            var content = window.opener.getPageContent("table");
            $("#dvCetak").html(content);

            var table = $('#table');
            table.find('tr').each(function() {
                $(this).find('td.menu').remove();
            });

            $("#spDepartemen").html(": " + window.opener.getPageContent("departemen"));
        });
    </script>
</head>

<body>
<table border="0" cellpadding="10" cellpadding="5" width="780" align="left">
<tr>
    <td align="left" valign="top">

<?php   if ($departemen == "ALL")
            getHeader('yayasan');
        else
            getHeader($departemen); ?>

        <center><font size="4"><strong>JENIS TABUNGAN PEGAWAI</strong></font><br /> </center><br /><br />
        <table border="0">
        <tr>
            <td width="49%" align="left" valign="top">

                <table border="0">
                    <tr>
                        <td><strong>Departemen</strong></td>
                        <td><span id="spDepartemen" style="font-weight: bold;"></span></td>
                    </tr>
                </table>

            </td>

            <td width="2%">&nbsp;</td>
            <td width="49%" align="left" valign="top">

            </td>
        </tr>
        </table>

    </td>
</tr>
<tr>
    <td align="left" valign="top">
        <div id="dvCetak">

        </div>
    </td>
</tr>
</table>

</body>
</html>
<?php
CloseDb();
?>