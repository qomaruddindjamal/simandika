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

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idKategori = RequestData("idkategori", "JTT");
$namaKategori = RequestData("namakategori", "");
$tanggal1 = RequestData("tanggal1", "");
$tanggal2 = RequestData("tanggal2", "");
$idLaporan = RequestData("idlaporan", 1);
$namaLaporan = RequestData("namalaporan", "");
$idPetugas = RequestData("idpetugas", "");
$namaPetugas = RequestData("namapetugas", "");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekapitulasi Pembayaran Harian</title>
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
    <script language="javascript" src="rekap.pembayaran.harian.js?r=<?=filemtime('rekap.pembayaran.harian.js')?>"></script>
</head>
<body style="margin: 10px;">

<table border="0" cellpadding="2" cellspacing="0" align="center">
<tr>
    <td align="left" valign="top">
        <a href="JavaScript:refresh()"><img src="../../images/ico/refresh.png" border="0">&nbsp;refresh</a>&nbsp;&nbsp;
        <a href="JavaScript:cetak()"><img src="../../images/ico/print.png" border="0">&nbsp;cetak</a>&nbsp;&nbsp;
        <a href="JavaScript:excel()"><img src="../../images/ico/excel.png" border="0">&nbsp;excel</a>&nbsp;
    </td>
</tr>
</table>

<?php
if ($departemen == "ALL")
{
    $sql = "SELECT departemen 
              FROM jbsakad.departemen 
             ORDER BY urutan";
    $dres = $db->QueryDb($sql);
    $k = 0;
    while ($drow = mysqli_fetch_row($dres))
        $darray[$k++] = $drow[0];
}
else
{
    $darray = array( $departemen );
}

if ($idPetugas == "ALL")
    $sql_idpetugas = "";
elseif ($idPetugas == "landlord")
    $sql_idpetugas = " AND j.idpetugas IS NULL ";
else
    $sql_idpetugas = " AND j.idpetugas = '$idPetugas' ";

