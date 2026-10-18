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
require_once('include/sessionchecker.php');
require_once('include/config.php');
require_once('include/appversion.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Multiple Transactions</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="style/style.css?<?=filemtime('style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="main.footer.css?<?=filemtime('main.footer.css')?>">
</head>

<body>
<div class="container">
&nbsp;&nbsp;Basis data: <?= $db_host ?>&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;Versi: <?= "$VERSION - $BUILDDATE" ?>
</div>
</body>
</html>