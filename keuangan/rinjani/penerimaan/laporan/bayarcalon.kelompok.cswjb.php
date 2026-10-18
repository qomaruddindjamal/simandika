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
require_once('bayarcalon.kelompok.cswjb.func.php');

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
$status = RequestData("status", -1);
$namaStatus = RequestData("namastatus", -1);
$urut = RequestData("urut", "s.nis");
$page = RequestData("page", 1);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Pembayaran Calon Siswa Per Kelompok</title>
    <link rel="stylesheet" type="text/css" href="../../style/style.css?<?=filemtime('../../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../../script/tables.js"></script>
    <script language="javascript" src="../../script/tools.js"></script>
    <script language="javascript" src="../../script/toast.js"></script>
    <script language="javascript" src="../../script/vldr.js"></script>
    <script language="javascript" src="../../script/qsbuilder.js"></script>
    <script language="javascript" src="bayarcalon.kelompok.cswjb.js?r=<?=filemtime('bayarcalon.kelompok.cswjb.js')?>"></script>
</head>
<body style="margin: 10px;">

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

if ($status == -1)
{
    if ($idKelompok == -1)
    {
        // semua kelompok di proses terpilih
        $sql = "SELECT MAX(jumlah), COUNT(replid) 
		          FROM ((SELECT c.replid, count(p.replid) as jumlah 
                           FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsakad.calonsiswa c 
                          WHERE p.idbesarjttcalon = b.replid 
                            AND b.idcalon = c.replid 
                            AND b.info2 = '$idTahunBuku'
						    AND b.idpenerimaan = '$idPenerimaan' 
						  GROUP BY c.replid) AS x)";
    }
    else
    {
        // proses & kelompok terpilih
        $sql = "SELECT MAX(jumlah), COUNT(replid) 
		          FROM ((SELECT c.replid, count(p.replid) as jumlah 
                           FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsakad.calonsiswa c 
						  WHERE p.idbesarjttcalon = b.replid 
						    AND b.idcalon = c.replid 
						    AND b.info2 = '$idTahunBuku'
                            AND c.idkelompok = '$idKelompok' 
                            AND b.idpenerimaan = '$idPenerimaan' 
                          GROUP BY c.replid) AS x)";
    }
}
else
{
    if ($idKelompok == -1)
    {
        // semua kelas di tingkat terpilih
        $sql = "SELECT MAX(jumlah), COUNT(replid)
		          FROM ((SELECT c.replid, count(p.replid) as jumlah 
					       FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsakad.calonsiswa c 
						  WHERE p.idbesarjttcalon = b.replid 
						    AND b.idcalon = c.replid 
						    AND b.info2 = '$idTahunBuku' 
							AND b.idpenerimaan = '$idPenerimaan' 
							AND b.lunas = '$status' 
					      GROUP BY c.replid) AS x);";
    }
    else
    {
        // tingkat & kelas terpilih
        $sql = "SELECT MAX(jumlah), COUNT(replid) 
		          FROM ((SELECT c.replid, count(p.replid) as jumlah 
                           FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsakad.calonsiswa c 
					  	  WHERE p.idbesarjttcalon = b.replid 
					  	    AND b.idcalon = c.replid 
					  	    AND b.info2 = '$idTahunBuku' 
							AND c.idkelompok = '$idKelompok' 
							AND b.idpenerimaan = '$idPenerimaan' 
							AND b.lunas = '$status' 
						  GROUP BY c.replid) AS x);";
    }
}
//Peek::Show($sql);
// -- calculate table width ----------------------------------------
$row = $db->FetchSingleRow($sql);
$max_n_cicilan = $row[0];
$ndata = $row[1];

if ($max_n_cicilan == 0 && $ndata == 0)
{
    echo "<span style='color: maroon; font-size: 13px;'>";
    echo "Belum data pembayaran di kelompok terpilih";
    echo "</span>";
    exit();
}

$table_width = 1115 + $max_n_cicilan * 120;
$startFromIndex = ($page - 1) * $nRowPerPage;
$totalPage = ceil($ndata / $nRowPerPage);

