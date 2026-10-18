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
function ShowSelectDepartemenTabunganPegawai($db)
{
    global $departemen;

    try
    {
        echo "<select name='departemen' id='departemen' onChange='onChangeDept()' class='inputbox' style='width:200px'>";
        $dep = getDepartemen($db, getAccess());
        foreach($dep as $value)
        {
            if ($departemen == "") $departemen = $value;
            $sel = $departemen == $value ? "selected" : "";
            echo "<option value='$value' $sel>$value</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "krt72");
    }
}

function ShowDaftarJenisTabunganPegawai($db)
{
    global $idKategori, $departemen;

    try
    {
        echo "<table id='table' class='tab' border='1' style='border-collapse:collapse' width='95%' align='center'>";
        echo "<tr height='30' align='center'>";
        echo "<td class='header' width='5%'>No</td>";
        echo "<td class='header' width='15%'>Nama</td>";
        echo "<td class='header' width='30%'>Kode Rekening</td>";
        echo "<td class='header' width='*'>Keterangan</td>";
        echo "<td class='header' width='120'>Notif<br><span style='font-size: 9px'>JS | TGRAM | SMS</span></td>";
        echo "<td class='header menu' width='100'>&nbsp;</td>";
        echo "</tr>";

        $cnt = 0;
        $sql = "SELECT * 
                  FROM jbsfina.datatabunganp 
                 WHERE departemen = '$departemen' 
                 ORDER BY replid";
        $request = $db->QueryDb($sql);
        if (mysqli_num_rows($request) == 0)
        {
            echo "<tr style='height: 60px'>";
            echo "<td colspan='6' align='center' valign='middle'>";
            echo "<i>belum ada data tabungan</i>";
            echo "</td>";
            echo "</tr>";

            return;
        }

        while ($row = mysqli_fetch_array($request)) {
            $sql = "SELECT nama FROM jbsfina.rekakun WHERE kode = '$row[rekkas]'";
            $result = $db->QueryDb($sql);
            $row2 = mysqli_fetch_row($result);
            $namarekkas = $row2[0];

            $sql = "SELECT nama FROM jbsfina.rekakun WHERE kode = '$row[rekutang]'";
            $result = $db->QueryDb($sql);
            $row2 = mysqli_fetch_row($result);
            $namarekutang = $row2[0];

            $cnt += 1;

            echo "<tr height='25'>";
            echo "<td align='center' class='numberColumn'>$cnt</td>";
            echo "<td>$row[nama]</td>";
            echo "<td>";
            echo "<strong>Kas:</strong> $row[rekkas] $namarekkas<br />";
            echo "<strong>Beban:</strong> $row[rekutang] $namarekutang <br />";
            echo "</td>";
            echo "<td>$row[keterangan]</td>";
            echo "<td align='center'>";
            if ($row['info2'] == 1)
                echo "<img src='../images/ico/checka.png' title='kirim'>";
            else
                echo "&nbsp;";
            echo "</td>";
            echo "<td class='menu' align='center'>";

            $img = "aktif.png";
            if ($row['aktif'] == 0)
                $img = "nonaktif.png";

            echo "<input type='hidden' id='dataaktif-$row[replid]' value='$row[aktif]'>";
            echo "<a href='#' onClick='set_aktif($row[replid])'><img id='imgaktif-$row[replid]' src='../images/ico/$img' border='0'/></a>&nbsp;";
            echo "<a href='#' onClick='ubah($row[replid])'><img src='../images/ico/ubah.png' border='0'/></a>&nbsp;";
            echo "<a href='#' onClick='hapus($row[replid])'><img src='../images/ico/hapus.png' border='0'/></a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kbyhk");
    }
}

function ChangeAktif()
{
    $db = new Db();
    try
    {
        $db->Open();

        $id = $_REQUEST["id"];
        $newAktif = $_REQUEST["newaktif"];

        $sql = "UPDATE jbsfina.datatabunganp
                   SET aktif = $newAktif
                 WHERE replid = $id";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kntxr") ]);
    }
    finally
    {
        $db->Close();
    }
}

function HapusJenisTabungan()
{
    $db = new Db();
    try
    {
        $db->Open();

        $id = $_REQUEST["id"];

        $sql = "DELETE FROM jbsfina.datatabunganp
                 WHERE replid = $id";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $lastError = $db->LastError();
        $errNo = $lastError[0];
        if ($errNo == 1451)
        {
            return json_encode([-99, "Tidak dapat menghapus data ini karena sudah digunakan" ]);
        }
        else
        {
            return json_encode([-99, Msg::InfoError($ex->getMessage(), "kjf9q") ]);
        }
    }
    finally
    {
        $db->Close();
    }
}
?>