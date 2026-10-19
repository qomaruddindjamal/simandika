<?
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
<?
require_once('infosiswa.session.php');
require_once('infosiswa.security.php');
require_once('infosiswa.config.php');
require_once('infosiswa.profile.func.php');
require_once('../include/common.php');
require_once('../include/config.php');
require_once('../include/rupiah.php');
require_once('../library/datearith.php');
require_once('../include/db_functions.php');

OpenDb();

$nis = $_SESSION["infosiswa.nis"];

$sql = "SELECT *
	      FROM jbsakad.siswa
		 WHERE nis = '$nis'";
$res = QueryDb($sql);
$row = mysqli_fetch_array($res);
$asalsekolah = $row['asalsekolah'];

$sql = "SELECT departemen
          FROM jbsakad.asalsekolah
         WHERE sekolah = '$asalsekolah'";
$res2 = QueryDb($sql);
$row2 = mysqli_fetch_array($res2);
$jenjangsekolah = $row2['departemen'];

$sql = "SELECT departemen 
          FROM jbsakad.siswa s, jbsakad.angkatan a 
         WHERE s.idangkatan = a.replid
           AND s.nis = '$nis'";
$res2 = QueryDb($sql);
$row2 = mysqli_fetch_array($res2);
$departemen = $row2['departemen'];
?>
<script language="javascript" src="infosiswa/infosiswa.profile.js"></script>
<div id="is_profile">
<form name="is_form" id="is_form" method="post" class="anj-form">
<div class="row g-4 g-lg-5 anj-kol-form-blok">
<div class="col-12 col-lg-6 anj-kol-form">

<h3 class="anj-form-judul">Data Pribadi Siswa</h3>

<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_nisn" class="col-12 col-md-3 col-form-label anj-label">NISN</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_nisn" id="is_nisn" maxlength="100" class="form-control" value="<?=$row['nisn']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_nik" class="col-12 col-md-3 col-form-label anj-label">NIK</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_nik" id="is_nik" maxlength="100" class="form-control" value="<?=$row['nik']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_noun" class="col-12 col-md-3 col-form-label anj-label">No UN Sebelumnya</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_noun" id="is_noun" maxlength="100" class="form-control" value="<?=$row['noun']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_nis" class="col-12 col-md-3 col-form-label anj-label">NIS</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_nis" id="is_nis" maxlength="100" class="form-control" value="<?=$row['nis']?>" readonly>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_nama" class="col-12 col-md-3 col-form-label anj-label">Nama</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_nama" id="is_nama" maxlength="255" class="form-control" value="<?=$row['nama']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_panggilan" class="col-12 col-md-3 col-form-label anj-label">Panggilan</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_panggilan" id="is_panggilan" maxlength="100" class="form-control" value="<?=$row['panggilan']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-start mb-2">
    <span class="col-12 col-md-3 anj-label anj-label-blok">Jenis Kelamin</span>
    <div class="col-12 col-md-9 d-flex flex-wrap gap-3 pt-2 pt-md-1">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="is_kelamin" id="is_kelamin_l" value="l" <?= StringIsChecked($row['kelamin'], "l") ?>>
            <label class="form-check-label" for="is_kelamin_l">Laki-laki</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="is_kelamin" id="is_kelamin_p" value="p" <?= StringIsChecked($row['kelamin'], "p") ?>>
            <label class="form-check-label" for="is_kelamin_p">Perempuan</label>
        </div>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_tmplahir" class="col-12 col-md-3 col-form-label anj-label">Tempat Lahir</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_tmplahir" id="is_tmplahir" maxlength="100" class="form-control" value="<?=$row['tmplahir']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <span class="col-12 col-md-3 anj-label anj-label-blok">Tanggal Lahir</span>
    <div class="col-12 col-md-9">
<?      $thn = date('Y');
        $bln = date('n');
        $tgl = date('j');
        
        $date = explode("-", $row['tgllahir']);
        if (count($date) == 3)
        {
            $thn = $date[0];
            $bln = $date[1];
            $tgl = $date[2];
        } ?>
        <div class="anj-kombo-tanggal">
