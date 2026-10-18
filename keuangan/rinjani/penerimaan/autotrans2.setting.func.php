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
function ShowSelectDepartemen($db)
{
    global $departemen;

    echo "<select name='departemen' id='departemen' onChange='refreshPage()' class='inputbox' style='width:200px'>";
    $dep = getDepartemen($db, getAccess());
    foreach($dep as $value)
    {
        if ($departemen == "") $departemen = $value;
        $sel = $departemen == $value ? "selected" : "";
        echo "<option value='$value' $sel>$value</option>";
    }
    echo "</select>";
}

function HapusAutoTrans($idAutoTrans)
{
    $db = new Db();
    try
    {
        $db->Open();
        $db->BeginTrans();

        $sql = "DELETE FROM jbsfina.autotransdata WHERE idautotrans = $idAutoTrans";
        $db->QueryDb($sql);

        $sql = "DELETE FROM jbsfina.autotrans WHERE replid = $idAutoTrans";
        $db->QueryDb($sql);

        $db->CommitTrans();
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
        $db->RollbackTrans();
    }
    finally
    {
        $db->Close();
    }

}

function ShowDaftar($db, $departemen)
{
    ?>
    <table border="1" id="table" class="tab" cellpadding="2" cellspacing="0" style="border-collapse: collapse; border-width: 1px" width="1150">
    <tr style="height: 25px">
        <td class="header" width="50" align="center">No</td>
        <td class="header" width="350">Batch Payment</td>
        <td class="header" width="450">Transaksi Penerimaan</td>
        <td class="header" width="100" align="center">Urutan</td>
        <td class="header" width="100" align="center">Aktif</td>
        <td class="header" width="100" align="center">&nbsp;</td>
    </tr>
    <?php
    $sql = "SELECT * 
              FROM jbsfina.autotrans
             WHERE departemen = '$departemen'
             ORDER BY urutan";
    $res = $db->QueryDb($sql);
    $no = 0;
    while($row = mysqli_fetch_array($res))
    {
        $no += 1;

        $idAutoTrans = $row["replid"];
        $imgAktif = $row["aktif"] == 1 ? "../images/ico/aktif.png" : "../images/ico/nonaktif.png";
        $kelompok = $row["kelompok"] == 1 ? "Siswa" : "Calon Siswa";
        ?>
        <tr>
            <td align="center" valign="top" class="numberColumn"><?=$no?></td>
            <td valign="top"><strong><?=$row['judul']?></strong><br>Pembayaran <?=$kelompok?><br><i><?=$row['keterangan']?></i></td>
            <td valign="top">
                <table class="tab" border="1" cellspacing="0" cellpadding="0" width="370">
<?php       $sql = "SELECT dp.nama, ad.besar
                      FROM jbsfina.autotransdata ad, jbsfina.datapenerimaan dp
                     WHERE ad.idpenerimaan = dp.replid
                       AND ad.idautotrans = $idAutoTrans
                       AND ad.aktif = 1
                     ORDER BY ad.urutan";
                    $res2 = $db->QueryDb($sql);
                    while($row2 = mysqli_fetch_array($res2))
                    {
                        echo "<tr style='height: 15px'>";
                        echo "<td align='left' width='250'>$row2[nama]</td>";
                        echo "<td align='right' width='120'>" . FormatRupiah($row2['besar']) . "</td>";
                        echo "<tr>";
                    } ?>
                </table>
            </td>
            <td align="center" valign="top"><?=$row["urutan"]?></td>
            <td align="center" valign="top">
                <input type="hidden" id="aktif-<?=$no?>" value="<?=$row['aktif']?>">
                <a onclick='setAktif(<?=$no?>, <?=$idAutoTrans?>)' style='cursor: pointer'><img id='img-<?=$no?>' src='<?=$imgAktif?>'></a>
            </td>

            <td align="center" valign="top">
                <a onclick="edit(<?=$idAutoTrans?>)" style="cursor: pointer"><img src="../images/ico/ubah.png" title="edit"></a>&nbsp;
                <a onclick="hapus(<?=$idAutoTrans?>)" style="cursor: pointer"><img src="../images/ico/hapus.png" title="edit"></a>
            </td>
        </tr>
        <?php
    }

    if ($no == 0)
    {
        echo "<tr height='80'>";
        echo "<td align='center' valign='middle' colspan='8'><i>belum ada data pengaturan batch payment</i></td>";
        echo "</tr>";
    }
    ?>
    </table>
<?php
}

function SetAktif($db, $idAutoTrans, $newAktif)
{
    $db = new Db();
    try
    {
        $db->Open();

        $sql = "UPDATE jbsfina.autotrans 
                   SET aktif = $newAktif 
                 WHERE replid = $idAutoTrans";
        $db->QueryDb($sql);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();
    }
    finally
    {
        $db->Close();
    }

}
?>