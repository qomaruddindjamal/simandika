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
require_once('bayarcalon.kelompok.cswjb.func.php');
require_once('bayarcalon.tunggak.laporan.cswjb.func.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idProses = RequestData("idproses", 0);
$namaProses = RequestData("namaproses", "");
$idKelompok = RequestData("idkelompok", 0);
$namaKelompok = RequestData("namakelompok", "");
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
    <title>Laporan Pembayaran Calon Siswa Yang Menunggak</title>
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
    <script language="javascript" src="bayarcalon.tunggak.laporan.cswjb.js?r=<?=filemtime('bayarcalon.tunggak.laporan.cswjb.js')?>"></script>
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

if ($idKelompok == -1)
{
    $sql = "SELECT idbesarjttcalon, datediff('$tanggal', max(tanggal)) as x 
              FROM jbsfina.penerimaanjttcalon p , jbsfina.besarjttcalon b, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k 
             WHERE p.idbesarjttcalon = b.replid 
               AND b.lunas = 0 
               AND b.info2 = '$idTahunBuku' 
               AND b.idpenerimaan = $idPenerimaan
               AND c.replid = b.idcalon 
               AND c.idkelompok = k.replid 
               AND k.idproses = $idProses 
             GROUP BY idbesarjttcalon 
            HAVING x >= $telat";
}
else
{
    $sql = "SELECT idbesarjttcalon, datediff('$tanggal', max(tanggal)) as x 
              FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsakad.calonsiswa c 
             WHERE p.idbesarjttcalon = b.replid 
               AND b.lunas = 0 
               AND b.info2 = '$idTahunBuku' 
               AND b.idpenerimaan = $idPenerimaan
               AND c.replid = b.idcalon 
               AND c.idkelompok = $idKelompok 
             GROUP BY idbesarjttcalon 
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
    echo "Belum ada calon siswa yang memiliki tunggakan <b>$namaPenerimaan</b> terhitung <b>$telat</b> hari dari tanggal <b>" . LongDateFormat($tanggal) . "</b>";
    echo "</span>";

    exit();
}
?>

<table border="0" width="100%" align="center">
<tr>
    <td>

<?php
    $sql = "SELECT MAX(jumlah) 
              FROM (SELECT idbesarjttcalon, count(replid) AS jumlah 
                      FROM jbsfina.penerimaanjttcalon 
                     WHERE idbesarjttcalon IN ($idstr) 
                     GROUP BY idbesarjttcalon) AS X";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_row($result);

    $max_n_cicilan = $row[0];
    $table_width = 1200 + $max_n_cicilan * 120;

    $sql_tot = "SELECT b.replid AS id, b.besar
                  FROM jbsfina.besarjttcalon b 
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
                   FROM jbsfina.penerimaanjttcalon 
                  WHERE idbesarjttcalon = $idbesarjttA";
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
        <td width="180" valign="top">
            <span style='color: #999'>Total <?= $namaPenerimaan ?></span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($besarjttallA) ?></span>
        </td>
        <td width="180" valign="top">
            <span style='color: #999'>Total Pembayaran</span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($totalbayarallB) ?></span>
        </td>
        <td width="180" valign="top">
            <span style='color: #999'>Total Diskon</span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($totaldiskonallB) ?></span>
        </td>
        <td width="180" valign="top">
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
        <td width="80" style="cursor:pointer; color: <?php ColumnColor("c.nopendaftaran") ?>;" onClick="onChangeUrut('c.nopendaftaran')">No Pendaftaran</td>
        <td width="140" style="cursor:pointer; color: <?php ColumnColor("c.nama") ?>;" onClick="onChangeUrut('c.nama')">Nama</td>
        <td width="50">Kelompok</td>
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
        $sql = "SELECT b.idcalon, c.nopendaftaran, c.nama, k.kelompok, b.replid AS id, b.besar, b.keterangan, b.lunas, p.proses 
                  FROM jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k, jbsfina.besarjttcalon b, jbsakad.prosespenerimaansiswa p 
                 WHERE c.replid = b.idcalon 
                   AND c.idkelompok = k.replid 
                   AND k.idproses = p.replid 
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
            $nic = $row['nopendaftaran'];
            $idcalon = $row['idcalon'];

?>
            <tr height="40">
                <td align="center" class="numberColumn"><?= ++$cnt ?></td>
                <td align="center">
                    <input type="checkbox" id="ckKirim-<?= $nRow ?>"">
                </td>
                <td align="left">
                    <a class="ablue" onclick="showInfoCalonSiswa('<?=$row['nopendaftaran'] ?>')">
                        <?= $row['nopendaftaran'] ?>
                    </a>
                </td>
                <td align="left"><?= $row['nama'] ?></td>
                <td align="left">
                    <?php if ($idKelompok == -1) echo $row['proses'] . " - "; ?>
                    <?php echo $row['kelompok'] ?>
                </td>
<?php
                $sql = "SELECT count(*) 
                          FROM jbsfina.penerimaanjttcalon 
                         WHERE idbesarjttcalon = $idbesarjtt";
                $result2 = $db->QueryDb($sql);
                $row2 = mysqli_fetch_row($result2);

                $nbayar = $row2[0];
                $nblank = $max_n_cicilan - $nbayar;
                $totalbayar = 0;
                $totaldiskon = 0;

                if ($nbayar > 0)
                {
                    $sql = "SELECT date_format(tanggal, '%d-%b-%y'), jumlah, info1 
                              FROM jbsfina.penerimaanjttcalon 
                             WHERE idbesarjttcalon = $idbesarjtt 
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
<?php               }
                    $totalbayarall += $totalbayar - $totaldiskon;
                }

                for ($i = 0; $i < $nblank; $i++) {
                    echo "<td>&nbsp;</td>";
                }