<?      ShowYearCombo('is_thnlahir', 'is_changeTahunLahirSiswa()', 1990, date('Y') + 1, $thn); ?>
<?      ShowMonthCombo('is_blnlahir', 'is_changeBulanLahirSiswa()', $bln); ?>
            <span id="is_divTglLahirSiswa">
<?      ShowDateCombo('is_tgllahir', 'is_changeTanggalLahirSiswa()', date('Y'), date('n'), $tgl); ?>
            </span>
        </div>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_agama" class="col-12 col-md-3 col-form-label anj-label">Agama</label>
    <div class="col-12 col-md-9">
<?      ShowAgamaCombo($row['agama']); ?>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_suku" class="col-12 col-md-3 col-form-label anj-label">Suku</label>
    <div class="col-12 col-md-9">
<?      ShowSukuCombo($row['suku']); ?>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_status" class="col-12 col-md-3 col-form-label anj-label">Status</label>
    <div class="col-12 col-md-9">
<?      ShowStatusCombo($row['status']); ?>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_kondisi" class="col-12 col-md-3 col-form-label anj-label">Kondisi</label>
    <div class="col-12 col-md-9">
<?      ShowKondisiCombo($row['kondisi']); ?>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-start mb-2">
    <span class="col-12 col-md-3 anj-label anj-label-blok">Kewarganegaraan</span>
    <div class="col-12 col-md-9 d-flex flex-wrap gap-3 pt-2 pt-md-1">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="is_warga" id="is_warga_wni" value="WNI" <?= StringIsChecked($row['warga'], "WNI") ?>>
            <label class="form-check-label" for="is_warga_wni">WNI</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="is_warga" id="is_warga_wna" value="WNA" <?= StringIsChecked($row['warga'], "WNA") ?>>
            <label class="form-check-label" for="is_warga_wna">WNA</label>
        </div>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_urutananak" class="col-12 col-md-3 col-form-label anj-label">Anak ke</label>
    <div class="col-12 col-md-9">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="is_urutananak" id="is_urutananak" maxlength="3" class="form-control anj-input-kecil" value="<?=$row['anakke']?>">
            <span class="anj-satuan">dari</span>
            <input type="text" name="is_jumlahanak" id="is_jumlahanak" maxlength="3" class="form-control anj-input-kecil" value="<?=$row['jsaudara']?>">
            <span class="anj-satuan">bersaudara</span>
        </div>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_statusanak" class="col-12 col-md-3 col-form-label anj-label">Status Anak</label>
    <div class="col-12 col-md-9">
<?      ShowStatusAnakCombo( $row['statusanak'] ); ?>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_jkandung" class="col-12 col-md-3 col-form-label anj-label">Jumlah Saudara Kandung</label>
    <div class="col-12 col-md-9">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="is_jkandung" id="is_jkandung" maxlength="3" class="form-control anj-input-kecil" value="<?=$row['jkandung']?>">
            <span class="anj-satuan">orang</span>
        </div>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_jtiri" class="col-12 col-md-3 col-form-label anj-label">Jumlah Saudara Tiri</label>
    <div class="col-12 col-md-9">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="is_jtiri" id="is_jtiri" maxlength="3" class="form-control anj-input-kecil" value="<?=$row['jtiri']?>">
            <span class="anj-satuan">orang</span>
        </div>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_bahasa" class="col-12 col-md-3 col-form-label anj-label">Bahasa</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_bahasa" id="is_bahasa" maxlength="100" class="form-control" value="<?=$row['bahasa']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-start mb-2">
    <label for="is_alamatsiswa" class="col-12 col-md-3 anj-label pt-2">Alamat</label>
    <div class="col-12 col-md-9">
        <textarea name="is_alamatsiswa" id="is_alamatsiswa" rows="2" class="form-control"><?= $row['alamatsiswa'] ?></textarea>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_kodepos" class="col-12 col-md-3 col-form-label anj-label">Kode Pos</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_kodepos" id="is_kodepos" maxlength="8" class="form-control anj-input-sedang" value="<?=$row['kodepossiswa']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_jarak" class="col-12 col-md-3 col-form-label anj-label">Jarak ke Sekolah</label>
    <div class="col-12 col-md-9">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="is_jarak" id="is_jarak" maxlength="4" class="form-control anj-input-kecil" value="<?=$row['jarak']?>">
            <span class="anj-satuan">km</span>
        </div>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_telponsiswa" class="col-12 col-md-3 col-form-label anj-label">Telpon</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_telponsiswa" id="is_telponsiswa" maxlength="20" class="form-control anj-input-sedang" value="<?=$row['telponsiswa']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_hpsiswa" class="col-12 col-md-3 col-form-label anj-label">Handphone</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_hpsiswa" id="is_hpsiswa" maxlength="20" class="form-control anj-input-sedang" value="<?=$row['hpsiswa']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_emailsiswa" class="col-12 col-md-3 col-form-label anj-label">Email</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_emailsiswa" id="is_emailsiswa" maxlength="100" class="form-control" value="<?=$row['emailsiswa']?>">
    </div>
