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
function HapusBarang()
{
    $db = new Db();
    try
    {
        $db->Open();

        $id = $_REQUEST["id"];

        $sql = "DELETE FROM jbsfina.barang WHERE replid = $id";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k53du")]);
    }
    finally
    {
        $db->Close();
    }
}

function SetAktif()
{
    $db = new Db();
    try
    {
        $db->Open();

        $id = $_REQUEST["id"];
        $newaktif = $_REQUEST["newaktif"];

        $sql = "UPDATE jbsfina.barang SET aktif = $newaktif WHERE replid = $id";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k53du")]);
    }
    finally
    {
        $db->Close();
    }
}
?>