?>
                <td align="center">
<?php
                $sql = "SELECT datediff('$tanggal', max(tanggal)) 
                          FROM jbsfina.penerimaanjttcalon 
                         WHERE idbesarjttcalon = $idbesarjtt";
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
<?php               $tunggakan = $besarjtt - $totalbayar - $totaldiskon ?>
                    <input type="hidden" id="tunggakan-<?= $nRow ?>" value='<?= $tunggakan ?>'>
                    <input type="hidden" id="nama-<?= $nRow ?>" value='<?= $nama ?>'>
                    <input type="hidden" id="nic-<?= $nRow ?>" value='<?= $nic ?>'>
                    <input type="hidden" id="idcalon-<?= $nRow ?>" value='<?= $idcalon ?>'>
                </td>
            </tr>
<?php
        } // end while
?>
    </table>

        </td>
    </tr>
</table>

</div>

<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idproses" value="<?= $idProses ?>">
<input type="hidden" id="namaproses" value="<?= $namaProses  ?>">
<input type="hidden" id="idkelompok" value="<?= $idKelompok  ?>">
<input type="hidden" id="namakelompok" value="<?= $namaKelompok ?>">
<input type="hidden" id="idkategori" value="<?= $idKategori  ?>">
<input type="hidden" id="namakategori" value="<?= $namaKategori  ?>">
<input type="hidden" id="idpenerimaan" value="<?= $idPenerimaan ?>">
<input type="hidden" id="namapenerimaan" value="<?= $namaPenerimaan  ?>">
<input type="hidden" id="urut" value="<?= $urut ?>">
<input type="hidden" id="tanggal" value="<?= $tanggal ?>">
<input type="hidden" id="telat" value="<?= $telat ?>">
<input type="hidden" id="nrow" value="<?= $nRow ?>">

<?php
echo "<div id='dvPageControl' style='width: 100%'>";
echo "Halaman&nbsp;&nbsp;";
echo "<select id='page' class='inputbox' style='width: 50px' onchange='onChangePage()'>";
for ($i = 1; $i <= $totalPage; $i++)
{
    $sel = $i == $page ? "selected" : "";
    echo "<option value='$i' $sel>$i</option>";
}
echo "</select>";
echo "&nbsp;dari $totalPage, jumlah $nData data";
echo "&nbsp;&nbsp;<input type='button' class='but' id='btKirim' onclick='sendFormatNotif()' value='Kirim' style='width: 60px; height: 30px'>&nbsp;pesan tunggakan&nbsp;&nbsp;";
echo "(<a href='JavaScript:showFormatPesanDialog()' style='font-weight: normal; color: blue;'>atur format pesan notifikasi</a>)";
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