</div>

<h3 class="anj-form-judul">Data Sekolah Siswa</h3>

<div class="row g-2 g-md-3 align-items-start mb-2">
    <span class="col-12 col-md-3 anj-label anj-label-blok pt-2">Asal Sekolah</span>
    <div class="col-12 col-md-9 anj-kombo-bertingkat">
<?      ShowJenjangSekolahCombo($jenjangsekolah) ?>
        <span id='is_divAsalSekolah'>
<?      ShowAsalSekolahCombo($jenjangsekolah, $asalsekolah) ?>
        </span>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_noijasah" class="col-12 col-md-3 col-form-label anj-label">No Ijasah</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_noijasah" id="is_noijasah" maxlength="100" class="form-control" value="<?=$row['noijasah']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_tglijasah" class="col-12 col-md-3 col-form-label anj-label">Tgl Ijasah</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_tglijasah" id="is_tglijasah" maxlength="100" class="form-control anj-input-sedang" value="<?=$row['tglijasah']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-start mb-2">
    <label for="is_ketsekolah" class="col-12 col-md-3 anj-label pt-2">Keterangan</label>
    <div class="col-12 col-md-9">
        <textarea name="is_ketsekolah" id="is_ketsekolah" rows="2" class="form-control"><?=$row['ketsekolah']?></textarea>
    </div>
</div>

</div>
<div class="col-12 col-lg-6 anj-kol-form">

<h3 class="anj-form-judul">Riwayat Kesehatan Siswa</h3>

<div class="row g-2 g-md-3 align-items-start mb-2">
    <span class="col-12 col-md-3 anj-label anj-label-blok">Golongan Darah</span>
    <div class="col-12 col-md-9 d-flex flex-wrap gap-3 pt-2 pt-md-1">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="is_gol" id="is_gol_a" value="A" <?= StringIsChecked($row['darah'], "A") ?>>
            <label class="form-check-label" for="is_gol_a">A</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="is_gol" id="is_gol_ab" value="AB" <?= StringIsChecked($row['darah'], "AB") ?>>
            <label class="form-check-label" for="is_gol_ab">AB</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="is_gol" id="is_gol_b" value="B" <?= StringIsChecked($row['darah'], "B") ?>>
            <label class="form-check-label" for="is_gol_b">B</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="is_gol" id="is_gol_o" value="O" <?= StringIsChecked($row['darah'], "O") ?>>
            <label class="form-check-label" for="is_gol_o">O</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="is_gol" id="is_gol_kosong" value="" <?= StringIsChecked($row['darah'], "") ?>>
            <label class="form-check-label" for="is_gol_kosong"><em>(belum ada data)</em></label>
        </div>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_berat" class="col-12 col-md-3 col-form-label anj-label">Berat</label>
    <div class="col-12 col-md-9">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="is_berat" id="is_berat" maxlength="4" class="form-control anj-input-kecil" value="<?=$row['berat']?>">
            <span class="anj-satuan">kg</span>
        </div>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_tinggi" class="col-12 col-md-3 col-form-label anj-label">Tinggi</label>
    <div class="col-12 col-md-9">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <input type="text" name="is_tinggi" id="is_tinggi" maxlength="4" class="form-control anj-input-kecil" value="<?=$row['tinggi']?>">
            <span class="anj-satuan">cm</span>
        </div>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-start mb-2">
    <label for="is_kesehatan" class="col-12 col-md-3 anj-label pt-2">Riwayat Penyakit</label>
    <div class="col-12 col-md-9">
        <textarea name="is_kesehatan" id="is_kesehatan" rows="3" class="form-control"><?= $row['kesehatan'] ?></textarea>
    </div>
