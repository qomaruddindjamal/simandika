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
require_once('../include/db.onfunc.php');
require_once('../library/departemen.php');
require_once('../library/msg.php');
require_once('../library/rupiah.php');
require_once('../library/jurnal2.func.php');
require_once('../library/smsmanager2.func.php');
require_once('../util/peek.php');
require_once('../library/userinfo.php');
require_once('../include/errorhandler.php');
require_once('transaksi.tabungan.func.php');

$op = $_REQUEST["op"];
if ($op == "riwayat")
{
    $db = new Db();
    try
    {
        $db->Open();

        $nis = $_REQUEST["nis"];
        $idtahunbuku = $_REQUEST["idtahunbuku"];
        $idtabungan = $_REQUEST["idtabungan"];
        $page = $_REQUEST["page"];

        ShowTransaksiTabungan($db);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kz817");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "info")
{
    $db = new Db();
    try
    {
        $db->Open();

        $nis = $_REQUEST["nis"];
        $idtabungan = $_REQUEST["idtabungan"];

        ShowInfoTabungan($db);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "krzsr");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "setoran")
{
    echo SimpanSetoran();
}
else if ($op == "tarikan")
{
    echo SimpanTarikan();
}
else if ($op == "pagecontrol")
{
    $nData = RequestData("ndata", 0);
    $totalPage = RequestData("totalpage", 0);
    $page = 1;

    ShowPageControl();
}
else if ($op == "lokasiambil")
{
    ShowSelectLokasiPengambilanTabungan();
}
?>
