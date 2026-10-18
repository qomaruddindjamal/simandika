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
require_once('../util/peek.php');
require_once('../include/errorhandler.php');
require_once('common.func.php');

$db = new Db;
$db->TryOpenExit(true);

$departemen = RequestData("departemen", "");
$idTahunBuku = RequestData("idtahunbuku", 0);
$namaTahunBuku = RequestData("namatahunbuku", "");
$tanggal1 = RequestData("tanggal1", date('Y-m-d'));
$tanggal2 = RequestData("tanggal2", date('Y-m-d'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Audit Perubahan Data</title>
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
    <script language="javascript" src="audit.jenis.js?r=<?=filemtime('audit.jenis.js')?>"></script>
</head>
<body style="margin: 0; margin-top: 10px;">
<input type="hidden" id="departemen" value="<?=$departemen?>">
<input type="hidden" id="idtahunbuku" value="<?=$idTahunBuku?>">
<input type="hidden" id="namatahunbuku" value="<?=$namaTahunBuku?>">
<input type="hidden" id="tanggal1" value="<?=$tanggal1?>">
<input type="hidden" id="tanggal2" value="<?=$tanggal2?>">

<?php
$sql = "SELECT * 
          FROM jbsfina.auditinfo 
         WHERE info1 IS NULL";
$res = $db->QueryDb($sql);
while ($row = mysqli_fetch_array($res))
{
    $id = $row['replid'];
    $table = $row['sumber'];
    $idsumber = $row['idsumber'];

    if ($table == "besarjtt")
        $sql = "SELECT info1 FROM jbsfina.besarjtt WHERE replid = '$idsumber'";
    elseif ($table == "besarjttcalon")
        $sql = "SELECT info1 FROM jbsfina.besarjttcalon WHERE replid = '$idsumber'";
    elseif ($table == "penerimaanjtt")
        $sql = "SELECT idjurnal FROM jbsfina.penerimaanjtt WHERE replid = '$idsumber'";
    elseif ($table == "penerimaanjttcalon")
        $sql = "SELECT idjurnal FROM jbsfina.penerimaanjttcalon WHERE replid = '$idsumber'";
    elseif ($table == "penerimaaniuran")
        $sql = "SELECT idjurnal FROM jbsfina.penerimaaniuran WHERE replid = '$idsumber'";
    elseif ($table == "penerimaaniurancalon")
        $sql = "SELECT idjurnal FROM jbsfina.penerimaaniurancalon WHERE replid = '$idsumber'";
    elseif ($table == "penerimaanlain")
        $sql = "SELECT idjurnal FROM jbsfina.penerimaanlain WHERE replid = '$idsumber'";
    elseif ($table == "pengeluaran")
        $sql = "SELECT idjurnal FROM jbsfina.pengeluaran WHERE replid = '$idsumber'";
    elseif ($table == "tabungan")
        $sql = "SELECT idjurnal FROM jbsfina.tabungan WHERE replid = '$idsumber'";
    elseif ($table == "tabunganp")
        $sql = "SELECT idjurnal FROM jbsfina.tabunganp WHERE replid = '$idsumber'";
    elseif ($table == "jurnalumum")
        $sql = "SELECT $idsumber";

    $res2 = $db->QueryDb($sql);
    if (mysqli_num_rows($res2) > 0)
    {
        $row2 = mysqli_fetch_row($res2);
        $idjurnal = $row2[0];

        $sql = "UPDATE jbsfina.auditinfo 
                   SET info1='$idjurnal' 
                 WHERE replid='$id'";
        $db->QueryDb($sql);
    }
    else
    {
        $sql = "UPDATE jbsfina.auditinfo 
                   SET info1='na' 
                 WHERE replid='$id'";
        $db->QueryDb($sql);
    }

} // while

$sql = "SELECT a.sumber, count(a.replid) 
          FROM jbsfina.auditinfo a, jbsfina.jurnal j 
         WHERE a.info1 = j.replid 
           AND j.idtahunbuku = '$idTahunBuku' 
           AND a.departemen = '$departemen' 
           AND a.tanggal >= '$tanggal1 00:00:00' 
           AND a.tanggal <= '$tanggal2 23:59:59' 
         GROUP BY a.sumber 
         ORDER BY a.sumber";
$res = $db->QueryDb($sql);
if (mysqli_num_rows($res) == 0)
{
    echo "<span style='color: maroon'>belum ada data perubahan data keuangan</span>";
    exit();
}

echo "<table class='tab' id='table' border='1' cellpadding='2' style='border-collapse:collapse' cellspacing='2' width='95%' align='center'>";
echo "<tr height='30' align='center'>";
echo "<td class='header-sm' width='85%'>Perubahan</td>";
echo "<td class='header-sm' width='15%'>Jumlah</td>";
echo "</tr>";
$cnt = 0;
while($row = mysqli_fetch_row($res))
{
    switch($row[0])
    {
        case 'jurnalumum':
            $jurnal = "Jurnal Umum"; break;
        case 'penerimaanjtt':
            $jurnal = "Penerimaan Iuran Wajib Siswa"; break;
        case 'penerimaaniuran':
            $jurnal = "Penerimaan Iuran Sukarela Siswa"; break;
        case 'penerimaanlain':
            $jurnal = "Penerimaan Lain-Lain"; break;
        case 'pengeluaran':
            $jurnal = "Pengeluaran"; break;
        case 'penerimaanjttcalon':
            $jurnal = "Penerimaan Iuran Wajib Calon Siswa"; break;
        case 'penerimaaniurancalon':
            $jurnal = "Penerimaan Iuran Sukarela Calon Siswa"; break;
        case 'besarjtt':
            $jurnal = "Pendataan Besar Iuran Wajib Siswa"; break;
        case 'besarjttcalon':
            $jurnal = "Pendataan Besar Iuran Wajib Calon Siswa"; break;
        case 'tabungan':
            $jurnal = "Tabungan Siswa"; break;
        case 'tabunganp':
            $jurnal = "Tabungan Pegawai"; break;
    }

    $cnt += 1;
    echo "<tr height='25' onclick='show_detail(\"$row[0]\")' style='cursor: pointer'>";
    echo "<td align='left'>$jurnal</td>";
    echo "<td align='center'><strong>$row[1] </strong></td>";
    echo "</tr>";
}
echo "</table>";
?>

</body>
</html>