</div>

<h3 class="anj-form-judul">Data Orangtua Siswa</h3>

<div class="row g-2 g-md-3 mb-2 anj-orangtua">
    <span class="col-12 col-md-3"></span>
    <span class="col-12 col-md-4 anj-kepala-kol anj-kol-ayah">Ayah</span>
    <span class="col-12 col-md-4 anj-kepala-kol anj-kol-ibu">Ibu</span>
</div>

<div class="row g-2 g-md-3 align-items-start mb-2 anj-orangtua">
    <label for="is_namaayah" class="col-12 col-md-3 anj-label pt-2">Nama</label>
    <div class="col-12 col-md-4 anj-kol-ayah">
        <input type="text" name="is_namaayah" id="is_namaayah" maxlength="100" class="form-control" value="<?=$row['namaayah']?>">
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="is_almayah" id="is_almayah" value="1" title="Klik disini jika Ayah Almarhum" <?= IntIsChecked($row['almayah'], 1) ?>>
            <label class="form-check-label anj-alm" for="is_almayah">(Almarhum)</label>
        </div>
    </div>
    <div class="col-12 col-md-4 anj-kol-ibu">
        <label for="is_namaibu" class="visually-hidden">Nama Ibu</label>
        <input type="text" name="is_namaibu" id="is_namaibu" maxlength="100" class="form-control" value="<?=$row['namaibu']?>">
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="is_almibu" id="is_almibu" value="1" title="Klik disini jika Ibu Almarhumah" <?= IntIsChecked($row['almibu'], 1) ?>>
            <label class="form-check-label anj-alm" for="is_almibu">(Almarhumah)</label>
        </div>
    </div>
</div>

<div class="row g-2 g-md-3 align-items-center mb-2 anj-orangtua">
    <span class="col-12 col-md-3 anj-label anj-label-blok">Status Orangtua</span>
    <div class="col-12 col-md-4 anj-kol-ayah">
        <? ShowStatusOrtuCombo('is_statusayah', $row['statusayah']) ?>
    </div>
    <div class="col-12 col-md-4 anj-kol-ibu">
        <? ShowStatusOrtuCombo('is_statusibu', $row['statusibu']) ?>
    </div>
</div>

<div class="row g-2 g-md-3 align-items-center mb-2 anj-orangtua">
    <label for="is_tmplahirayah" class="col-12 col-md-3 col-form-label anj-label">Tempat Lahir</label>
    <div class="col-12 col-md-4 anj-kol-ayah">
        <input type="text" name="is_tmplahirayah" id="is_tmplahirayah" maxlength="100" class="form-control" value="<?=$row['tmplahirayah']?>">
    </div>
    <div class="col-12 col-md-4 anj-kol-ibu">
        <label for="is_tmplahiribu" class="visually-hidden">Tempat Lahir Ibu</label>
        <input type="text" name="is_tmplahiribu" id="is_tmplahiribu" maxlength="100" class="form-control" value="<?=$row['tmplahiribu']?>">
    </div>
</div>

<div class="row g-2 g-md-3 align-items-center mb-2 anj-orangtua">
    <span class="col-12 col-md-3 anj-label anj-label-blok">Tanggal Lahir</span>
    <div class="col-12 col-md-4 anj-kol-ayah">
<?      $thn = date('Y');
        $bln = date('n');
        $tgl = date('j');
        
        $date = explode("-", $row['tgllahirayah']);
        if (count($date) == 3)
        {
            $thn = $date[0];
            $bln = $date[1];
            $tgl = $date[2];
        } ?>
        <div class="anj-kombo-tanggal">
