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
require_once('../util/peek.php');
require_once('bayartunggak.content.daftar.func.php');

$op = $_REQUEST["op"];
if ($op == "daftarsiswa")
{
    $db = new Db();
    try
    {
        $db->Open();

        $idpenerimaan = $_REQUEST["idpenerimaan"];
        $idtahunbuku = $_REQUEST["idtahunbuku"];
        $urut = $_REQUEST["urut"];
        $page = $_REQUEST["page"];

        ShowDaftarSiswaTable($db);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kvmf3");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "daftarcalon")
{
    $db = new Db();
    try
    {
        $db->Open();

        $idpenerimaan = $_REQUEST["idpenerimaan"];
        $idtahunbuku = $_REQUEST["idtahunbuku"];
        $urut = $_REQUEST["urut"];
        $page = $_REQUEST["page"];

        ShowDaftarCalonSiswaTable($db);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k5qkj");
    }
    finally
    {
        $db->Close();
    }
}
?>