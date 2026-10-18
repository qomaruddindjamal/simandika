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
function ShowSelectKategori()
{
    global $kategori;

    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT * 
                  FROM jbsfina.katerekakun 
                 ORDER BY urutan";
        $result = $db->QueryDb($sql);

        echo "<select class='inputbox' name='kategori' id='kategori' onChange='change_kategori()' style='width:150px'>";
        while ($row = mysqli_fetch_array($result))
        {
            if ($kategori == "")
                $kategori = $row['kategori'];

            $sel = $kategori == $row["kategori"] ? "selected" : "";
            echo "<option value='$row[kategori]' $sel>$row[kategori]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k3edk");
    }
    finally
    {
        $db->Close();
    }
}

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

        $output  = "<h2>$kodeRek - $namaRek</h2>";
        $output .= "<p style='font-size: 13px; font-family: \"Segoe UI\", sans-serif'>";
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

function CountRekAkunUsage($db, $kodeRek)
{
    $sql = "SELECT
              (SELECT COUNT(replid) FROM jbsfina.datapenerimaan WHERE rekkas = '$kodeRek' OR rekpendapatan = '$kodeRek' OR rekpiutang = '$kodeRek') +
              (SELECT COUNT(replid) FROM jbsfina.datapengeluaran WHERE rekdebet = '$kodeRek' OR rekkredit = '$kodeRek') +
              (SELECT COUNT(replid) FROM jbsfina.datatabungan WHERE rekkas = '$kodeRek' OR rekutang = '$kodeRek') +
              (SELECT COUNT(replid) FROM jbsfina.datatabunganp WHERE rekkas = '$kodeRek' OR rekutang = '$kodeRek') +
              (SELECT COUNT(id) FROM jbsfina.pgservicefee2 WHERE rekkas = '$kodeRek' OR rekpendapatan = '$kodeRek') +
              (SELECT COUNT(replid) FROM jbsfina.paymenttabungan WHERE rekkasvendor = '$kodeRek' OR rekutangvendor = '$kodeRek')";
    $res = $db->QueryDb($sql);
    $row = mysqli_fetch_row($res);
    return $row[0];
}

function ListRekAkunUsage($db, $kodeRek)
{
    $sql = "SELECT GROUP_CONCAT(nama SEPARATOR ', ')
              FROM (
                    SELECT nama FROM jbsfina.datapenerimaan WHERE rekkas = '$kodeRek' OR rekpendapatan = '$kodeRek' OR rekpiutang = '$kodeRek'
                    UNION
                    SELECT nama FROM jbsfina.datapengeluaran WHERE rekdebet = '$kodeRek' OR rekkredit = '$kodeRek'
                    UNION
                    SELECT nama FROM jbsfina.datatabungan WHERE rekkas = '$kodeRek' OR rekutang = '$kodeRek'
                    UNION
                    SELECT nama FROM jbsfina.datatabunganp WHERE rekkas = '$kodeRek' OR rekutang = '$kodeRek'
                    UNION
                    SELECT nama FROM jbsfina.pgservicefee2 WHERE rekkas = '$kodeRek' OR rekpendapatan = '$kodeRek'
                    UNION
                    SELECT 'Pembayaran Vendor SchoolPay' AS nama FROM jbsfina.paymenttabungan WHERE rekkasvendor = '$kodeRek'  OR rekutangvendor = '$kodeRek'
                   ) AS x";
    $res = $db->QueryDb($sql);
    $row = mysqli_fetch_row($res);
    return $row[0];
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

function HapusRekAkun()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idRekAkun = $_REQUEST["idrekakun"];
        $kode = SafeInput($_REQUEST["kode"]);

        $nData = CheckRekUsage($db, $kode);
        if ($nData > 0)
        {
            return json_encode([-1, "Tidak dapat menghapus rekening ini karena sudah digunakan dalam transaksi"]);
        }

        $sql = "DELETE FROM jbsfina.rekakun
                 WHERE replid = $idRekAkun";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();

        return json_encode( [-99, Msg::InfoError($ex->getMessage(), "kbq0y")] );
    }
    finally
    {
        $db->Close();
    }
}

function ShowTableRekAkun()
{
    global $kategori;

    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT * 
                  FROM jbsfina.rekakun 
                 WHERE kategori = '$kategori' 
                 ORDER BY kode";
        $result = $db->QueryDb($sql);
        if (mysqli_num_rows($result) == 0)
        {
            echo "<i>belum ada kode akuntansi untuk $kategori</i>";
            return;
        }

?>
    <table class="tab" id="table" border="1" style="border-collapse:collapse" width="95%" align="center" bordercolor="#000000">
    <tr height="30" align="center">
        <td class="header" width="50">No</td>
        <td class="header" width="10%">Kode</td>
        <td class="header" width="20%">Nama</td>
        <td class="header" width="7%">Penggunaan</td>
        <td class="header">Keterangan</td>
        <td class="header colButton" width="100">&nbsp;</td>
    </tr>
<?php
        $no = 0;
        while ($row = mysqli_fetch_array($result))
        {
            $kode = $row['kode'];
            $nUsage = CountRekAkunUsage($db, $kode);

            ?>
            <tr style="height: 25px;">
                <td align="center" style="background-color: #eee"><?=++$no ?></td>
                <td align="center"><?=$row['kode'] ?></td>
                <td><?=$row['nama'] ?></td>
                <td align="center"><?= $nUsage ?></td>
                <td><?=$row['keterangan'] ?></td>
                <td align="center" class="colButton">
                    <img src='../images/ico/question.gif' class="ImageHover" onclick='showInfoRek("<?= $row['kode'] ?>")' title='Info Rekening'>&nbsp;
                    <img src="../images/ico/ubah.png" class="ImageHover" onclick='edit(<?= $row['replid'] ?>, "<?= $row['kode'] ?>")' title="Ubah Rekening">&nbsp;
                    <img src="../images/ico/hapus.png" class="ImageHover" onclick='hapus(<?= $row['replid'] ?>, "<?= $row['kode'] ?>")' title="Hapus Rekening">
                </td>
            </tr>
<?php
        }
        echo "</table>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "k3edk");
    }
    finally
    {
        $db->Close();
    }
}
?>