<?      ShowYearCombo('is_thnlahirayah', 'is_changeTahunLahirAyah()', 1900, date('Y') + 1, $thn); ?>
<?      ShowMonthCombo('is_blnlahirayah', 'is_changeBulanLahirAyah()', $bln); ?>
            <span id="is_divTglLahirAyah">
<?      ShowDateCombo('is_tgllahirayah', 'is_changeTanggalLahirAyah()', date('Y'), date('n'), $tgl); ?>
            </span>
        </div>
    </div>
    <div class="col-12 col-md-4 anj-kol-ibu">
<?      $thn = date('Y');
        $bln = date('n');
        $tgl = date('j');
        
        $date = explode("-", $row['tgllahiribu']);
        if (count($date) == 3)
        {
            $thn = $date[0];
            $bln = $date[1];
            $tgl = $date[2];
        } ?>
        <div class="anj-kombo-tanggal">
<?      ShowYearCombo('is_thnlahiribu', 'is_changeTahunLahirIbu()', 1900, date('Y') + 1, $thn); ?>
<?      ShowMonthCombo('is_blnlahiribu', 'is_changeBulanLahirIbu()', $bln); ?>
            <span id="is_divTglLahirIbu">
<?      ShowDateCombo('is_tgllahiribu', 'is_changeTanggalLahirIbu()', date('Y'), date('n'), $tgl); ?>
            </span>
        </div>
    </div>
</div>

<div class="row g-2 g-md-3 align-items-center mb-2 anj-orangtua">
    <label for="is_pendidikanayah" class="col-12 col-md-3 col-form-label anj-label">Pendidikan</label>
    <div class="col-12 col-md-4 anj-kol-ayah">
<?      ShowPendidikanCombo('is_pendidikanayah', $row['pendidikanayah']) ?>
    </div>
    <div class="col-12 col-md-4 anj-kol-ibu">
<?      ShowPendidikanCombo('is_pendidikanibu', $row['pendidikanibu']) ?>
    </div>
</div>

<div class="row g-2 g-md-3 align-items-center mb-2 anj-orangtua">
    <label for="is_pekerjaanayah" class="col-12 col-md-3 col-form-label anj-label">Pekerjaan</label>
    <div class="col-12 col-md-4 anj-kol-ayah">
<?      ShowPekerjaanCombo('is_pekerjaanayah', $row['pekerjaanayah']) ?>
    </div>
    <div class="col-12 col-md-4 anj-kol-ibu">
<?      ShowPekerjaanCombo('is_pekerjaanibu', $row['pekerjaanibu']) ?>
    </div>
</div>

<div class="row g-2 g-md-3 align-items-center mb-2 anj-orangtua">
    <label for="is_penghasilanayah" class="col-12 col-md-3 col-form-label anj-label">Penghasilan</label>
    <div class="col-12 col-md-4 anj-kol-ayah">
        <input type="text" name="is_penghasilanayah" id="is_penghasilanayah" maxlength="100" class="form-control"
               onblur="formatRupiah('is_penghasilanayah')" onfocus="unformatRupiah('is_penghasilanayah')" value="<?= FormatRupiah($row['penghasilanayah']) ?>" >
    </div>
    <div class="col-12 col-md-4 anj-kol-ibu">
        <label for="is_penghasilanibu" class="visually-hidden">Penghasilan Ibu</label>
        <input type="text" name="is_penghasilanibu" id="is_penghasilanibu" maxlength="100" class="form-control"
               onblur="formatRupiah('is_penghasilanibu')" onfocus="unformatRupiah('is_penghasilanibu')" value="<?= FormatRupiah($row['penghasilanibu']) ?>" >
    </div>
</div>

