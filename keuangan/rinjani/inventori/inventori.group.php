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
require_once('inventori.group.func.php');

$db = new Db;
$db->TryOpenExit(true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Cari Jurnal</title>
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
    <script language="javascript" src="inventori.group.js?r=<?=filemtime('inventori.group.js')?>"></script>
</head>
<body style="margin: 0 10px">

<fieldset style="border:#ccc 1px solid; border-radius: 10px; background-color:#ffffff" >
    <legend style="background-color: #ccc; color:#333; font-size:10px; padding:5px; border-radius: 3px;">Kategori Barang</legend>
    <div align="right">
        <img class="help-icon-1" src="../images/help32.png" title="bantuan" onclick="showHelp()">
        <a href="javascript:tambahKategori()"><img src="../images/ico/tambah.png" border="0" title="tambah kategori">&nbsp;tambah kategori</a>
    </div>
<?php
    $sql = "SELECT replid, namagroup 
              FROM jbsfina.groupbarang 
             ORDER BY namagroup";
    $res = $db->QueryDb($sql);
    if (mysqli_num_rows($res) > 0)
    {
        echo "<ul class='mktree' id='tree1'>";
        while ($row = mysqli_fetch_row($res))
        {
            $class = "liOpen";
            if (getNSubDir($db, $row[0]) == 0)
                $class = "liClose";

            $idGroup = $row[0];
            $namaGroup = $row[1];

            echo "<li class='liOpen' style='cursor: default' onmouseover='hoverKategori(\"$row[0]\")' onmouseleave='leaveKateegori(\"$row[0]\")'>&nbsp;";
            echo "<img src='../images/ico/folder.gif' border='0'>&nbsp;<strong>$row[1]</strong>&nbsp;";
            echo "<span id='spKategori-$row[0]' style='visibility: hidden;'>";
            echo "<a href='javascript:tambahKelompok(\"$idGroup\",\"$namaGroup\")'><img src='../images/ico/tambah.png' border='0' title='tambah kelompok'></a>&nbsp;&nbsp;";
            echo "<a href='javascript:editKategori(\"$row[0]\")'><img src='../images/ico/ubah.png' border='0' title='ubah kategori'></a>";
            echo "<a href='javascript:hapusKategori(\"$row[0]\")'><img src='../images/ico/hapus.png' border='0' title='hapus kategori'></a>";
            echo "</span>";

            $sql2 = "SELECT replid, kelompok 
                       FROM jbsfina.kelompokbarang 
                      WHERE idgroup='$row[0]'";
            $res2 = $db->QueryDb($sql2);
            if (mysqli_num_rows($res2) > 0)
            {
                echo "<ul>";
                while ($row2 = mysqli_fetch_row($res2))
                {
                    echo "<li class='liOpen' id='liOpen$row2[0]' onmouseover='hoverKelompok(\"$row2[0]\")' onmouseleave='leaveKelompok(\"$row2[0]\")' onClick='selectKelompok(\"$idGroup\",\"$namaGroup\",\"$row2[0]\",\"$row2[1]\")'>";
                    echo "<img src='../images/ico/page.gif' border='0'>&nbsp;$row2[1]</span>&nbsp;";
                    echo "<span id='spKelompok-$row2[0]' style='visibility: hidden;'>";
                    echo "<img src='../images/ico/ubah.png' border='0' onClick='editKelompok(\"$idGroup\",\"$namaGroup\",\"$row2[0]\")' title='ubah kelompok' style='cursor:pointer'>";
                    echo "<img src='../images/ico/hapus.png' border='0' onClick='hapusKelompok(\"$row2[0]\")' title='hapus kelompok' style='cursor:pointer'>";
                    echo "</span>";
                    echo "</li>";
                }
                echo "</ul>";
            }
            echo "</li>";
        }
        echo "</ul>";
    };
    ?>
</fieldset>

</body>
</html>