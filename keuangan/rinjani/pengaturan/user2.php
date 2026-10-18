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
require_once('../util/peek.php');
require_once('user2.func.php');

if (getLevel() == 2)
{
    echo "<script>";
    echo "alert('Maaf, anda tidak berhak mengakses halaman ini!');";
    echo "window.history.back();";
    echo "</script>";
    exit();
}

$db = new Db();
$db->TryOpenExit();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Daftar Pengguna</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?=filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="javascript" src="../script/tools.js"></script>
    <script language="javascript" src="../script/toast.js"></script>
    <script language="javascript" src="../script/dialogbox.js" ></script>
    <script language="javascript" src="../script/qsbuilder.js?<?=filemtime('../script/qsbuilder.js')?>" ></script>
    <script language="javascript" src="user2.js?<?=filemtime('user2.js')?>"></script>
</head>
<body>

<table border="0" width="100%">
<tr>
    <td align="center" valign="top">

        <table border="0" width="95%" align="center">
        <tr>
            <td align="right">
                <span class="pageTitle">Daftar Pengguna</span><br>
                <a class="pageLink" href="pengaturan.php">Referensi</a>&nbsp&gt;&nbsp
                <span class="pageLinkCurrent">Daftar Pengguna</span>
            </td>
        </tr>
        </table>
        <br>

        <table border="0" cellpadding="0" cellspacing="0" width="95%" align="center">
        <tr>
            <td align="right" width="25%">

            </td>
            <td align="right" width="*">
                <a href="JavaScript:refresh()">
                    <img src="../images/ico/refresh.png" border="0"/>&nbsp;refresh
                </a>&nbsp;&nbsp;
                <a href="JavaScript:cetak()">
                    <img src="../images/ico/print.png" border="0"/>&nbsp;cetak
                </a>&nbsp;&nbsp;
                <a href="JavaScript:tambah()">
                    <img src="../images/ico/tambah.png" border="0"/>&nbsp;tambah
                </a>&nbsp;&nbsp;
            </td>
        </tr>
        </table>

        <div id="dvTableContent">
<?php
        $sql = "SELECT h.login, h.replid, h.tingkat, h.departemen, 
                       h.keterangan, p.nama, p.aktif, DATE_FORMAT(h.lastlogin,'%Y-%m-%d') AS tanggal, 
                       TIME(h.lastlogin) as jam 
                  FROM jbsuser.hakakses h, jbssdm.pegawai p, jbsuser.login l 
                 WHERE h.modul = 'KEUANGAN' 
                   AND h.login = l.login 
                   AND l.login = p.nip 
                 ORDER BY nama";
        $res = $db->QueryDb($sql);

        echo "<table id='table' class='tab' border='1' style='border-collapse:collapse' width='95%' bordercolor='#000000' align='center'>";
        echo "<tr height='30' class='header' align='center'>";
        echo "<td width='4%' class='header'>No</td>";
        echo "<td width='10%' class='header' >Login</td>";
        echo "<td width='20%' class='header'>Nama</td>";
        echo "<td width='12%' class='header'>Departemen</td>";
        echo "<td width='10%' class='header'>Tingkat</td>";
        echo "<td width='10%' class='header'>Status</td>";
        echo "<td width='*'>Keterangan</td>";
        echo "<td width='15%' class='header'>Login Terakhir</td>";
        if (getLevel() == 0)
            echo "<td width='8%' class='header hide-in-report'>&nbsp;</td>";
        echo "</tr>";

        $cnt = 0;
        while ($row = mysqli_fetch_array($res))
        {
            $cnt += 1;

            echo "<tr height='25'>";
            echo "<td align='center' class='numberColumn'>$cnt</td>";
            echo "<td align='center'>$row[login]</td>";
            echo "<td>$row[nama] </td>";
            echo "<td align='center'>";
            if ($row['tingkat']==1)
                echo  "Semua Departemen";
            else
                echo  $row['departemen'];
            echo "</td>";
            echo "<td align='center'>";
            switch ($row['tingkat'])
            {
                case 0:
                    echo  "Landlord";
                    break;
                case 1:
                    echo  "Manajer Keuangan";
                    break;
                case 2:
                    echo  "Staf Keuangan";
                    break;
            }
            echo "</td>";
            echo "<td align='center'>";
            if ($row['aktif'] == 1)
                echo  'Aktif';
            else
                echo  'Tidak Aktif';
            echo "</td>";
            echo "<td>$row[keterangan] </td>";
            echo "<td align='center'>" . LongDateFormat($row['tanggal']) . " $row[jam]</td>";
            echo "<td align='center' class='hide-in-report'>";
            if (getLevel() == 0)
            {
                echo "<a href='JavaScript:edit(\"$row[replid]\")'><img src='../images/ico/ubah.png' border='0'></a>&nbsp;";
                echo "<a href='JavaScript:hapus(\"$row[replid]\")'><img src='../images/ico/hapus.png' border='0'></a>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";

?>


        </div>

    </td>
</tr>
</table>

<div id="divDialog"></div>
<div id="toast-container"></div>

</body>
</html>