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
require_once('../../library/departemen.php');
require_once('../../library/msg.php');
require_once('../../library/rupiah.php');
require_once('../../library/userinfo.php');
require_once('../../util/peek.php');
require_once('../../include/errorhandler.php');
require_once('bayarsiswa.kelas.jtt.func.php');
require_once('bayarsiswa.tunggak.laporan.jtt.func.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idTingkat = RequestData("idtingkat", 0);
$namaTingkat = RequestData("namatingkat", "");
$idKelas = RequestData("idkelas", 0);
$namaKelas = RequestData("namakelas", "");
$idKategori = RequestData("idkategori", 0);
$namaKategori = RequestData("namakategori", "");
$idPenerimaan = RequestData("idpenerimaan", 0);
$namaPenerimaan = RequestData("namapenerimaan", "");
$urut = RequestData("urut", "s.nis");
$page = RequestData("page", 1);
$tanggal = RequestData("tanggal", date("Y-m-d"));
$telat = RequestData("telat",30);

$formatNotif = DefaultFormatNotif($db);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Pembayaran Siswa Yang Menunggak</title>
    <link rel="stylesheet" type="text/css" href="../../style/style.css?<?=filemtime('../../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../../script/tables.js"></script>
    <script language="javascript" src="../../script/tools.js"></script>
    <script language="javascript" src="../../script/toast.js"></script>
    <script language="javascript" src="../../script/vldr.js"></script>
    <script language="javascript" src="../../script/dialogbox.js"></script>
    <script language="javascript" src="../../script/qsbuilder.js"></script>
    <script language="javascript" src="bayarsiswa.tunggak.laporan.jtt.js?r=<?=filemtime('bayarsiswa.tunggak.laporan.jtt.js')?>"></script>
</head>
<body style="margin: 10px;">
<input type="hidden" id="formatnotif" value="<?= $formatNotif ?>">
<?php
$sql = "SELECT replid 
          FROM jbsfina.tahunbuku 
         WHERE departemen = '$departemen' 
           AND aktif = 1";
$res = $db->QueryDb($sql);
if (mysqli_num_rows($res) == 0)
{
    $db->Close();

    echo "<span style='color: maroon; font-size: 13px;'>";
    echo "Belum ada Tahun buku yang Aktif di departemen $departemen. Silakan isi/aktifkan Tahun Buku di menu Referensi";
    echo "</span>";

    exit();
}
$row = mysqli_fetch_row($res);
$idTahunBuku = $row[0];
echo "<input type='hidden' id='idtahunbuku' value='$idTahunBuku'>";

if ($idKelas == -1)
{
    $sql = "SELECT idbesarjtt, datediff('$tanggal', max(tanggal)) as x 
              FROM jbsfina.penerimaanjtt p , jbsfina.besarjtt b, jbsakad.siswa s, jbsakad.kelas k 
             WHERE p.idbesarjtt = b.replid 
               AND b.lunas = 0 
               AND b.info2 = '$idTahunBuku' 
               AND b.idpenerimaan = $idPenerimaan
               AND s.nis = b.nis 
               AND s.idkelas = k.replid 
               AND k.idtingkat = $idTingkat 
             GROUP BY idbesarjtt 
            HAVING x >= $telat";
}
else
{
    $sql = "SELECT idbesarjtt, datediff('$tanggal', max(tanggal)) as x 
              FROM jbsfina.penerimaanjtt p, jbsfina.besarjtt b, jbsakad.siswa s 
             WHERE p.idbesarjtt = b.replid 
               AND b.lunas = 0 
               AND b.info2='$idTahunBuku' 
               AND b.idpenerimaan = $idPenerimaan
               AND s.nis = b.nis 
               AND s.idkelas = $idKelas 
             GROUP BY idbesarjtt 
            HAVING x >= $telat";
}

$result = $db->QueryDb($sql);
$idstr = "";
while($row = mysqli_fetch_row($result))
{
    if ($idstr != "") $idstr .= ",";
    $idstr .= $row[0];
}

if ($idstr == "")
{
    $db->Close();

    echo "<span style='color: maroon; font-size: 13px;'>";
    echo "Belum ada siswa yang memiliki tunggakan <b>$namaPenerimaan</b> terhitung <b>$telat</b> hari dari tanggal <b>" . LongDateFormat($tanggal) . "</b>";
    echo "</span>";

    exit();
}
?>

<table border="0" width="100%" align="center">
<tr>
    <td>

<?php
    $sql = "SELECT MAX(jumlah) 
                 FROM (SELECT idbesarjtt, count(replid) AS jumlah 
                         FROM jbsfina.penerimaanjtt 
                        WHERE idbesarjtt IN ($idstr) 
                    GROUP BY idbesarjtt) AS X";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);

    $max_n_cicilan = $row[0];
    $table_width = 1200 + $max_n_cicilan * 120;

    $sql_tot = "SELECT b.replid AS id, b.besar
                  FROM jbsfina.besarjtt b 
                 WHERE b.replid IN ($idstr)";
    $result_tot = $db->QueryDb($sql_tot);
    $nData = mysqli_num_rows($result_tot);

    $totalbiayaall = 0;
    $totalbayarall = 0;
    $totaldiskonall = 0;

    $totalbayarallB = 0;
    $totaldiskonallB = 0;
    $besarjttallA = 0;

    while ($rowA = @mysqli_fetch_array($result_tot))
    {
        $idbesarjttA = $rowA['id'];
        $besarjttA = $rowA['besar'];

        $sqlB = "SELECT SUM(jumlah), SUM(info1) 
                   FROM jbsfina.penerimaanjtt 
                  WHERE idbesarjtt = $idbesarjttA";
        $resultB = $db->QueryDb($sqlB);
        $rowB = mysqli_fetch_row($resultB);
        $totalbayarB = $rowB[0];
        $totaldiskonB = $rowB[1];

        $totalbayarallB += $totalbayarB;
        $totaldiskonallB += $totaldiskonB;
        $besarjttallA += $besarjttA;
    }
?>

<div id="dvLaporan">

    <table border="0" cellpadding="0" cellspacing="2">
    <tr>
        <td width="180">
            <span style='color: #999'>Total <?= $namaPenerimaan ?></span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($besarjttallA) ?></span>
        </td>
        <td width="180">
            <span style='color: #999'>Total Pembayaran</span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($totalbayarallB) ?></span>
        </td>
        <td width="180">
            <span style='color: #999'>Total Diskon</span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($totaldiskonallB) ?></span>
        </td>
        <td width="180">
            <span style='color: #999'>Total Tunggakan</span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($besarjttallA - $totalbayarallB - $totaldiskonallB) ?></span>
        </td>
        <td width="300" align="right" valign="bottom">
            <div class="hide-in-report">
                <a href="JavaScript:refresh()"><img src="../../images/ico/refresh.png" border="0">&nbsp;refresh</a>&nbsp;
                <a href="JavaScript:cetak()"><img src="../../images/ico/print.png" border="0">&nbsp;cetak</a>&nbsp;
                <a href="JavaScript:excel()"><img src="../../images/ico/excel.png" border="0">&nbsp;excel</a>&nbsp;
            </div>
        </td>
    </tr>
    </table>

    <br>

    <table class="tab" id="table" border="1" style="border-collapse:collapse" width="<?= $table_width ?>" align="left" cellpadding="5" cellspacing="0">
    <tr height="30" align="center" class="header">
        <td width="30">No</td>
        <td width="50" align="center">
            Kirim<br>
            <input type="checkbox" id="ckKirimToggle" onclick="onCkKirimChange()">
        </td>
        <td width="80" style="cursor:pointer; color: <?php ColumnColor("s.nis") ?>;" onClick="onChangeUrut('s.nis')">NIS</td>
        <td width="140" style="cursor:pointer; color: <?php ColumnColor("s.nama") ?>;" onClick="onChangeUrut('s.nama')">Nama</td>
        <td width="50">Kelas</td>
<?php
    for ($i = 0; $i < $max_n_cicilan; $i++)
    {
        $n = $i + 1;
        echo "<td class='header' width='120' align='center'>Bayaran-$n</td>";
    }
?>
        <td width="80">Telat<br/><em>(hari)</em></td>
        <td width="125" style="cursor:pointer; color: <?php ColumnColor("b.besar") ?>;" onClick="onChangeUrut('b.besar')"><?= $namaPenerimaan ?></td>
        <td width="125">Total Pembayaran</td>
        <td width="125">Total Diskon</td>
        <td width="125">Total Tunggakan</td>
        <td width="200" align="center">Keterangan</td>
    </tr>
<?php

    $startFromIndex = ($page - 1) * $nRowPerPage;
    $totalPage = ceil($nData / $nRowPerPage);

    $nRow = 0;
    $cnt = $startFromIndex;
    $sql = "SELECT b.nis, s.nama, k.kelas, b.replid AS id, b.besar, b.keterangan, b.lunas, t.tingkat 
              FROM jbsakad.siswa s, jbsakad.kelas k, jbsfina.besarjtt b, jbsakad.tingkat t 
             WHERE s.nis = b.nis 
               AND s.idkelas = k.replid 
               AND k.idtingkat = t.replid 
               AND b.replid IN ($idstr) 
             ORDER BY $urut ASC
             LIMIT $startFromIndex, $nRowPerPage";
    $result = $db->QueryDb($sql);
    while ($row = mysqli_fetch_array($result))
    {
        $nRow += 1;

        $idbesarjtt = $row['id'];
        $besarjtt = $row['besar'];
        $ketjtt = $row['keterangan'];
        $lunasjtt = $row['lunas'];

        $infojtt = "<font color=red><strong>Belum Lunas</strong></font>";
        if ($lunasjtt == 1)
            $infojtt = "<font color=blue><strong>Lunas</strong></font>";
        $totalbiayaall += $besarjtt;

        $nama = $row['nama'];
        $nis = $row['nis'];

?>
        <tr height="40">
            <td align="center" class="numberColumn"><?= ++$cnt ?></td>
            <td align="center">
                <input type="checkbox" id="ckKirim-<?= $nRow ?>"">
            </td>
            <td align="left">
                <a class="ablue" onclick="showInfoSiswa('<?=$row['nis'] ?>')">
                    <?= $row['nis'] ?>
                </a>
            </td>
            <td><?= $row['nama'] ?></td>
            <td align="center">
                <?php if ($idKelas == -1) echo $row['tingkat'] . " - "; ?>
                <?php echo $row['kelas'] ?>
            </td>
<?php
            $sql = "SELECT count(*) 
                      FROM jbsfina.penerimaanjtt 
                     WHERE idbesarjtt = $idbesarjtt";
            $result2 = $db->QueryDb($sql);
            $row2 = mysqli_fetch_row($result2);

            $nbayar = $row2[0];
            $nblank = $max_n_cicilan - $nbayar;
            $totalbayar = 0;
            $totaldiskon = 0;

            if ($nbayar > 0)
            {
                $sql = "SELECT date_format(tanggal, '%d-%b-%y'), jumlah, info1 
                          FROM jbsfina.penerimaanjtt 
                         WHERE idbesarjtt = $idbesarjtt 
                         ORDER BY tanggal";
                $result2 = $db->QueryDb($sql);

                while ($row2 = mysqli_fetch_row($result2))
                {
                    $totalbayar = $totalbayar + $row2[1];
                    $totaldiskon = $totaldiskon + $row2[2];
?>
                    <td>
                        <table border="1" class="tab" cellpadding="0" cellspacing="0" width="100%"style="border-collapse:collapse">
                        <tr height="20"><td align="center"><?= FormatRupiah($row2[1]) ?></td></tr>
                        <tr height="20"><td align="center"><?= $row2[0] ?></td></tr>
                        </table>
                    </td>
<?php           }
                $totalbayarall += $totalbayar - $totaldiskon;
            }

            for ($i = 0; $i < $nblank; $i++) {
                    echo "<td>&nbsp;</td>";
            }
?>
            <td align="center">
<?php
                $sql = "SELECT datediff('$tanggal', max(tanggal)) 
                          FROM jbsfina.penerimaanjtt 
                         WHERE idbesarjtt = $idbesarjtt";
                $result2 = $db->QueryDb($sql);
                $row2 = mysqli_fetch_row($result2);
                echo "<span style='color: red; font-weight: bold'>$row2[0]</span>";
?>
            </td>
            <td align="right"><?= FormatRupiah($besarjtt) ?></td>
            <td align="right"><?= FormatRupiah($totalbayar) ?></td>
            <td align="right"><?= FormatRupiah($totaldiskon) ?></td>
            <td align="right"><span style='color: red;'><?= FormatRupiah($besarjtt - $totalbayar - $totaldiskon) ?></span></td>
            <td>
                <?= $ketjtt ?>
<?php           $tunggakan = $besarjtt - $totalbayar - $totaldiskon ?>
                <input type="hidden" id="tunggakan-<?= $nRow ?>" value='<?= $tunggakan ?>'>
                <input type="hidden" id="nama-<?= $nRow ?>" value='<?= $nama ?>'>
                <input type="hidden" id="nis-<?= $nRow ?>" value='<?= $nis ?>'>
            </td>
        </tr>
<?php
    }
?>
    </table>

    </td>
</tr>
</table>

</div>

<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idtingkat" value="<?= $idTingkat ?>">
<input type="hidden" id="namatingkat" value="<?= $namaTingkat  ?>">
<input type="hidden" id="idkelas" value="<?= $idKelas  ?>">
<input type="hidden" id="namakelas" value="<?= $namaKelas ?>">
<input type="hidden" id="idkategori" value="<?= $idKategori  ?>">
<input type="hidden" id="namakategori" value="<?= $namaKategori  ?>">
<input type="hidden" id="idpenerimaan" value="<?= $idPenerimaan ?>">
<input type="hidden" id="namapenerimaan" value="<?= $namaPenerimaan  ?>">
<input type="hidden" id="urut" value="<?= $urut ?>">
<input type="hidden" id="totalpage" value="<?= $totalPage ?>">
<input type="hidden" id="tanggal" value="<?= $tanggal ?>">
<input type="hidden" id="telat" value="<?= $telat ?>">
<input type="hidden" id="nrow" value="<?= $nRow ?>">

<?php
echo "<div id='dvPageControl' style='width: 100%'>";
echo "Halaman&nbsp;&nbsp;";
echo "<input type='button' class='but' style='height: 28px; width: 28px' value=' < ' onclick='onPrevPage()'>";
echo "<select id='page' class='inputbox' style='width: 50px' onchange='onChangePage()'>";
for ($i = 1; $i <= $totalPage; $i++)
{
    $sel = $i == $page ? "selected" : "";
    echo "<option value='$i' $sel>$i</option>";
}
echo "</select>";
echo "<input type='button' class='but' style='height: 28px; width: 28px' value=' > ' onclick='onNextPage()'>";
echo "&nbsp;dari $totalPage, jumlah $nData data";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "Kirim pesan tunggakan kepada siswa dan orangtua &nbsp;&nbsp;";
echo "(<a href='JavaScript:showFormatPesanDialog()' style='font-weight: normal; color: blue;'>format pesan tunggakan</a>) &nbsp;&nbsp;";
echo "<input type='button' class='but' id='btKirim' onclick='sendFormatNotif()' value='Kirim' style='width: 60px; height: 30px'>";
echo "&nbsp;&nbsp;<span id='spInfo' style='color: #666; visibility: hidden'>memuat ..</span>";
echo "</div>";
?>

<div id="divDialog">
    <h3>Format Notifikasi</h3>
    <textarea rows="7" cols="50" class="inputbox" id="txformat"></textarea><br>
    <input type="button" class="dialogButtonPositive" value="Simpan" onclick="saveFormatNotif()">
    <input type="button" class="dialogButtonNegative" value="Tutup" onclick="closeFormatNotifDialog()">
</div>
<div id="toast-container"></div>
</body>
</html>