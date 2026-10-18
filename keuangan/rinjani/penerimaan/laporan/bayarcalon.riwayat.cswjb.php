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
require_once('../../include/sessioninfo.php');
require_once('../../include/sessionchecker.php');
require_once('../../library/common.func.php');
require_once('../../include/config.php');
require_once('../../include/db.onfunc.php');
require_once('../../library/rupiah.php');
require_once('../../include/errorhandler.php');
require_once('../../include/getheader.php');
require_once('../pembayaran.cswjb.func.php');

$db = new Db();
$db->TryOpenExit();

$idpenerimaan = $_REQUEST["idpenerimaan"];
$namapenerimaan = $_REQUEST["namapenerimaan"];
$idtahunbuku = $_REQUEST["idtahunbuku"];
$userId = $_REQUEST["userid"];
$userName = $_REQUEST["username"];
$idcalon = $_REQUEST["idcalon"];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Riwayat Pembayaran Iuran Wajib Calon Siswa</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../../style/style.css">
    <script language="javascript" src="../../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../../script/tables.js"></script>
    <script type="application/javascript">
        $(document).ready(function ()
        {
            $(".hide-in-report").remove();

            if ($("#tableRiwayat").length)
                Tables("tableRiwayat", 1, 0);
        })
    </script>
</head>

<body>
<center>
    <h2>Riwayat Pembayaran <?= $namapenerimaan?></h2>
</center>

<div>
    <span style="display: inline-block; width: 60px">Nama:</span>
    <span style="font-weight: bold"><?=$userName?></span>
</div>
<div>
    <span style="display: inline-block; width: 60px">No Pendaftaran:</span>
    <span style="font-weight: bold"><?=$userId?></span>
</div>

<?php
$payInfo = GetPaymentInfoCsWjb($db, $idpenerimaan, $idtahunbuku, $idcalon);
if ($payInfo->Exist == false)
{
    echo "<i>Tidak ditemukan data penerimaan /krw70</i>";
    exit();
}

ShowRiwayatPembayaranJttCalon($db, $payInfo);
?>
</html>