<div class="row g-2 g-md-3 align-items-center mb-2 anj-orangtua">
    <label for="is_emailayah" class="col-12 col-md-3 col-form-label anj-label">Email</label>
    <div class="col-12 col-md-4 anj-kol-ayah">
        <input type="text" name="is_emailayah" id="is_emailayah" maxlength="100" class="form-control" value="<?=$row['emailayah']?>">
    </div>
    <div class="col-12 col-md-4 anj-kol-ibu">
        <label for="is_emailibu" class="visually-hidden">Email Ibu</label>
        <input type="text" name="is_emailibu" id="is_emailibu" maxlength="100" class="form-control" value="<?=$row['emailibu']?>">
    </div>
</div>

<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_namawali" class="col-12 col-md-3 col-form-label anj-label">Nama Wali</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_namawali" id="is_namawali" maxlength="100" class="form-control" value="<?=$row['wali']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-start mb-2">
    <label for="is_alamatortu" class="col-12 col-md-3 anj-label pt-2">Alamat Orangtua</label>
    <div class="col-12 col-md-9">
        <textarea name="is_alamatortu" id="is_alamatortu" rows="2" class="form-control"><?= $row['alamatortu'] ?></textarea>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_telponortu" class="col-12 col-md-3 col-form-label anj-label">Telepon</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_telponortu" id="is_telponortu" maxlength="100" class="form-control anj-input-sedang" value="<?=$row['telponortu']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_hportu" class="col-12 col-md-3 col-form-label anj-label">HP Ortu #1</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_hportu" id="is_hportu" maxlength="100" class="form-control anj-input-sedang" value="<?=$row['hportu']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_hportu2" class="col-12 col-md-3 col-form-label anj-label">HP Ortu #2</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_hportu2" id="is_hportu2" maxlength="100" class="form-control anj-input-sedang" value="<?=$row['info1']?>">
    </div>
</div>
<div class="row g-2 g-md-3 align-items-center mb-2">
    <label for="is_hportu3" class="col-12 col-md-3 col-form-label anj-label">HP Ortu #3</label>
    <div class="col-12 col-md-9">
        <input type="text" name="is_hportu3" id="is_hportu3" maxlength="100" class="form-control anj-input-sedang" value="<?=$row['info2']?>">
    </div>
</div>

<h3 class="anj-form-judul">Informasi Tambahan</h3>

<div class="row g-2 g-md-3 align-items-start mb-2">
    <label for="is_hobi" class="col-12 col-md-3 anj-label pt-2">Hobi</label>
    <div class="col-12 col-md-9">
        <textarea name="is_hobi" id="is_hobi" rows="2" class="form-control"><?= $row['hobi'] ?></textarea>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-start mb-2">
    <label for="is_alamatsurat" class="col-12 col-md-3 anj-label pt-2">Alamat Surat</label>
    <div class="col-12 col-md-9">
        <textarea name="is_alamatsurat" id="is_alamatsurat" rows="2" class="form-control"><?= $row['alamatsurat'] ?></textarea>
    </div>
</div>
<div class="row g-2 g-md-3 align-items-start mb-2">
    <label for="is_keterangan" class="col-12 col-md-3 anj-label pt-2">Keterangan</label>
    <div class="col-12 col-md-9">
        <textarea name="is_keterangan" id="is_keterangan" rows="2" class="form-control"><?= $row['keterangan'] ?></textarea>
    </div>
</div>

