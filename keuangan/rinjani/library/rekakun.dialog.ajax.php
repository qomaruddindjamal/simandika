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
require_once('../include/rupiah.php');
require_once('../include/config.php');
require_once('../include/db.onfunc.php');
require_once('../include/sessioninfo.php');
require_once('../include/errorhandler.php');
require_once('request.func.php');
require_once('logger.php');
require_once('msg.php');
require_once('rekakun.dialog.func.php');
require_once('rekakun.func.php');

$op = $_REQUEST["op"];
if ($op == "344234234324324")
{
    echo SimpanKodeRek();
}
else if ($op == "874897498237432")
{
    DaftarRekAkun($_REQUEST["kategori"]);
}
else if ($op == "783468764837242")
{
    echo CheckKodeRekUsage();
}
else if ($op == "4678732648732648732")
{
    echo HapusKodeRek();
}
else if ($op == "9845798573948573")
{
    echo KodeRekInfo();
}

?>