$total = 0;
echo "<div id='dvLaporan'>";
$ntable = count($darray);
echo "<input type='hidden' id='ntable' value='$ntable'>";
for($k = 0; $k < count($darray); $k++)
{
    $dept = $darray[$k];
    $cnt = 0;

    $sql = "SELECT replid 
              FROM jbsfina.tahunbuku 
             WHERE departemen = '$dept' 
               AND aktif = 1";
    $res = $db->QueryDb($sql);
    $ntb = mysqli_num_rows($res);
    if ($ntb == 0)
        continue;

    $row = mysqli_fetch_row($res);
    $idtahunbuku = $row[0];

    // Ambil tanggal-tanggal transaksi yang terjadi pada rentang terpilih
    if ($idKategori == "JTT")
    {
        $sql = "SELECT DISTINCT p.tanggal 
				  FROM jbsfina.penerimaanjtt p, jbsfina.besarjtt b, jbsfina.datapenerimaan dp, jbsfina.jurnal j 
				 WHERE p.idbesarjtt = b.replid
				   AND b.idpenerimaan = dp.replid
				   AND j.replid = p.idjurnal
				   AND j.idtahunbuku = '$idtahunbuku'
			       AND dp.departemen = '$dept'
				   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'
				   $sql_idpetugas
				 ORDER BY p.tanggal ASC";
    }
    elseif ($idKategori == "SKR")
    {
        $sql = "SELECT DISTINCT p.tanggal 
		          FROM jbsfina.penerimaaniuran p, jbsfina.datapenerimaan dp, jbsfina.jurnal j
				 WHERE p.idjurnal = j.replid
				   AND j.idtahunbuku = '$idtahunbuku'
				   AND p.idpenerimaan = dp.replid 
				   AND dp.departemen='$dept'
				   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'
				   $sql_idpetugas
				ORDER BY p.tanggal ASC";
    }
    elseif ($idKategori == "CSWJB")
    {
        $sql = "SELECT DISTINCT p.tanggal 
                  FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsfina.datapenerimaan dp, jbsfina.jurnal j 
                 WHERE p.idbesarjttcalon = b.replid
                   AND b.idpenerimaan = dp.replid
                   AND j.replid = p.idjurnal
                   AND j.idtahunbuku = '$idtahunbuku'
                   AND dp.departemen = '$dept'
                   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'
                 $sql_idpetugas
		 		ORDER BY p.tanggal ASC";
    }
    elseif ($idKategori == "CSSKR")
    {
        $sql = "SELECT DISTINCT p.tanggal 
		          FROM jbsfina.penerimaaniurancalon p, jbsfina.datapenerimaan dp, jbsfina.jurnal j 
				 WHERE p.idjurnal = j.replid
				   AND j.idtahunbuku = '$idtahunbuku'
				   AND p.idpenerimaan = dp.replid 
				   AND dp.departemen='$dept'
				   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'
				   $sql_idpetugas
				 ORDER BY p.tanggal ASC";
    }
    elseif ($idKategori == "LNN")
    {
        $sql = "SELECT DISTINCT p.tanggal
		          FROM jbsfina.penerimaanlain p, jbsfina.datapenerimaan dp, jbsfina.jurnal j  
				 WHERE p.idjurnal = j.replid
				   AND j.idtahunbuku = '$idtahunbuku'
				   AND p.idpenerimaan = dp.replid 
				   AND dp.departemen='$dept'
				   AND p.tanggal BETWEEN '$tanggal1' AND '$tanggal2'
				   $sql_idpetugas
				 ORDER BY p.tanggal ASC";
    }

    // tarray -> tanggal array
    // n -> conter tarray
    $tarray = array();
    //Peek::Show($sql);
    $tres = $db->QueryDb($sql);
    $n = 0;
    while ($trow = mysqli_fetch_row($tres))
    {
        $tarray[$n] = $trow[0];
        $n++;
    }

    if ($n == 0)
        continue;

    // ambil nama-nama penerimaan pada departemen terpilih
    // parray -> penerimaan array
    // m -> counter parray
    $parray = array();
    $sql = "SELECT replid, nama 
              FROM jbsfina.datapenerimaan 
             WHERE departemen = '$dept' 
               AND aktif = 1 
               AND idkategori = '$idKategori'";
    //Peek::Show($sql);
    $pres = $db->QueryDb($sql);
    $m = 0;
    while ($prow = mysqli_fetch_row($pres))
    {
        $parray[$m][0] = $prow[0];
        $parray[$m][1] = $prow[1];
        $m++;
    }

    // rarray -> result array
    $rarray = array();
    for($i = 0; $i < $m; $i++)
    {
        $idp = $parray[$i][0];
        $pen = $parray[$i][1];

        for($j = 0; $j < $n; $j++)
        {
            $tanggal = $tarray[$j];

            if ($idKategori == "JTT")
            {
                $sql = "SELECT SUM(p.jumlah), SUM(p.info1)
							  FROM jbsfina.penerimaanjtt p, jbsfina.besarjtt b, jbsfina.datapenerimaan dp, jbsfina.jurnal j  
							 WHERE p.idbesarjtt = b.replid
							   AND b.idpenerimaan = dp.replid
							   AND j.replid = p.idjurnal
							   AND j.idtahunbuku = '$idtahunbuku'
							   AND dp.replid = '$idp'
							   AND dp.departemen='$dept'
							   AND p.tanggal = '$tanggal'
							   $sql_idpetugas";
            }
            elseif ($idKategori == "SKR")
            {
                $sql = "SELECT SUM(p.jumlah), 0
						   	  FROM jbsfina.penerimaaniuran p, jbsfina.datapenerimaan dp, jbsfina.jurnal j 
							 WHERE p.idpenerimaan = dp.replid
							   AND p.idjurnal = j.replid
							   AND j.idtahunbuku = '$idtahunbuku'
							   AND dp.replid = '$idp'
							   AND dp.departemen='$dept'
							   AND p.tanggal = '$tanggal'
							   $sql_idpetugas";
            }
            elseif ($idKategori == "CSWJB")
            {
                $sql = "SELECT SUM(p.jumlah), SUM(p.info1)
						  	  FROM jbsfina.penerimaanjttcalon p, jbsfina.besarjttcalon b, jbsfina.datapenerimaan dp, jbsfina.jurnal j  
							 WHERE p.idbesarjttcalon = b.replid
							   AND b.idpenerimaan = dp.replid
							   AND j.replid = p.idjurnal
							   AND j.idtahunbuku = '$idtahunbuku'
							   AND dp.replid = '$idp'
							   AND dp.departemen='$dept'
							   AND p.tanggal = '$tanggal'
							   $sql_idpetugas";
            }
            elseif ($idKategori == "CSSKR")
            {
                $sql = "SELECT SUM(p.jumlah), 0
							  FROM jbsfina.penerimaaniurancalon p, jbsfina.datapenerimaan dp, jbsfina.jurnal j  
							 WHERE p.idpenerimaan = dp.replid
							   AND p.idjurnal = j.replid
							   AND j.idtahunbuku = '$idtahunbuku'
							   AND dp.replid = '$idp' 
							   AND dp.departemen='$dept'
							   AND p.tanggal = '$tanggal'
							   $sql_idpetugas";
            }
            elseif ($idKategori == "LNN")
            {
                $sql = "SELECT SUM(p.jumlah), 0
						      FROM jbsfina.penerimaanlain p, jbsfina.datapenerimaan dp, jbsfina.jurnal j   
							 WHERE p.idpenerimaan = dp.replid
							   AND p.idjurnal = j.replid
							   AND j.idtahunbuku = '$idtahunbuku'
							   AND dp.replid = '$idp' 
							   AND dp.departemen='$dept'
							   AND p.tanggal = '$tanggal'
							   $sql_idpetugas";
            }
            //Peek::Show($sql);
            $jres = $db->QueryDb($sql);
            $jrow = mysqli_fetch_row($jres);
            $jumlah = 0;
            if (!is_null($jrow[0]))
                $jumlah = $jrow[0];

            $rarray[$j][$i] = $jumlah;
        } // for j
    }  // for i

    $colspan = 2 + $m + 1;
    echo "<table cellpadding='5' id='table-$k' border='1' class='tab' cellspacing='0' align='center'>";
    echo "<tr>";
    echo "<td colspan='$colspan' align='right' valign='middle' bgcolor='#660099'>";
    echo "<font color='#FFFFFF'><strong><em>$dept</em></strong></font>";
    echo "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td bgcolor='#FFECFF' width='25' align='center' valign='middle'><strong>No</strong></td>";
    echo "<td bgcolor='#FFECFF' width='80' align='center' valign='middle'><strong>Tanggal</strong></td>";
    for($i = 0; $i < $m; $i++)
    {
        $pen = $parray[$i][1];
        echo "<td bgcolor='#FFECFF' width='140' align='center' valign='middle'><strong>$pen</strong></td>";
    }
    echo "<td bgcolor='#FFECFF' width='140' align='center' valign='middle'><strong>Sub Total</strong></td>";
    echo "</tr>";

    $cnt = 0;
    for($i = 0; $i < $n; $i++)
    {
        $cnt++;
        $tanggal = RegularDateFormat($tarray[$i]);

        echo  "<tr>";
        echo  "<td align='center' class='numberColumn' valign='top'>$cnt</td>";
        echo  "<td align='center' valign='top'>$tanggal</td>";

        $subtotal = 0;
        for($j = 0; $j < $m; $j++)
        {
            $subtotal = $subtotal + $rarray[$i][$j];
            $jumlah = FormatRupiah($rarray[$i][$j]);

            if ($rarray[$i][$j] > 0)
            {
                $idpen = $parray[$j][0];
                $tgl = $tarray[$i];

                echo  "<td align='right' valign='top'>
						   <a href=\"JavaScript:showDetail('$departemen', $idtahunbuku, '$idKategori', $idpen, '$tgl', '$idPetugas')\" style='color: blue; font-weight: normal;'>$jumlah</a>
                       </td>";
            }
            else
            {
                echo  "<td align='right' valign='top'>$jumlah</td>";
            }
        }
        echo  "<td align='right' valign='top'>" . FormatRupiah($subtotal) . "</td>";
        echo  "</tr>";
    }

    echo  "<tr height='40'>";
    echo  "<td colspan='2' align='right' valign='middle' bgcolor='#333333'><font color='#ffffff'><strong>T O T A L</strong></font></td>";
    $total = 0;
    for($i = 0; $i < $m; $i++)
    {
        $subtotal = 0;
        for($j = 0; $j < $n; $j++)
        {
            $subtotal = $subtotal + $rarray[$j][$i];
        }
        $total = $total + $subtotal;
        echo  "<td align='right' valign='middle' bgcolor='#333333'><font color='#ffffff'><strong>" . FormatRupiah($subtotal) . "</strong></font></td>";
    }
    echo  "<td align='right' valign='middle' bgcolor='#333333'><font color='#ffffff'><strong>" . FormatRupiah($total) . "</strong></font></td>";
    echo  "</tr>";
    echo  "</table>";

    echo  "<br><br>";

} // for k dept array
echo "</div>";
?>


<input type="hidden" id="departemen" value="<?= $departemen ?>">
<input type="hidden" id="idkategori" value="<?= $idKategori ?>">
<input type="hidden" id="namakategori" value="<?= $namaKategori  ?>">
<input type="hidden" id="idlaporan" value="<?= $idLaporan  ?>">
<input type="hidden" id="namalaporan" value="<?= $namaLaporan ?>">
<input type="hidden" id="idpetugas" value="<?= $idPetugas  ?>">
<input type="hidden" id="namapetugas" value="<?= $namaPetugas  ?>">
<input type="hidden" id="tanggal1" value="<?= $tanggal1 ?>">
<input type="hidden" id="tanggal2" value="<?= $tanggal2 ?>">

</body>
</html>