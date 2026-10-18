<?php
function SimpanSetAktif()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idLokasiDana = RequestData('idlokasidana', 0);
        $newAktif = RequestData('newaktif', 1);

        $sql = "UPDATE jbsfina.lokasidana
                   SET aktif = $newAktif
                 WHERE id = $idLokasiDana";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kdwwh")]);
    }
    finally
    {
        $db->Close();
    }
}

function HapusLokasiDana()
{
    $db = new Db();
    try
    {
        $db->Open();

        $idLokasiDana = RequestData('idlokasidana', 0);

        $sql = "DELETE FROM jbsfina.lokasidana
                 WHERE id = $idLokasiDana";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $le = $db->LastError();
        if ($le[0] == 1451)
            return json_encode([-1, "Tidak dapat menghapus data ini karena sudah digunakan! /knzah"]);

        $db->LogLastErrorIfExist();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kdwwh")]);
    }
    finally
    {
        $db->Close();
    }
}

function ShowTableLokasiDana()
{
    $db = new Db();
    try
    {
        $db->Open();

        $sql = "SELECT * 
                  FROM jbsfina.lokasidana 
                 ORDER BY urutan";
        $result = $db->QueryDb($sql);
        if (mysqli_num_rows($result) == 0)
        {
            echo "<i>belum ada data lokasi dana</i>";
            return;
        }

?>
        <table class="tab" id="table" border="1" style="border-collapse:collapse" width="95%" align="center" bordercolor="#000000">
        <tr height="30" align="center">
            <td class="header" width="50">No</td>
            <td class="header" width="10%">Kode</td>
            <td class="header" width="20%">Nama</td>
            <td class="header" width="15%">Kelompok</td>
            <td class="header" width="7%">Aktif</td>
            <td class="header">Keterangan</td>
            <td class="header colButton" width="100">&nbsp;</td>
        </tr>
<?php
        $no = 0;
        while ($row = mysqli_fetch_array($result))
        {
            $id = $row['id'];
            $kode = $row['kode'];
            ?>
            <tr style="height: 25px;">
                <td align="center" style="background-color: #eee"><?=++$no ?></td>
                <td align="center"><?=$row['kode'] ?></td>
                <td><?=$row['nama'] ?></td>
                <td align="center"><?=$row['kelompok'] ?></td>
                <td align="center">
<?php
                if ($kode == "TUNAI")
                {
                    echo "&nbsp;";
                }
                else
                {
                    echo "<input type='hidden' id='statusaktif-$id' value='$row[aktif]'>";
                    if ($row['aktif'] == 1)
                    {
                        echo "<img id='imaktif-$id' src='../images/ico/aktif.png' title='aktif' style='cursor: pointer'
                                   onclick='setAktif($id)' >";
                    }
                    else
                    {
                        echo "<img id='imaktif-$id' src='../images/ico/nonaktif.png' title='aktif' style='cursor: pointer'
                                   onclick='setAktif($row[id])' >";
                    }
                }
?>
                </td>
                <td><?=$row['keterangan'] ?></td>
                <td align="center" class="colButton">
<?php
                if ($kode == "TUNAI")
                {
                    echo "&nbsp;";
                }
                else
                {
                    echo "<img src='../images/ico/ubah.png' class='ImageHover' onclick='edit($id)' title='Ubah'>&nbsp;";
                    echo "<img src='../images/ico/hapus.png' class='ImageHover' onclick='hapus($id)' title='Hapus'>";
                }
?>
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