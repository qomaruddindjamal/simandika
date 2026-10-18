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
//require_once('../include/config.php');
//require_once("../include/db.onfunc.php");
//require_once("../util/peek.php");
require_once("updatedb.func.php");

if (!isset($_SESSION["updatedb"]))
{
    echo "<br><br>";
    echo "<center>checking for update ..</center>";

    $db = new Db();
    $db->TryOpenExit();

    require_once("updatedb.001.php");
    require_once("updatedb.002.php");
    require_once("updatedb.003.php");
}

$_SESSION["updatedb"] = 1;
?>
