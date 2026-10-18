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
require_once('../include/db.onpage.php');
require_once('../library/departemen.php');
require_once('../library/stringbuilder.php');
require_once('../include/errorhandler.php');
require_once('../include/getheader.php');

OpenDb();

$transId = $_REQUEST["transid"];

$sql = "SELECT SUM(jumlah)
          FROM jbsfina.paymenttrans
         WHERE transactionid = '$transId'";
$res = QueryDb($sql);
$row = mysqli_fetch_row($res);
$jumlah = $row[0];

$sql = "SELECT p.transactionid, DATE_FORMAT(p.waktu, '%d-%b-%Y %H:%i') AS waktu,
               v.nama as namavendor, u.nama AS namauser, p.jenis, IFNULL(p.nis, '') AS nis, IFNULL(s.nama, '') AS namasiswa,
               IFNULL(p.nip, '') AS nip, IFNULL(pg.nama, '') AS namapegawai, p.jumlah, p.keterangan, p.jenistrans, p.iddatapenerimaan,
               IFNULL(dp.nama, '') AS namapenerimaan, IF(p.valmethod = 1, 'PIN', 'Agreement') AS valmethod, 
               p.keterangan, p.idjurnalvendor, IFNULL(a.departemen, '') AS departemensiswa
          FROM jbsfina.paymenttrans p
         INNER JOIN jbsfina.vendor v ON p.vendorid = v.vendorid
         INNER JOIN jbsfina.userpos u ON p.userid = u.userid
          LEFT JOIN jbsakad.siswa s ON p.nis = s.nis
          LEFT JOIN jbsakad.angkatan a ON s.idangkatan = a.replid
          LEFT JOIN jbssdm.pegawai pg ON p.nip = pg.nip
          LEFT JOIN jbsfina.datapenerimaan dp ON p.iddatapenerimaan = dp.replid
         WHERE transactionid = '$transId'";
$res = QueryDb($sql);
$row = mysqli_fetch_array($res);
$tanggal = $row["waktu"];
$petugas = $row["namauser"];
$vendor = $row["namavendor"];
$departemen = "";
if ($row["jenis"] == 2)
{
    $departemen = $row["departemensiswa"];
    $penerima = $row["namasiswa"] . " (" . $row["nis"] . ")";
}
else
{
    $penerima = $row["namapegawai"] . " (" . $row["nip"] . ")";

    $sql = "SELECT departemen
              FROM jbsfina.paymenttabungan
             WHERE jenis = 1";
    $res2 = QueryDb($sql);
    if ($row2 = mysqli_fetch_row($res2))
        $departemen = $row2[0];
}
$keterangan = $row["keterangan"];
$idJurnal = $row["idjurnalvendor"];

$noKas = "";
$sql = "SELECT nokas 
          FROM jbsfina.jurnal 
         WHERE replid = $idJurnal";
$res = QueryDb($sql);
if ($row = mysqli_fetch_row($res))
    $noKas = $row[0];

$rpJumlah = FormatRupiah($jumlah);
$transaksi = "pembayaran non-tunai untuk <strong>$vendor</strong> no transaksi <strong>$transId</strong>";

$sql = "SELECT replid, nama, alamat1,
               IF(foto IS NULL, 0, 1) AS fotoexist, 
               IF(foto IS NULL, '', TO_BASE64(foto)) AS foto64  
          FROM jbsumum.identitas 
         WHERE departemen='$departemen'";
$result = QueryDb($sql);
$row = @mysqli_fetch_array($result);
$idHeader = $row["replid"];
$namaHeader = $row["nama"];
$alamatHeader = $row["alamat1"];
$foto64 = $row['foto64'];
$fotoexist = $row['fotoexist'];
if ($fotoexist == 0)
    $foto64 = "/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAeAAD/4QMvaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjYtYzA2NyA3OS4xNTc3NDcsIDIwMTUvMDMvMzAtMjM6NDA6NDIgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2NDkwQjQ0N0E5NTAxMUYwOEUxQUJEQzU5NkY5MkMwOCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2NDkwQjQ0OEE5NTAxMUYwOEUxQUJEQzU5NkY5MkMwOCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjY0OTBCNDQ1QTk1MDExRjA4RTFBQkRDNTk2RjkyQzA4IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjY0OTBCNDQ2QTk1MDExRjA4RTFBQkRDNTk2RjkyQzA4Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQAEAsLCwwLEAwMEBcPDQ8XGxQQEBQbHxcXFxcXHx4XGhoaGhceHiMlJyUjHi8vMzMvL0BAQEBAQEBAQEBAQEBAQAERDw8RExEVEhIVFBEUERQaFBYWFBomGhocGhomMCMeHh4eIzArLicnJy4rNTUwMDU1QEA/QEBAQEBAQEBAQEBA/8AAEQgAQABAAwEiAAIRAQMRAf/EAEsAAQEAAAAAAAAAAAAAAAAAAAAHAQEAAAAAAAAAAAAAAAAAAAAAEAEAAAAAAAAAAAAAAAAAAAAAEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/9k=";

CloseDb();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <link rel="stylesheet" type="text/css" href="../style/style.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Kuitansi Pembayaran Non Tunai</title>
</head>

<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

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
                        <img style='width: 30px; height: 30px;' src='data:image/jpg;base64,<?=$foto64?>'>
                    </td>
                    <td align="left">
                        <font style='font-size:14px'><strong><?=$namaHeader?></strong></font><br>
                        <font style='font-size:10px'><?=$alamatHeader?></font>
                    </td>
                </tr>
<?php       } else { ?>
                <tr height="1">
                    <td align="center" width='15%'>&nbsp;</td>
                    <td align="left">&nbsp;</td>
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
                    Telah diterima dari:
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
                </td>
            </tr>
            </table>
        </td>
    </tr>
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
<script language="javascript">
    window.print();
</script>
</html>