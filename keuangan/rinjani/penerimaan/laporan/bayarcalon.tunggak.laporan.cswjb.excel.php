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
require_once('../../library/msg.php');
require_once('../../library/rupiah.php');
require_once('../../util/peek.php');
require_once('../../include/errorhandler.php');

header('Content-Type: application/vnd.ms-excel'); //IE and Opera
header('Content-Type: application/x-msexcel'); // Other browsers
header('Content-Disposition: attachment; filename=Laporan_Calon_Siswa_Menunggak.xls');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

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
$tanggal = RequestData("tanggal", date("Y-m-d"));
$telat = RequestData("telat", -1);
$urut = RequestData("urut", "s.nis");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Laporan Pembayaran Calon Siswa Menunggak</title>
</head>
<body style="margin: 10px;">

<?php
$sql = "SELECT replid 
          FROM jbsfina.tahunbuku 
         WHERE departemen = '$departemen' 
           AND aktif = 1";
$res = $db->QueryDb($sql);
$row = mysqli_fetch_row($res);
$idTahunBuku = $row[0];

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

<center><font size="4" face="Arial"><strong>LAPORAN SISWA YANG MENUNGGAK</strong></font><br /></center>
<br /><br /><br />

<table border="0">
    <tr>
        <td>Departemen:</td>
        <td><?=$departemen?></td>
    </tr>
    <tr>
        <td>Proses:</td>
        <td><?=$namaProses?></td>
    </tr>
    <tr>
        <td>Kelompok:</td>
        <td><?=$namaKelompok?></td>
    </tr>
    <tr>
        <td>Kategori:</td>
        <td><?=$namaKategori?></td>
    </tr>
    <tr>
        <td>Penerimaan:</td>
        <td><?=$namaPenerimaan?></td>
    </tr>
    <tr>
        <td>Telat:</td>
        <td><?=$telat?> hari dari tanggal <?= LongDateFormat($tanggal) ?></td>
    </tr>
</table>
<br>

<table border="0" cellpadding="0" cellspacing="2">
<tr>
    <td>Total <?= $namaPenerimaan ?></td>
    <td><?= FormatRupiah($besarjttallA) ?></td>
</tr>
<tr>
    <td>Total Pembayaran</td>
    <td><?= FormatRupiah($totalbayarallB) ?></td>
</tr>
<tr>
    <td>Total Diskon</td>
    <td><?= FormatRupiah($totaldiskonallB) ?></td>
</tr>
<tr>
    <td>Total Tunggakan</td>
    <td><?=  FormatRupiah($besarjttallA - $totalbayarallB - $totaldiskonallB) ?></td>
</tr>
</table>
<br>



<table class="tab" id="table" border="1" style="border-collapse:collapse"  align="left" cellpadding="5" cellspacing="0">
<tr height="30" align="center" class="header">
    <td>No</td>
    <td>No Pendaftaran</td>
    <td>Nama</td>
    <td>Kelas</td>
    <?php
    for ($i = 0; $i < $max_n_cicilan; $i++)
    {
        $n = $i + 1;
        echo "<td colspan='2'>Bayaran-$n</td>";
    }
    ?>
    <td>Telat<br/><em>(hari)</em></td>
    <td><?= $namaPenerimaan ?></td>
    <td>Total Pembayaran</td>
    <td>Total Diskon</td>
    <td>Total Tunggakan</td>
    <td>Keterangan</td>
</tr>
<?php
    $nRow = 0;
    $cnt = 0;
    $sql = "SELECT b.idcalon, c.nopendaftaran, c.nama, k.kelompok, b.replid AS id, b.besar, b.keterangan, b.lunas, p.proses 
              FROM jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k, jbsfina.besarjttcalon b, jbsakad.prosespenerimaansiswa p 
             WHERE c.replid = b.idcalon 
               AND c.idkelompok = k.replid 
               AND k.idproses = p.replid 
               AND b.replid IN ($idstr) 
             ORDER BY $urut ASC";
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
        <tr>
            <td><?= ++$cnt ?></td>
            <td><?= $row['nopendaftaran'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td>
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
                    <td><?= FormatRupiah($row2[1]) ?></td>
                    <td><?=$row2[0] ?></td>
<?php           }
                $totalbayarall += $totalbayar - $totaldiskon;
            }

            for ($i = 0; $i < $nblank; $i++)
            {
                echo "<td>&nbsp;</td>";
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
                echo $row2[0];
?>
            </td>
            <td align="right"><?= FormatRupiah($besarjtt) ?></td>
            <td align="right"><?= FormatRupiah($totalbayar) ?></td>
            <td align="right"><?= FormatRupiah($totaldiskon) ?></td>
            <td align="right"><?= FormatRupiah($besarjtt - $totalbayar - $totaldiskon) ?></td>
            <td>
                <?= $ketjtt ?>
            </td>
        </tr>
        <?php
    }
?>
</table>

</body>
</html>