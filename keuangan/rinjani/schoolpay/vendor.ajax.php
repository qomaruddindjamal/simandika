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
require_once('../include/sessioninfo.php');
require_once('../library/common.func.php');
require_once('../include/config.php');
require_once('../library/rupiah.php');
require_once('../library/msg.php');
require_once('../include/db.onfunc.php');
require_once('../library/departemen.php');
require_once('../library/stringbuilder.php');
require_once('../include/errorhandler.php');
require_once('vendor.func.php');

$op = $_REQUEST["op"];
if ($op == "568789673945")
{
    echo SetVendorAktif();
}
else if ($op == "2876328746237462")
{
    echo HapusUserVendor();
}
else if ($op == "98789769")
{
    $db = new Db();
    try
    {
        $db->Open();

        $vendorId = $_REQUEST["vendorid"];
        ShowDaftarPetugas($db, $vendorId);
    }
    catch (Exception $ex)
    {
        Msg::InfoError($ex->getMessage(), "k8fyb");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "387298378923")
{
    echo HapusVendor();
}
?>