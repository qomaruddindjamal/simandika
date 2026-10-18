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
require_once('../include/rupiah.php');
require_once('../include/config.php');
require_once('../library/msg.php');
require_once('../include/db.onfunc.php');
require_once('rekakun.dialog.func.php');
require_once('rekakun.func.php');

$kategori = strtoupper($_REQUEST["kategori"]);
$subKategori = $_REQUEST["subkategori"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Jenis Tabungan</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/qsbuilder.js"></script>
    <script language="javascript" src="../script/tables.js"></script>
    <script language="JavaScript">

        $(document).ready(function ()
        {
            showRekAkun();
        });

        function change_kategori()
        {
            showRekAkun();
        }

        function showRekAkun()
        {
            var qsb = new QsBuilder();
            qsb.add("container", "frame");
            qsb.addInput("kategori", "kategori");
            qsb.addInput("subkategori", "subkategori");

            parent.content.location.href = "rekakun.dialog.php?" + qsb.createQs();
        }

    </script>

</head>

<body style="margin: 2px; background-color: #c6dfea" >
<input type="hidden" id="subkategori" value="<?=$subKategori?>">
<table border="0" width="100%" cellpadding="5">
<tr>
    <td align="left" valign="top">
        Kategori:
<?php
        if ($kategori == "ALL")
        {
            $db = new Db();
            try
            {
                $db->Open();

                $sql = "SELECT * 
                          FROM jbsfina.katerekakun 
                         ORDER BY urutan";
                $result = $db->QueryDb($sql);

                echo "<select class='inputbox' name='kategori' id='kategori' onChange='change_kategori()' style='font-size: 18px; width:200px'>";
                while ($row = mysqli_fetch_array($result))
                {
                    echo "<option value='$row[kategori]'>$row[kategori]</option>";
                }
                echo "</select>";
            }
            catch(Exception $ex)
            {
                echo Msg::InfoError($ex->getMessage(), "k0q86");
            }
            finally
            {
                $db->Close();
            }
        }
        else
        {
            echo "<select class='inputbox' name='kategori' id='kategori' style='font-size: 18px; width:200px'>";
            echo "<option value='$kategori'>$kategori</option>";
            echo "</select>";
        }

?>
    </td>
</tr>
</table>

</body>

</html>
