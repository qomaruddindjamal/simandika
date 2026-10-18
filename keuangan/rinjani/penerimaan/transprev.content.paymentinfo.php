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
require_once('../include/sessionchecker.onfunc.php');
require_once('../library/common.func.php');
require_once('../include/config.php');
require_once('../include/db.onpage.php');
require_once('../library/departemen.php');
require_once('../library/msg.php');
require_once('../library/rupiah.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('transprev.content.paymentinfo.func.php');

OpenDb();
try
{
    $jenis = $_REQUEST['jenis'];
    $departemen = $_REQUEST['departemen'];
    $noid = $_REQUEST['noid'];
    $idtahunbuku = $_REQUEST['idtahunbuku'];
    $kelompok = $_REQUEST['kelompok'];
    $idpayment = $_REQUEST['idpayment'];

    if ($jenis == "JTT")
        InfoWjbSiswa();
    elseif ($jenis == "CSWJB")
        InfoWjbCalonSiswa();

    CloseDb();
    http_response_code(200);
}
catch(DbException $dbe)
{
    CloseDb();

    http_response_code(500);
    echo $dbe->getMessage();
}
catch(Exception $e)
{
    CloseDb();

    http_response_code(500);
    echo $e->getMessage();
}
?>