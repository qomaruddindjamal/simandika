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
require_once('../include/db.onfunc.php');
require_once('../include/getheader2.php');
require_once('../library/departemen.php');
require_once('../include/errorhandler.php');
require_once('../library/date.func.php');
require_once('../library/logger.php');
require_once('../library/stringbuilder.php');

$idRefund = $_REQUEST["idrefund"];

$db = new Db();
$db->TryOpenExit();

$sql = "SELECT DATE_FORMAT(r.waktu, '%d-%b-%Y %H:%i') AS waktu, IFNULL(pg.nama, 'Administrator JIBAS') AS petugas, 
               u.nama AS penerima, r.jumlah, r.keterangan, 
               IFNULL(r.idjurnalsiswa, 0) AS idjurnalsiswa, IFNULL(r.idjurnalpegawai, 0) AS idjurnalpegawai, tb.departemen 
          FROM jbsfina.refund r
          LEFT JOIN jbssdm.pegawai pg ON r.nip = pg.nip
          LEFT JOIN jbsfina.userpos u ON r.idpenerima = u.userid
          LEFT JOIN jbsfina.tahunbuku tb ON r.idtahunbuku = tb.replid
         WHERE r.replid = $idRefund";
$res = $db->QueryDb($sql);
$row = mysqli_fetch_array($res);
$tanggal = $row["waktu"];
$jumlah = $row["jumlah"];
$petugas = $row["petugas"];
$penerima = $row["penerima"];
$keterangan = $row["keterangan"];
$idJurnalSiswa = $row["idjurnalsiswa"];
$idJurnalPegawai = $row["idjurnalpegawai"];
$departemen = $row["departemen"];

$noKas = "";
if ($idJurnalSiswa <> 0)
{
    $sql = "SELECT nokas FROM jbsfina.jurnal WHERE replid = $idJurnalSiswa";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
        $noKas = $row[0];
}

if ($idJurnalPegawai <> 0)
{
    $sql = "SELECT nokas FROM jbsfina.jurnal WHERE replid = $idJurnalPegawai";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        if ($noKas <> "") $noKas .= ", ";
        $noKas .= $row[0];
    }
}

$sql = "SELECT DATE_FORMAT(tanggal, '%d-%b-%Y') AS tanggal
          FROM jbsfina.refunddate
         WHERE idrefund = $idRefund
         ORDER BY tanggal";
$res2 = $db->QueryDb($sql);
$stTanggal = "";
while($row2 = mysqli_fetch_row($res2))
{
    if ($stTanggal <> "") $stTanggal .= ", ";
    $stTanggal .= $row2[0];
}
$transaksi = "Pembayaran penerimaan Vendor dari transaksi SchoolPay tanggal $stTanggal";

$sql = "SELECT replid, nama, alamat1 FROM jbsumum.identitas WHERE departemen='$departemen'";
$result = $db->QueryDb($sql);
$row = @mysqli_fetch_array($result);
$idHeader = $row["replid"];
$namaHeader = $row["nama"];
$alamatHeader = $row["alamat1"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Kuitansi Pembayaran Tabungan</title>
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script type="application/javascript">
        $(document).ready(function () {
            window.print();
        })
    </script>
</head>

<body style="margin: 0;">

<table border="0" cellpadding="0" cellspacing="0" width="340" align="center">
<?php
for($i = 0; $i < 2; $i++)
{
    ?>
    <tr>
        <td align="center" valign="top">
            <table border="0" cellpadding="0" cellspacing="3" width="330" align="center">
<?php       if ($i == 0)
            { ?>
                <tr>
                    <td align="center" width='15%'>
<?php                   getSmallHeader2($db, $departemen); ?>
                    </td>
                </tr>
<?php       } else { ?>
                <tr height="1">
                    <td align="center" width='15%'>&nbsp;</td>
                </tr>
<?php       } ?>
                <tr>
                    <td align="right" colspan='2'>
                        <font size="1"><strong>No. <?=$noKas ?></strong></font>
                    </td>
                </tr>
                <tr>
                    <td align="center" colspan='2'>
                        <font size="1"><strong>TANDA BUKTI PEMBAYARAN</strong></font>
                    </td>
                </tr>
                <tr>
                    <td align="left" colspan='2'>
                        Telah dibayarkan kepada:
                        <table border="0" cellpadding="2" cellspacing="0" width="100%">
                            <tr>
                                <td>&nbsp;</td>
                                <td>Nama</td>
                                <td>:&nbsp;<strong><?=$penerima?></strong></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>Tanggal</td>
                                <td>:&nbsp;<strong><?= $tanggal ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="3" valign="top">uang sejumlah
                                    <font style="font-size:11px; font-weight:bold; font-style:italic;">
                                        <?= FormatRupiah($jumlah) ?> (<?= KalimatUang($jumlah) ?>)
                                    </font>
                                    untuk <?=$transaksi ?>
                                </td>
                            </tr>
                        </table>
                        <br>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="65%">

                                    <table border="1" cellpadding="2" cellspacing="0" style="border-width:1px" width="100%">
                                        <tr>
                                            <td valign="top">
                                                <strong>Keterangan:</strong><br>
                                                &#149;&nbsp;<em>Tgl cetak: <?= date('d/m/Y H:i:s') ?></em><br>
                                                &#149;&nbsp;<em>Petugas: <?= $petugas ?></em><br>
                                            </td>
                                        </tr>
                                    </table>

                                </td>
                                <td align="center">
                                    <? if ($i == 0) { ?>
                                        Yang menerima<br /><br /><br /><br /><br />
                                        ( <?=getUserName() ?> )
                                    <? } else { ?>
                                        Yang menyerahkan<br /><br /><br /><br /><br />
                                        ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                                    <? } ?>
                                </td>
                            </tr>
                        </table>
                    </td></tr>
            </table>
        </td></tr>
    <tr>
        <td align='right'>
            <? if ($i == 0) { ?>
                <hr width="350" style="border-style:dashed; line-height:1px; color:#666;" />
            <?	} ?>
        </td>
    </tr>
<? } //for ?>
</table>

</body>
</html>