<h3 class="anj-form-judul">Data Tambahan</h3>
<?php
    $sql = "SELECT replid, kolom, jenis
              FROM tambahandata 
             WHERE aktif = 1
               AND departemen = '$departemen'
             ORDER BY urutan";
    $res = QueryDb($sql);
    $idtambahan = "";
    while($row = mysqli_fetch_row($res))
    {
        $replid = $row[0];
        $kolom = $row[1];
        $jenis = $row[2];

        if ($idtambahan != "") $idtambahan .= ",";
        $idtambahan .= $replid;

        $replid_data = 0;
        $data = "";
        if ($jenis == 1)
        {
            $sql = "SELECT replid, teks FROM tambahandatasiswa WHERE nis = '$nis' AND idtambahan = '$replid'";
            $res2 = QueryDb($sql);
            if ($row2 = mysqli_fetch_row($res2))
            {
                $replid_data = $row2[0];
                $data = $row2[1];
            }
        }
        else if ($jenis == 2)
        {
            $sql = "SELECT replid, filename FROM tambahandatasiswa WHERE nis = '$nis' AND idtambahan = '$replid'";
            $res2 = QueryDb($sql);
            if ($row2 = mysqli_fetch_row($res2))
            {
                $replid_data = $row2[0];
                $filename = $row2[1];
                $data = "<a href='infosiswa/infosiswa.profile.file.php?replid=$replid_data'>$filename</a>";
            }
            else
            {
                $replid_data = 0;
                $data = "(belum ada)";
            }
        }
        else if ($jenis == 3)
        {
            $sql = "SELECT replid, teks FROM tambahandatasiswa WHERE nis = '$nis' AND idtambahan = '$replid'";
            $res2 = QueryDb($sql);
            if ($row2 = mysqli_fetch_row($res2))
            {
                $replid_data = $row2[0];
                $data = $row2[1];
            }

            $sql = "SELECT pilihan 
                      FROM jbsakad.pilihandata 
                     WHERE idtambahan = '$replid'
                       AND aktif = 1
                     ORDER BY urutan";
            $res2 = QueryDb($sql);

            $arrList = array();
            if (mysqli_num_rows($res2) == 0)
                $arrList[] = "-";

            while($row2 = mysqli_fetch_row($res2))
            {
                $arrList[] = $row2[0];
            }

            $opt = "";
            for($i = 0; $i < count($arrList); $i++)
            {
                $pilihan = CQ($arrList[$i]);
                $sel = $pilihan == $data ? "selected" : "";
                $opt .= "<option value='$pilihan' $sel>$pilihan</option>";
            }
        }

        ?>
        <div class="row g-2 g-md-3 align-items-center mb-2">
            <span class="col-12 col-md-3 anj-label anj-label-blok"><?=htmlspecialchars($kolom)?></span>
            <div class="col-12 col-md-9">
                <? if ($jenis == 1) { ?>
                    <input type="hidden" id="is_jenisdata-<?=$replid?>" name="is_jenisdata-<?=$replid?>" value="1">
                    <input type="hidden" id="is_repliddata-<?=$replid?>" name="is_repliddata-<?=$replid?>" value="<?=$replid_data?>">
                    <input type="text" class="form-control" name="is_tambahandata-<?=$replid?>" id="is_tambahandata-<?=$replid?>" maxlength="1000" value="<?=$data?>">
                <? } else if ($jenis == 2) { ?>
                    <input type="hidden" id="is_jenisdata-<?=$replid?>" name="is_jenisdata-<?=$replid?>" value="2">
                    <input type="hidden" id="is_repliddata-<?=$replid?>" name="is_repliddata-<?=$replid?>" value="<?=$replid_data?>">
                    <input type="hidden" name="is_tambahandata-<?=$replid?>" id="is_tambahandata-<?=$replid?>" value="">
                    <i class="anj-satuan"><?=$data?></i>
                <? } else { ?>
                    <input type="hidden" id="is_jenisdata-<?=$replid?>" name="is_jenisdata-<?=$replid?>" value="3">
                    <input type="hidden" id="is_repliddata-<?=$replid?>" name="is_repliddata-<?=$replid?>" value="<?=$replid_data?>">
                    <select class="inputbox" name="is_tambahandata-<?=$replid?>" id="is_tambahandata-<?=$replid?>">
                        <?= $opt ?>
                    </select>
                <? } ?>
            </div>
        </div>
        <?
    }
    ?>
    <input type="hidden" id="is_idtambahan" name="is_idtambahan" value="<?=$idtambahan?>">

<div class="anj-form-aksi">
<?		if ($IsAllowStudentEdit) { ?>		
    <input type="button" value="Simpan" class="but" onclick="is_Simpan()">
<?		} else { ?>
		<span class="anj-tidak-boleh-ubah">
			Tidak dapat mengubah data pribadi siswa. Silahkan hubungi Administrator JIBAS untuk mengatur perubahan data pribadi siswa.
		</span>
<?		} ?>
</div>
</div>
</div>
</form>
</div>
<?
CloseDb();
?>