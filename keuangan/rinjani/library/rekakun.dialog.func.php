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
function KodeRekInfo()
{
    $db = new Db();
    try
    {
        $db->Open();

        $kodeRek = $_REQUEST["koderek"];
        $namaRek = "";
        $kateRek = "";
        $ketRek = "";

        $sql = "SELECT nama, kategori, keterangan
                  FROM jbsfina.rekakun
                 WHERE kode  = '$kodeRek'";
        $res = $db->QueryDbEx($sql);
        if ($row = mysqli_fetch_row($res))
        {
            $namaRek = $row[0];
            $kateRek = $row[1];
            $ketRek = $row[2];
        }

        $nUsage = CountRekAkunUsage($db, $kodeRek);
        $listUsage = ListRekAkunUsage($db, $kodeRek);

        $output  = "<h2>$kodeRek - $namaRek</h2><br>";
        $output .= "<p style='font-size: 12px'>";
        $output .= "<span style='font-weight: bold'>Kategori</span><br>";
        $output .= "<span style='margin-left: 10px'>$kateRek</span><br><br>";
        $output .= "<span style='font-weight: bold'>Keterangan</span><br>";
        $output .= "<span style='margin-left: 10px'>$ketRek</span><br><br>";
        $output .= "<span style='font-weight: bold'>Jumlah Penggunaan Rekening</span><br>";
        $output .= "<span style='margin-left: 10px'>$nUsage</span><br><br>";
        $output .= "<span style='font-weight: bold'>Penggunaan Rekening</span><br>";
        $output .= "<span style='margin-left: 10px'>$listUsage</span><br><br>";
        $output .= "</p>";

        return $output;
    }
    catch (Exception $ex)
    {
        return Msg::InfoError($ex->getMessage(), "kagjs");
    }
    finally
    {
        $db->Close();
    }
}

function SimpanKodeRek()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idKodeRek = $_REQUEST["idkoderek"];
        $kategori = $_REQUEST["kategori"];
        $kode = SafeValueHtml($_REQUEST["kode"]);
        $nama = SafeValueHtml($_REQUEST["nama"]);
        $keterangan = SafeValueHtml($_REQUEST["keterangan"]);

        if ($idKodeRek == 0)
        {
            $sql = "SELECT COUNT(replid)
                      FROM jbsfina.rekakun
                     WHERE kode = '$kode'";
            $res = $db->QueryDbEx($sql);
            $row = mysqli_fetch_row($res);
            if ($row[0] > 0)
            {
                return createJsonReturn(-1, "Kode rekening $kode sudah digunakan", "");
            }

            $sql = "INSERT INTO jbsfina.rekakun
                       SET kategori = '$kategori', kode = '$kode', nama = '$nama', keterangan = '$keterangan'";
            $db->QueryDbEx($sql);

            return json_encode([1, "Berhasil", ""]);
        }
        else
        {
            $sql = "SELECT COUNT(replid)
                      FROM jbsfina.rekakun
                     WHERE kode = '$kode'
                       AND replid <> $idKodeRek";
            $res = $db->QueryDbEx($sql);
            $row = mysqli_fetch_row($res);
            if ($row[0] > 0)
            {
                return createJsonReturn(-1, "Kode rekening $kode sudah digunakan", "");
            }

            $sql = "SELECT replid
                      FROM jbsfina.jurnaldetail
                     WHERE koderek = '$kode'
                     LIMIT 1";
            $res = $db->QueryDbEx($sql);
            $nUsed = mysqli_num_rows($res);

            if ($nUsed == 0)
            {
                $sql = "UPDATE jbsfina.rekakun
                           SET kode = '$kode', nama = '$nama', keterangan = '$keterangan'
                         WHERE replid = $idKodeRek";
                $db->QueryDbEx($sql);
            }
            else
            {
                $sql = "UPDATE jbsfina.rekakun
                           SET keterangan = '$keterangan'
                         WHERE replid = $idKodeRek";
                $db->QueryDbEx($sql);
            }

            return json_encode([1, "Berhasil", ""]);
        }
    }
    catch (Exception $ex)
    {
        return json_encode([-1, "ERROR: " . $ex->getMessage(), ""]);
    }
    finally
    {
        $db->Close();
    }
}

function HapusKodeRek()
{
    $db = new Db();
    try
    {
        $db->Open();

        $kode = $_REQUEST["kode"];

        $sql = "DELETE FROM jbsfina.rekakun
                 WHERE kode = '$kode'";
        $db->QueryDbEx($sql);

        return json_encode([1, "Berhasil", ""]);
    }
    catch (Exception $ex)
    {
        return json_encode([-1, "ERROR: " . $ex->getMessage(), ""]);
    }
    finally
    {
        $db->Close();
    }
}