if ($status == -1)
{
    if ($idKelompok == -1)
    {
        // semua kelompok di proses terpilih
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBiaya
						    FROM jbsfina.besarjttcalon b, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k 
						   WHERE c.replid = b.idcalon 
						     AND b.idpenerimaan = '$idPenerimaan' 
						     AND b.info2 = '$idTahunBuku' 
						     AND c.idkelompok = k.replid";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon 
								   FROM jbsfina.penerimaanjttcalon p 
								  RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k 
								  WHERE c.replid = b.idcalon 
								    AND b.idpenerimaan = '$idPenerimaan' 
								    AND b.info2 = '$idTahunBuku' 
								    AND c.idkelompok = k.replid";

        $sql = "SELECT DISTINCT c.nopendaftaran, c.nama, k.kelompok, b.replid AS id, b.besar, b.keterangan, b.lunas 
		          FROM jbsfina.penerimaanjttcalon p RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
				 WHERE c.replid = b.idcalon 
				   AND b.idpenerimaan = '$idPenerimaan' 
				   AND b.info2 = '$idTahunBuku'
				   AND c.idkelompok = k.replid 
				 ORDER BY $urut ASC 
				 LIMIT $startFromIndex, $nRowPerPage";
    }
    else
    {
        // proses & kelompok terpilih
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBiaya
						    FROM jbsfina.besarjttcalon b, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
						   WHERE c.replid = b.idcalon 
						     AND b.idpenerimaan = '$idPenerimaan' 
						     AND b.info2 = '$idTahunBuku'
							 AND c.idkelompok = '$idKelompok' 
							 AND c.idkelompok = k.replid";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon  
								   FROM jbsfina.penerimaanjttcalon p 
								  RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
								  WHERE c.replid = b.idcalon 
								    AND b.idpenerimaan = '$idPenerimaan' 
								    AND b.info2 = '$idTahunBuku'
								    AND c.idkelompok = '$idKelompok' 
								    AND c.idkelompok = k.replid";

        $sql = "SELECT DISTINCT c.nopendaftaran, c.nama, k.kelompok, b.replid AS id, b.besar, b.keterangan, b.lunas 
		          FROM jbsfina.penerimaanjttcalon p 
		         RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
				 WHERE c.replid = b.idcalon 
				   AND b.idpenerimaan = '$idPenerimaan' 
				   AND b.info2 = '$idTahunBuku' 
				   AND c.idkelompok = '$idKelompok' 
				   AND c.idkelompok = k.replid 
				 ORDER BY $urut ASC
				 LIMIT $startFromIndex, $nRowPerPage";
    }
}
else
{
    if ($idKelompok == -1)
    {
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBiaya  
							FROM jbsfina.besarjttcalon b, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
						   WHERE c.replid = b.idcalon 
							 AND b.idpenerimaan = '$idPenerimaan' 
							 AND b.info2 = '$idTahunBuku'
							 AND c.idkelompok = k.replid 
							 AND b.lunas = '$status'";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon  
								   FROM jbsfina.penerimaanjttcalon p 
								  RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
								  WHERE c.replid = b.idcalon 
								    AND b.idpenerimaan = '$idPenerimaan' 
								    AND b.info2 = '$idTahunBuku'
									AND c.idkelompok = k.replid 
									AND b.lunas = '$status'";

        $sql = "SELECT DISTINCT c.nopendaftaran, c.nama, k.kelompok, b.replid AS id, b.besar, b.keterangan, b.lunas 
				  FROM jbsfina.penerimaanjttcalon p RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
				 WHERE c.replid = b.idcalon 
				   AND b.idpenerimaan = '$idPenerimaan' 
				   AND b.info2 = '$idTahunBuku' 
				   AND c.idkelompok = k.replid 
				   AND b.lunas = '$status' 
				 ORDER BY $urut ASC 
				 LIMIT $startFromIndex, $nRowPerPage";
    }
    else
    {
        $sql_sum_biaya = "SELECT SUM(b.besar) AS TotalBayar  
							FROM jbsfina.besarjttcalon b, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
						   WHERE c.replid = b.idcalon 
						     AND b.idpenerimaan = '$idPenerimaan' 
						     AND b.info2 = '$idTahunBuku'
							 AND c.idkelompok = '$idKelompok' 
							 AND c.idkelompok = k.replid AND b.lunas = '$status'";

        $sql_sum_bayar_diskon = "SELECT SUM(p.jumlah) AS TotalBayar, SUM(p.info1) AS TotalDiskon
								   FROM jbsfina.penerimaanjttcalon p 
								  RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
								  WHERE c.replid = b.idcalon 
								    AND b.idpenerimaan = '$idPenerimaan' 
								    AND b.info2 = '$idTahunBuku'
								    AND c.idkelompok = '$idKelompok' 
								    AND c.idkelompok = k.replid 
								    AND b.lunas = '$status'";

        $sql = "SELECT DISTINCT c.nopendaftaran, c.nama, k.kelompok, b.replid AS id, b.besar, b.keterangan, b.lunas 
		          FROM jbsfina.penerimaanjttcalon p RIGHT JOIN jbsfina.besarjttcalon b ON p.idbesarjttcalon = b.replid, jbsakad.calonsiswa c, jbsakad.kelompokcalonsiswa k
				 WHERE c.replid = b.idcalon 
				   AND b.idpenerimaan = '$idPenerimaan' 
				   AND b.info2 = '$idTahunBuku'
				   AND c.idkelompok = '$idKelompok' 
				   AND c.idkelompok = k.replid 
				   AND b.lunas = '$status' 
				 ORDER BY $urut ASC 
				 LIMIT $startFromIndex, $nRowPerPage";
    }
}

