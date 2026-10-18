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
require_once('../library/rupiah.php');
require_once('../library/msg.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');

$db = new Db;
$db->TryOpenExit(true);

$id = RequestData("id", 0);

$sql = "SELECT *, IF(foto IS NULL, 0, 1) AS fotoexist, 
               IF(foto IS NULL, '', TO_BASE64(foto)) AS foto64 
          FROM jbsfina.barang 
         WHERE replid = $id";
$res = $db->QueryDb($sql);
if (mysqli_num_rows($res) == 0)
{
    echo "Tidak ditemukan data barang!";
    exit();
}
$row = mysqli_fetch_array($res);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Informasi Barang</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/rupiah3.js"></script>
    <script language="javascript" src="../script/vldr.js?r=<?=filemtime('../script/vldr.js')?>"></script>
    <script language="javascript" src="../script/stringutil.js"></script>
    <script language="javascript" src="../script/dateutil.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="barang.dialog.js?r=<?=filemtime('barang.dialog.js')?>"></script>
</head>
<body style="margin: 10px">

<span style="color: #666; font-size: 12px;"><?= $_REQUEST["namagroup"]?>&nbsp;&gt;&nbsp;<?= $_REQUEST["namakelompok"]?></span><br>
<span style="color: #000; font-size: 18px;"><?= $row['nama'] ?>&nbsp;-&nbsp;<?= $row["kode"]?></span><br>
<div style="overflow: auto; width: 95%; height: 400px; align-items: center">
<?php

$fotoexist = $row['fotoexist'];
if ($fotoexist == 0)
    $foto64 = "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";
else
    $foto64 = $row['foto64'];
echo "<img style='padding: 2px;' src='data:image/jpg;base64,$foto64'><br>";
?>

</div>

<table cellpadding="2" cellspacing="0" width="100%" align="center">
<tr>
    <td style="width:100px;">Jumlah</td>
    <td>
        <?= $row["jumlah"] ?>
        <?= $row["satuan"] ?>
    </td>
</tr>
<tr>
    <td>Harga Satuan</td>
    <td><?= FormatRupiah($row["info1"]) ?></td>
</tr>
<tr>
    <td>Total Harga</td>
    <td>
<?php   $total = $row["jumlah"] * $row["info1"];
        echo FormatRupiah($total) ?>
    </td>
</tr>
<tr>
    <td>Tanggal Perolehan</td>
    <td>
        <?= LongDateFormat($row["tglperolehan"]) ?>
    </td>
</tr>
<tr>
    <td>Kondisi</td>
    <td>
        <?= $row["kondisi"] ?>
    </td>
</tr>
<tr>
    <td>Keterangan</td>
    <td>
        <?= $row["keterangan"] ?>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <br>
        <input type="button" class="dialogButtonNegative" value="Tutup" onclick="window.close()">
    </td>
</tr>
</table>

</body>
</html>