function DaftarRekAkun($kategori)
{
    $db = new Db();
    try
    {
        $db->Open();

        echo "<table id='table' border='0' cellspacing='0' cellpadding='2' width='100%'>";
        echo "<tr style='height: 30px'>";
        echo "<td class='header' align='center' width='8%'>No</td>";
        echo "<td class='header' align='center' width='8%'>Pilih</td>";
        echo "<td class='header' align='center' width='15%'>Kode</td>";
        echo "<td class='header' width='40%'>Nama</td>";
        echo "<td class='header' align='center' width='10%'>Usage</td>";
        echo "<td class='header' width='*'>&nbsp;</td>";
        echo "</tr>";

        $no = 0;
        $sql = "SELECT replid, kode, nama, keterangan
                  FROM jbsfina.rekakun
                 WHERE kategori = '$kategori'
                 ORDER BY kode";
        $res = $db->QueryDb($sql);
        while($row = mysqli_fetch_row($res))
        {
            $kodeRek = $row[1];
            $nUsage = CountRekAkunUsage($db, $kodeRek);

            $no += 1;
            echo "<tr style='height: 25px;'>";
            echo "<td align='center' style='background-color: #eee'>$no</td>";
            echo "<td align='center' style='background-color: #d5f8d6'>";
            //echo "<input type='button' class='but' value='pilih' onclick='pilihKodeRek(\"$row[1]\", \"$row[2]\")'>";
            echo "<img src='../images/ico/select16.png' title='pilih' onclick='pilihKodeRek(\"$row[1]\", \"$row[2]\")'>";
            echo "</td>";
            echo "<td align='center'>$row[1]</td>";
            echo "<td align='left'>$row[2]</td>";
            echo "<td align='center'>$nUsage</td>";
            echo "<td align='center'>";
            echo "<img src='../images/ico/question.gif' onclick='infoKodeRek(\"$row[1]\")' title='info'>&nbsp;";
            echo "<img src='../images/ico/ubah.png' onclick='editKodeRek($row[0],\"$row[1]\",\"$row[2]\",\"$row[3]\")' title='ubah'>&nbsp;";
            echo "<img src='../images/ico/hapus.png' onclick='hapusKodeRek(\"$row[1]\")' title='hapus'>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "krqtj");
    }
    finally
    {
        $db->Close();
    }

}

function CheckRekUsage($db, $kode)
{
    $sql = "SELECT COUNT(replid)
              FROM jbsfina.jurnaldetail
             WHERE koderek = '$kode'
             LIMIT 1";
    $nData = $db->ExecuteScalar($sql, 0);
    if ($nData > 0)
        return $nData;

    $sql = "SELECT COUNT(replid)
              FROM jbsfina.datapenerimaan
             WHERE rekkas = '$kode' 
                OR rekpendapatan = '$kode' 
                OR rekpiutang = '$kode' 
                OR info1 = '$kode'";
    $nData = $db->ExecuteScalar($sql, 0);
    if ($nData > 0)
        return $nData;

    $sql = "SELECT COUNT(replid)
              FROM jbsfina.datapengeluaran
             WHERE rekdebet = '$kode' 
                OR rekkredit = '$kode'";
    $nData = $db->ExecuteScalar($sql, 0);
    if ($nData > 0)
        return $nData;

    $sql = "SELECT COUNT(replid)
              FROM jbsfina.datatabungan
             WHERE rekkas = '$kode' 
                OR rekutang = '$kode'";
    $nData = $db->ExecuteScalar($sql, 0);
    if ($nData > 0)
        return $nData;

    $sql = "SELECT COUNT(replid)
              FROM jbsfina.datatabunganp
             WHERE rekkas = '$kode' 
                OR rekutang = '$kode'";
    $nData = $db->ExecuteScalar($sql, 0);
    if ($nData > 0)
        return $nData;

    $sql = "SELECT COUNT(replid)
              FROM jbsfina.paymenttabungan
             WHERE rekkasvendor = '$kode' 
                OR rekutangvendor = '$kode'";
    $nData = $db->ExecuteScalar($sql, 0);
    if ($nData > 0)
        return $nData;

    return 0;
}

function CheckKodeRekUsage()
{
    $kode = $_REQUEST["kode"];

    $db = new Db();
    try
    {
        $db->Open();

        $nData = CheckRekUsage($db, $kode);
        $isUsed = $nData > 0 ? "used" : "notused";

        return json_encode([$nData, $isUsed, ""]);
    }
    catch (Exception $ex)
    {
        return json_encode([-1, "ERROR: " . $ex->getMessage(), ""]);
    }
    finally
    {
        $db->Close();
    }
}

function createJsonReturn($status, $message, $data)
{
    $ret = array($status, $message, $data);
    return json_encode($ret);
}
?>