$totalBiayaAll = $db->FetchSingle($sql_sum_biaya, 0);
$row = $db->FetchSingleRow($sql_sum_bayar_diskon);
$totalBayarAll = $row[0] + $row[1];
$totalDiskonAll = $row[1];
?>
<br>

<div id="dvLaporan">
    <table border="0" cellpadding="0" cellspacing="2">
    <tr>
        <td width="180" valign="top">
            <span style='color: #999'>Total <?= $namaPenerimaan ?></span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($totalBiayaAll) ?></span>
        </td>
        <td width="180" valign="top">
            <span style='color: #999'>Total Pembayaran</span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($totalBayarAll) ?></span>
        </td>
        <td width="180" valign="top">
            <span style='color: #999'>Total Diskon</span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($totalDiskonAll) ?></span>
        </td>
        <td width="180" valign="top">
            <span style='color: #999'>Total Tunggakan</span><br>
            <span style='color: #333; font-size: 18px'><?= FormatRupiah($totalBiayaAll - $totalBayarAll) ?></span>
        </td>
        <td width="300" align="right" valign="bottom">
            <div class="hide-in-report">
                <a href="JavaScript:refresh()"><img src="../../images/ico/refresh.png" border="0"/>&nbsp;refresh</a>&nbsp;&nbsp;
                <a href="JavaScript:cetak()"><img src="../../images/ico/print.png" border="0"/>&nbsp;cetak</a>&nbsp;&nbsp;
                <a href="JavaScript:excel()"><img src="../../images/ico/excel.png" border="0"/>&nbsp;excel</a>
            </div>
        </td>
    </tr>
    </table>
    <br><br>

    <table class="tab" id="table" border="1" cellpadding="5" style="border-collapse:collapse" cellspacing="0" width="<?=$table_width?>">
    <tr height="30" align="center" class="header">
        <td width="30">No</td>
        <td width="80" style="cursor:pointer; color: <?php ColumnColor("c.nopendaftaran") ?>;" onClick="changeUrutan('c.nopendaftaran')">No Calon</td>
        <td width="140" style="cursor:pointer; color: <?php ColumnColor("c.nama") ?>;" onClick="changeUrutan('c.nama')">Nama</td>
        <td width="75" style="cursor:pointer; color: <?php ColumnColor("k.kelompok") ?>;" onClick="changeUrutan('k.kelompok')">Kelompok</td>
<?php
        for($i = 0; $i < $max_n_cicilan; $i++)
        {
            $n = $i + 1;
            echo "<td class='header' width='120' align='center'>Bayaran-$n</td>";
        } ?>
        <td width="90" style="cursor:pointer; color:<?php ColumnColor("b.lunas") ?>;" onClick="changeUrutan('b.lunas')">Status</td>
        <td width="125" style="cursor:pointer; color:<?php ColumnColor("b.besar") ?>;" onClick="changeUrutan('b.besar')"><?=$namaPenerimaan ?></td>
        <td width="125">Sub Total Pembayaran</td>
        <td width="125">Sub Total Diskon</td>
        <td width="125">Sub Total Tunggakan</td>
        <td class="header" width="200">Keterangan</td>
    </tr>
