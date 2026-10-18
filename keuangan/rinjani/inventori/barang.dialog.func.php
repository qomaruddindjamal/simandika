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
function LoadValues($db)
{
    global $id;
    global $kode, $nama, $jumlah, $satuan, $harga, $kondisi, $keterangan;
    global $tanggal, $ftanggal, $totalharga;

    $sql = "SELECT * 
              FROM jbsfina.barang 
             WHERE replid = $id";
    $result = $db->QueryDb($sql);
    $row = mysqli_fetch_array($result);
    $kode = $row['kode'];
    $nama = $row['nama'];
    $jumlah = $row['jumlah'];
    $kondisi = $row['kondisi'];
    $keterangan = $row['keterangan'];
    $satuan = $row['satuan'];
    $tanggal = $row['tglperolehan'];
    $ftanggal = LongDateFormat($tanggal);
    $harga = $row['info1'];
    $totalharga = $harga * $jumlah;
    $harga = FormatRupiah($harga);
    $totalharga = FormatRupiah($totalharga);
}

function SimpanBarangBaru()
{
    $db = new Db();
    try
    {
        $db->Open();

        $hasfoto = $_REQUEST["hasfoto"];
        $sql_foto = "";
        if ($hasfoto == 1)
        {
            $foto = $_FILES["foto"];
            $uploadedfile = $foto['tmp_name'];
            if (strlen($uploadedfile) != 0)
            {
                $tmp_path = realpath(".") . "/../temp";
                $tmp_exists = file_exists($tmp_path) && is_dir($tmp_path);
                if (!$tmp_exists)
                    mkdir($tmp_path, 0755);

                $filename = "$tmp_path/ad-inv-tmp.jpg";
                ResizeImage($foto, 640, 480, 70, $filename);

                $fh = fopen($filename,"r");
                $foto_data = addslashes(fread($fh, filesize($filename)));
                fclose($fh);

                $sql_foto = ", foto = '$foto_data'";
            }
        }

        $idkelompok = RequestData("idkelompok", 0);
        $kode = RequestData("kode", "");
        $nama = RequestData("nama", "");
        $jumlah = RequestData("jumlah", 0);
        $satuan = RequestData("satuan", "");
        $harga = RequestData("harga", 0);
        $tanggal = RequestData("tanggal", date("Y-m-d"));
        $kondisi = RequestData("kondisi", "");
        $keterangan = RequestData("keterangan", "");

        $sql = "INSERT INTO jbsfina.barang 
                   SET kode = '$kode', nama = '$nama',
					   jumlah = '$jumlah' , kondisi = '$kondisi', tglperolehan = '$tanggal',
					   keterangan = '$keterangan', idkelompok = '$idkelompok',
					   satuan = '$satuan', info1 = '$harga' $sql_foto";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k2qzu")]);
    }
    finally
    {
        $db->Close();
    }
}

function SimpanBarangEdit()
{
    $db = new Db();
    try
    {
        $db->Open();

        $hasfoto = $_REQUEST["hasfoto"];
        $sql_foto = "";
        if ($hasfoto == 1)
        {
            $foto = $_FILES["foto"];
            $uploadedfile = $foto['tmp_name'];
            if (strlen($uploadedfile) != 0)
            {
                $tmp_path = realpath(".") . "/../temp";
                $tmp_exists = file_exists($tmp_path) && is_dir($tmp_path);
                if (!$tmp_exists)
                    mkdir($tmp_path, 0755);

                $filename = "$tmp_path/ad-inv-tmp.jpg";
                ResizeImage($foto, 640, 480, 70, $filename);

                $fh = fopen($filename,"r");
                $foto_data = addslashes(fread($fh, filesize($filename)));
                fclose($fh);

                $sql_foto = ", foto = '$foto_data'";
            }
        }

        $idkelompok = RequestData("idkelompok", 0);
        $id = RequestData("id", 0);
        $kode = RequestData("kode", "");
        $nama = RequestData("nama", "");
        $jumlah = RequestData("jumlah", 0);
        $satuan = RequestData("satuan", "");
        $harga = RequestData("harga", 0);
        $tanggal = RequestData("tanggal", date("Y-m-d"));
        $kondisi = RequestData("kondisi", "");
        $keterangan = RequestData("keterangan", "");

        $sql = "UPDATE jbsfina.barang 
                   SET kode = '$kode', nama = '$nama',
					   jumlah = '$jumlah' , kondisi = '$kondisi', tglperolehan = '$tanggal',
					   keterangan = '$keterangan', idkelompok = '$idkelompok',
					   satuan = '$satuan', info1 = '$harga' $sql_foto
				 WHERE replid = $id";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->LogLastErrorIfExist();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k2qzu")]);
    }
    finally
    {
        $db->Close();
    }
}
?>