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
require_once('../util/peek.php');
require_once('../include/errorhandler.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$tanggal1 = RequestData("tanggal1", date("Y-m-d"));
$tanggal2 = RequestData("tanggal2", date("Y-m-d"));
$idtahunbuku = RequestData("idtahunbuku", 0);
$namatahunbuku = RequestData("namatahunbuku", "");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Pengeluaran</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/vldr.js"></script>
    <script language="javascript" src="../script/stringutil.js"></script>
    <script language="javascript" src="../script/dateutil.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="laporan.rekap.js?r=<?=filemtime('laporan.rekap.js')?>"></script>
</head>
<body style="margin: 5px">
<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idtahunbuku" value="<?= $idtahunbuku ?>">
<input type="hidden" id="namatahunbuku" value="<?= $namatahunbuku ?>">
<input type="hidden" id="tanggal1" value="<?= $tanggal1 ?>">
<input type="hidden" id="tanggal2" value="<?= $tanggal2 ?>">

<?php
$sql = "SELECT d.replid AS id, d.nama, SUM(p.jumlah) AS jumlah 
          FROM jbsfina.pengeluaran p, jbsfina.datapengeluaran d, jbsfina.jurnal j
		 WHERE p.idpengeluaran = d.replid 
		   AND d.departemen = '$departemen' 
		   AND p.idjurnal = j.replid 
		   AND j.idtahunbuku = '$idtahunbuku' 
		   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2' 
		 GROUP BY d.replid, d.nama 
		 ORDER BY d.nama";
$res = $db->QueryDb($sql);
if (mysqli_num_rows($res) == 0)
{
    $db->Close();
    echo "<br>";
    echo "<span style='color: maroon'>Belum ada data pengeluaran di tanggal terpilih</span>";
    exit();
}
?>


<table border="0" cellpadding="0" cellspacing="0" width="95%" align="center">
<tr>
    <td align="right">
        <a href="#" onClick="document.location.reload()"><img src="../images/ico/refresh.png" border="0"/>&nbsp;refresh</a>&nbsp;&nbsp;
        <a href="JavaScript:cetak()"><img src="../images/ico/print.png" border="0" />&nbsp;cetak</a>&nbsp;
    </td>
</tr>
</table>

<div id="dvLaporan">
<table class="tab" id="table" border="1" style="border-collapse:collapse" width="95%" align="center">
<tr height="30" align="center">
    <td width="10%" class="header-sm">No</td>
    <td width="50%" class="header-sm">Pengeluaran</td>
    <td width="*" class="header-sm">Jumlah</td>
</tr>
<?php
$cnt = 0;
$total = 0;
while ($row = mysqli_fetch_array($res))
{
    $total += $row['jumlah']; ?>
    <tr height="25" onclick="show_detail(<?= $row['id'] ?>, '<?= $row['nama'] ?>')" style="cursor:pointer">
        <td align="center" class="numberColumn"><?= ++$cnt ?></td>
        <td align="left"><?= $row['nama'] ?></td>
        <td align="right"><?= FormatRupiah($row['jumlah']) ?></td>
    </tr>
<?php
}
?>
<tr height="30">
    <td colspan="2" align="right" style="background-color: #ededed">
        <b>TOTAL</b>
    </td>
    <td align="right" style="background-color: #ededed">
        <strong><?=FormatRupiah($total) ?></strong>
    </td>
</tr>
</table>
</div>

</body>
</html>