<?php
        $cnt = $startFromIndex;
        $result = $db->QueryDb($sql);
        while ($row = mysqli_fetch_array($result))
        {
            $idbesarjtt = $row['id'];
            $besarjtt = $row['besar'];
            $ketjtt = $row['keterangan'];
            $lunasjtt = $row['lunas'];

            if ($lunasjtt == 1)
                $infojtt = "<font color=blue><strong>Lunas</strong></font>";
            elseif ($lunasjtt == 2)
                $infojtt = "<font color=green><strong>Gratis</strong></font>";
            else
                $infojtt = "<font color=red><strong>Belum Lunas</strong></font>"; ?>

            <tr height="40">
                <td align="center" class="numberColumn"><?=++$cnt ?></td>
                <td align="left">
                    <a class="ablue" onclick="showInfoCalonSiswa('<?=$row['nopendaftaran'] ?>')">
                        <?=$row['nopendaftaran'] ?>
                    </a>
                </td>
                <td align="left"><?=$row['nama'] ?></td>
                <td align="left">
                    <?php if ($idKelompok == -1) echo $namaProses . " - "; ?>
                    <?=$row['kelompok'] ?>
                </td>
<?php
            $sql = "SELECT COUNT(p.replid)
                      FROM jbsfina.penerimaanjttcalon p, jbsfina.jurnal j 
                     WHERE p.idjurnal = j.replid 
                       AND j.idtahunbuku = $idTahunBuku 
                       AND p.idbesarjttcalon = $idbesarjtt";
            $nbayar = $db->FetchSingle($sql, 0);
            $nblank = $max_n_cicilan - $nbayar;

            $totalbayar = 0;
            $totaldiskon = 0;

            if ($nbayar > 0)
            {
                $sql = "SELECT DATE_FORMAT(p.tanggal, '%d-%b-%y'), p.jumlah, p.info1 
                          FROM jbsfina.penerimaanjttcalon p, jbsfina.jurnal j 
                         WHERE p.idjurnal = j.replid 
                           AND j.idtahunbuku = $idTahunBuku 
                           AND p.idbesarjttcalon = $idbesarjtt 
                         ORDER BY p.tanggal";
                $result2 = $db->QueryDb($sql);
                while ($row2 = mysqli_fetch_row($result2))
                {
                    $totalbayar = $totalbayar + $row2[1] + $row2[2];
                    $totaldiskon = $totaldiskon + $row2[2];	?>
                    <td>
                        <table border="1" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse ">
                        <tr height="20"><td align="center"><?=FormatRupiah($row2[1]) ?></td></tr>
                        <tr height="20"><td align="center"><?=$row2[0] ?></td></tr>
                        </table>
                    </td>
<?php
                }

                $totalBayarAll += $totalbayar;
                $totalDiskonAll += $totaldiskon;
            }

            for ($i = 0; $i < $nblank; $i++)
            {
                echo "<td>&nbsp;</td>";
            } ?>
                <td align="center"><?=$infojtt ?></td>
                <td align="right"><?=FormatRupiah($besarjtt) ?></td>
                <td align="right"><?=FormatRupiah($totalbayar) ?></td>
                <td align="right"><?=FormatRupiah($totaldiskon) ?></td>
                <td align="right"><?=FormatRupiah($besarjtt - $totalbayar) ?></td>
                <td align="right"><?=$ketjtt ?></td>
            </tr>
            <?php
        }
        ?>
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
<input type="hidden" id="status" value="<?= $status ?>">
<input type="hidden" id="namastatus" value="<?= $namaStatus ?>">
<input type="hidden" id="urut" value="<?= $urut ?>">
<input type="hidden" id="totalpage" value="<?= $totalPage ?>">

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
echo "&nbsp;dari $totalPage, jumlah $ndata data";
echo "</div>";
?>
</body>
</html>