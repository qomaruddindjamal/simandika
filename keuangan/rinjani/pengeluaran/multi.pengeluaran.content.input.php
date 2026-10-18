<?php
require_once('../include/sessioninfo.php');
require_once('../include/sessionchecker.php');
require_once('../library/common.func.php');
require_once('../include/config.php');
require_once('../include/db.onfunc.php');

$db = new Db();
$db->TryOpenExit();

$idpengeluaran = $_REQUEST["idpengeluaran"];

$sql = "SELECT nama, rekkredit, keterangan, rekdebet
          FROM jbsfina.datapengeluaran
         WHERE replid = $idpengeluaran";
$res = $db->QueryDb($sql);
if (mysqli_num_rows($res) == 0)
{
    $db->Close();

    echo "<span style='color: maroon'>tidak ditemykan data pengeluaran</span>";
    exit();
}

$row = mysqli_fetch_row($res);
$nama = $row[0];
$rekkredit = $row[1];
$keterangan = $row[2];
$rekdebet = $row[3];
?>

<table border="0" cellpadding="5" cellspacing="0">
<tr>
    <td width="60">Jumlah:<?= $tag_mandatory ?></td>
    <td>
        <input type="text" class="inputbox-money bg-light-blue" id="jumlah" style="width: 180px" maxlength="18"
               onblur="Rupiah.FormatRupiah('jumlah')"
               onfocus="Rupiah.UnformatRupiah('jumlah')">
    </td>
</tr>
<tr>
    <td>Keperluan:<?= $tag_mandatory ?></td>
    <td>
        <input type="text" class="inputbox" id="keperluan" style="width: 300px" maxlength="255">
    </td>
</tr>
<tr>
    <td>Tanggal:<?= $tag_mandatory ?></td>
    <td>
        <input type="text" id="txTglJurnal" readonly size="15"
               value="<?= LongDateFormat(date("Y-m-d")) ?>"
               onclick="showPilihTanggal()"
               class="inputbox" style="background-color:#ddd; width: 150px;">&nbsp;
        <input type="hidden" id="tglJurnal" value="<?= date("Y-m-d") ?>">
        <a href="#" onclick="showPilihTanggal()">
            <img src="../images/ico/calendar.png" border="0" id="bttutup"/>
        </a>
    </td>
</tr>
<tr>
    <td>Sumber Dana:<?= $tag_mandatory ?></td>
    <td>
        <input type="hidden" id="rekbeban" value="<?=$rekdebet?>">
        <select id="rekkas" class="inputbox" style="width: 250px">
<?php       $sql = "SELECT kode, nama
                      FROM jbsfina.rekakun
                     WHERE kategori = 'HARTA' 
                     ORDER BY kode";
            $res = $db->QueryDb($sql);
            while($row = mysqli_fetch_row($res))
            {
                $sel = $rekkredit == $row[0] ? "selected" : "";
                echo "<option value='$row[0]' $sel>$row[0] $row[1]</option>";
            }

            ?>
        </select>
    </td>
</tr>
<tr>
    <td width="60">Pengguna:<?= $tag_mandatory ?></td>
    <td>
        <input type="text" class="inputbox" id="pengguna" style="width: 180px" maxlength="100">
        <input type="button" class="but" value="(..)" style="height: 28px" onclick="showSelectPengguna()">
        <img src="../images/help32.png" class="tooltip-icon"
             title="informasi"
             onclick="showTooltip(this, '../help/pl_tt_pengguna.html', 'auto', 400)" >
    </td>
</tr>
<tr>
    <td width="60">Penerima:</td>
    <td>
        <input type="text" class="inputbox" id="penerima" style="width: 180px" maxlength="100">
        <input type="button" class="but" value="(..)" style="height: 28px" onclick="showSelectPenerima()">
    </td>
</tr>
<tr>
    <td width="60">Keterangan:</td>
    <td>
        <textarea rows="2" cols="40" class="inputbox" id="keterangan"></textarea>
    </td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td>
        <input type="button" class="dialogButtonPositive" style="width: 240px; height: 40px" value="Tambah ke Daftar Pengeluaran >"
               onclick="addToList()" >
    </td>
</tr>
</table>

