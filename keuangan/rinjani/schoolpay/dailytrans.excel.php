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
require_once('common.func.php');
require_once('dailytrans.func.php');

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Daily_Trans.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$db = new Db();
$db->TryOpenExit();

$petugas = $_REQUEST["petugas"];
if ($petugas == "@0#")
    $petugas = "(Semua Petugas)";
else
    $petugas = GetVendorUserName2($db, $petugas);

$vendor = $_REQUEST["vendor"];
if ($vendor == "@0#")
    $vendor = "(Semua Vendor)";
else
    $vendor = GetVendorName2($db, $vendor);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Riwayat Transaksi Harian</title>
</head>

<body>
<center><font size="4"><strong>RIWAYAT TRANSAKSI HARIAN</strong></font><br /> </center><br /><br />`
<table border="0">
<tr>
    <td><strong>Tanggal </strong></td>
    <td><strong>: <?= LongDateFormat($_REQUEST["tanggal"]) ?></strong></td>
</tr>
<tr>
    <td><strong>Petugas </strong></td>
    <td><strong>: <?= $petugas ?></strong></td>
</tr>
<tr>
    <td><strong>Vendor </strong></td>
    <td><strong>: <?= $vendor ?></strong></td>
</tr>
</table>
<br />

<?php
ShowRekapDailyReport(false);
ShowDailyReport(false, false);
?>

</body>
</html>
