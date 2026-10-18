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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Query Error Log</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
</head>
<body>

<table border="0" width="100%">
<tr>
    <td align="center" valign="top">

        <table border="0" width="95%" align="center">
        <tr>
            <td align="right">
                <span class="pageTitle">Query Error Log</span><br>
                <a class="pageLink" href="pengaturan.php">Referensi</a>&nbsp&gt;&nbsp
                <span class="pageLinkCurrent">Query Error Log</span>
            </td>
        </tr>
        </table>

        <br>

<?php	$logFile = realpath(dirname(__FILE__)) . "/../../log/keuangan-error.log";
        $logFile = str_replace("\\", "/", $logFile);
        if (!file_exists($logFile))
            $logFile = "";

        if ($logFile != "")
        {
            $r = rand(1, 30000);
            $docRoot = $_SERVER['DOCUMENT_ROOT'];
            $logFile = "http://" . $_SERVER['SERVER_ADDR'] . str_replace($docRoot, "", $logFile) . "?$r";
        } ?>

        <iframe name="logContent" id="logContent"
                width="100%" height="400"
                src="<?=$logFile?>"
                style="border-width:1px; background-color:#FFF"></iframe>


    </td>
</tr>
</table>

</body>
</html>