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
require_once('../library/qsbuilder.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pengaturan</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="pengaturan.js?<?=filemtime('pengaturan.js')?>"></script>
</head>
<body>

<table border="0" cellspacing="0" cellpadding="0" align="center" width="70%">
<tr>
    <td align="center" width="100%">
        <span class="pageTitle">PENGATURAN</span><br><br><br>

        <table border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td align="center" width="120">
                <img src="../images/userlist.png" style="width: 40px" title="Daftar Pengguna"><br>
                <a href="user2.php">Daftar Pengguna</a><br>
            </td>
            <td align="center" width="10">&nbsp;</td>
            <td align="center" width="120">
                <img src="../images/changepass.png" style="width: 40px" title="Ganti Password"><br>
                <a href="JavaScript:changePwd()">Ganti Password</a><br>
            </td>
            <td align="center" width="10">&nbsp;</td>
            <td align="center" width="120">
                <img src="../images/errorlog.png" style="width: 40px" title="Query Error Log"><br>
                <a href="queryerror2.php">Query Error Log</a><br>
            </td>
        </tr>
        </table>

    </td>
</tr>
</table>

</body>
</html>