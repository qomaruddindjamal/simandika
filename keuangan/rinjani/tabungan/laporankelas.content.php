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
require_once('../library/msg.php');
require_once('../library/rupiah.php');
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('laporankelas.content.func.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idTingkat = RequestData("idtingkat", 0);
$namaTingkat = RequestData("namatingkat", "");
$idKelas = RequestData("idkelas", 0);
$namaKelas = RequestData("namakelas", "");
$idTabungan = RequestData("idtabungan", 0);
$namaTabungan = RequestData("namatabungan", "");
$urut = RequestData("urut", "s.nama");
$page = RequestData("page", 1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Tabungan Siswa Per Kelas</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="laporankelas.content.js?r=<?=filemtime('laporankelas.content.js')?>"></script>
</head>
<body style="margin: 10px;">
<span class="dialogTitle"><?=$namaTabungan?></span><br>

<?php
$nisList = "";
PrepareNisList($db);
if ($nisList == "")
{
    echo "<span style='color: maroon'>belum ada data tabungan di tingkat/kelas atau tabungan terpilih</span>";
    exit();
}
?>

<br>

<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idtingkat" value="<?= $idTingkat ?>">
<input type="hidden" id="namatingkat" value="<?= $namaTingkat ?>">
<input type="hidden" id="idkelas" value="<?= $idKelas ?>">
<input type="hidden" id="namakelas" value="<?= $namaKelas ?>">
<input type="hidden" id="idtabungan" value="<?= $idTabungan ?>">
<input type="hidden" id="namatabungan" value="<?= $namaTabungan ?>">
<input type="hidden" id="urut" value="<?= $urut ?>">
<input type="hidden" id="nislist" value="<?= base64_encode($nisList)?>">

<?php
    ShowRekapTabunganSiswa($db);
    ShowDaftarTabunganSiswa($db);
    ShowPageControl();
?>

</body>
</html>