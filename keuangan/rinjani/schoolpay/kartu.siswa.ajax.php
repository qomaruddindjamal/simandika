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
require_once('../include/errorhandler.php');
require_once('kartu.siswa.func.php');

$op = $_REQUEST["op"];
if ($op == "gettingkat")
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = $_REQUEST["departemen"];
        ShowCbTingkat($db, $departemen);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kgwwt");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "getkelas")
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = $_REQUEST["departemen"];
        $idTingkat = $_REQUEST["idtingkat"];

        ShowCbKelas($db, $departemen, $idTingkat);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kwknf");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "showkartu")
{
    $db = new Db();
    try
    {
        $db->Open();

        ShowKartuSiswa($db, true);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "ku1aw");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "changeaktif")
{
    $db = new Db();
    try
    {
        $db->Open();

        ChangeKartuAktif($db);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kw8z9");
    }
    finally
    {
        $db->Close();
    }
}
?>