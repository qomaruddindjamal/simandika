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
require_once('../include/db.onfunc.php');
require_once('../library/departemen.php');
require_once('../include/errorhandler.php');
require_once('../library/date.func.php');
require_once('../library/logger.php');
require_once('../library/stringbuilder.php');
require_once('refund.func.php');

$op = $_REQUEST["op"];
if ($op == "gettahunbuku")
{
    $dept = $_REQUEST["departemen"];

    $db = new Db();
    try
    {
        $db->Open();
        ShowTahunBuku($db, $dept);
    }
    catch (Exception $ex)
    {
        Msg::InfoError($ex->getMessage(), "knzzn");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "getlastrefunddate")
{
    $vendorId = $_REQUEST["vendorid"];
    $idTahunBuku = $_REQUEST["idtahunbuku"];

    $db = new Db();
    try
    {
        $db->Open();
        ShowLastRefundDate($db, $vendorId, $idTahunBuku);
    }
    catch (Exception $ex)
    {
        Msg::InfoError($ex->getMessage(), "kt1tu");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "gettagihanvendor")
{
    $vendorId = $_REQUEST["vendorid"];
    $departemen = $_REQUEST["departemen"];

    $db = new Db();
    try
    {
        $db->Open();
        ShowTagihanVendor($db, $vendorId, $departemen);
    }
    catch (Exception $ex)
    {
        Msg::InfoError($ex->getMessage(), "kmuen");
    }
    finally
    {
        $db->Close();
    }

}
else if ($op == "showrefundhistory")
{
    $db = new Db();
    try
    {
        $db->Open();
        ShowRefundHistory($db, true);
    }
    catch (Exception $ex)
    {
        Msg::InfoError($ex->getMessage(), "khgmu");
    }
    finally
    {
        $db->Close();
    }
}
?>