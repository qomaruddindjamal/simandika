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
require_once('../library/jurnal2.func.php');
require_once('../include/errorhandler.php');
require_once('inputbayar2.func.php');

$op = $_REQUEST["op"];
if ($op == "getpenerimaan")
{
    $departemen = $_REQUEST["departemen"];
    $idKategori = $_REQUEST["idkategori"];

    $db = new Db();
    try
    {
        $db->Open();
        echo ShowSelectPenerimaan($db, $departemen, $idKategori);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kuvcr");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "gettingkat")
{
    $departemen = $_REQUEST["departemen"];
    $idKategori = $_REQUEST["idkategori"];

    $db = new Db();
    try
    {
        $db->Open();

        if ($idKategori == "JTT")
            echo ShowSelectTingkatSiswa($db, $departemen);
        else
            echo ShowSelectProsesCalonSiswa($db, $departemen);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "ku12u");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "getkelas")
{
    $departemen = $_REQUEST["departemen"];
    $idKategori = $_REQUEST["idkategori"];
    $idTingkat = $_REQUEST["idtingkat"];

    $db = new Db();
    try
    {
        $db->Open();

        if ($idKategori == "JTT")
            echo ShowSelectKelasSiswa($db, $departemen, $idTingkat);
        else
            echo ShowSelectKelompokCalonSiswa($db, $departemen, $idTingkat);
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "ku12u");
    }
    finally
    {
        $db->Close();
    }
}
else if ($op == "setbayar")
{
    $departemen = $_REQUEST["departemen"];
    $idKategori = $_REQUEST["idkategori"];
    $idPenerimaan = $_REQUEST["idpenerimaan"];
    $idTingkat = $_REQUEST["idtingkat"];
    $idKelas = $_REQUEST["idkelas"];
    $besar = $_REQUEST["besar"];
    $cicilan = $_REQUEST["cicilan"];
    $cicilanPertama = $_REQUEST["cicilanpertama"];

    if ($idKategori == "JTT")
        echo SimpanBesarSiswa($departemen, $idPenerimaan, $idTingkat, $idKelas, $besar, $cicilan, $cicilanPertama);
    else if ($idKategori == "CSWJB")
        echo SimpanBesarCalonSiswa($departemen, $idPenerimaan, $idTingkat, $idKelas, $besar, $cicilan, $cicilanPertama);
}























?>