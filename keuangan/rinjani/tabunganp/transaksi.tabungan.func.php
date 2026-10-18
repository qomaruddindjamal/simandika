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
$nRowPerPage = 10;

function SimpanSetoranPegawai()
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = RequestData("departemen", "");
        $nip = RequestData("nip", "");
        $namapegawai = RequestData("namapegawai", "");
        $idtahunbuku = RequestData("idtahunbuku", 0);
        $idtabungan = RequestData("idtabungan", 0);
        $namatabungan = RequestData("namatabungan", "");
        $jumlah = RequestData("jumlah", 0);
        $keterangansetor = RequestData("keterangan", "");
        $sendnotif = RequestData("sendnotif", 0);

        $sumberDana = RequestData("sumberdana", "***");
        $sumberDanaValue = $sumberDana == "***" ? "NULL" : "'$sumberDana'";

        $lokasiDana = RequestData("lokasidanasetor", "***");
        $lokasiDanaValue = $lokasiDana == "***" ? "NULL" : "'$lokasiDana'";

        // Ambil informasi kode rekening
        $sql = "SELECT rekkas, rekutang
                  FROM jbsfina.datatabunganp
                 WHERE replid = '$idtabungan'";
        $res = $db->QueryDb($sql);
        if (mysqli_num_rows($res) == 0)
            return json_encode([-1, "Tidak ditemukan data tabungan /kj8mf"]);
        $row = mysqli_fetch_row($res);
        $rekkas = $row[0];
        $rekutang = $row[1];

        //Ambil awalan dan cacah tahunbuku untuk bikin nokas;
        $sql = "SELECT awalan, cacah 
                  FROM jbsfina.tahunbuku 
                 WHERE replid = '$idtahunbuku'";
        $res = $db->QueryDb($sql);
        if (mysqli_num_rows($res) == 0)
            return json_encode([-1, "Tidak ditemukan data tahun buku /kx3du"]);
        $row = mysqli_fetch_row($res);
        $awalan = $row[0];
        $cacah = $row[1];

        $cacah += 1; // Increment cacah
        $nokas = $awalan . rpad($cacah, "0", 6); // Form nomor kas

        $sql = "SELECT SUM(kredit - debet)
                  FROM jbsfina.tabunganp
                 WHERE nip = '$nip'
                   AND idtabungan = '$idtabungan'";
        $jsaldo = $db->FetchSingle($sql, 0);

        $tsetor = date("Y-m-d");
        $idpetugas = getIdUser();
        $petugas = getUserName();
        $keterangan = "Setoran Tabungan $namatabungan pegawai $namapegawai ($nip)";
        $ketsms = "Setoran Tabungan $namatabungan";

        $db->BeginTrans();

        $idjurnal = SimpanJurnal2($db, $idtahunbuku, $tsetor, $keterangan, $nokas, "", $idpetugas, $petugas, "setorantabungan");

        $sql = "INSERT INTO jbsfina.tabunganp
                   SET nip='$nip', idtabungan='$idtabungan', debet='0', kredit='$jumlah',
                       keterangan = '$keterangansetor', 
                       petugas='$idpetugas', idjurnal='$idjurnal', tanggal=NOW(),
                       sumberdana = $sumberDanaValue, lokasidana = $lokasiDanaValue";
        //Logger::LogOnce($sql);
        $db->QueryDb($sql);

        SimpanDetailJurnal2($db, $idjurnal, "D", $rekkas, $jumlah);
        SimpanDetailJurnal2($db, $idjurnal, "K", $rekutang, $jumlah);

        $sql = "UPDATE jbsfina.tahunbuku 
                   SET cacah = cacah + 1 
                 WHERE replid = $idtahunbuku";
        $db->QueryDb($sql);

        if ($sendnotif == 1)
        {
            $jsaldo = $jsaldo + $jumlah;

            CreateSMSTabungan2($db, 'PEGTAB',
                $departemen, $nip, $namapegawai,
                RegularDateFormat($tsetor),
                FormatRupiah($jumlah),
                FormatRupiah($jsaldo),
                $ketsms,
                $keterangansetor);
        }

        $db->CommitTrans();
        //$db->RollbackTrans();

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->RollbackTrans();
        $db->LogLastErrorIfExist();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kdska") ]);
    }
    finally
    {
        $db->Close();
    }

}

function SimpanTarikanPegawai()
{
    $db = new Db();
    try
    {
        $db->Open();

        $departemen = RequestData("departemen", "");
        $nip = RequestData("nip", "");
        $namapegawai = RequestData("namapegawai", "");
        $idtahunbuku = RequestData("idtahunbuku", 0);
        $idtabungan = RequestData("idtabungan", 0);
        $namatabungan = RequestData("namatabungan", "");
        $jumlah = RequestData("jumlah", 0);
        $keterangansetor = RequestData("keterangan", "");
        $sendnotif = RequestData("sendnotif", 0);

        $lokasiDana = RequestData("lokasidanatarik", "***");
        $lokasiDanaValue = $lokasiDana == "***" ? "NULL" : "'$lokasiDana'";

        // Ambil informasi kode rekening
        $sql = "SELECT rekkas, rekutang
                  FROM jbsfina.datatabunganp
                 WHERE replid = '$idtabungan'";
        $res = $db->QueryDb($sql);
        if (mysqli_num_rows($res) == 0)
            return json_encode([-1, "Tidak ditemukan data tabungan /kvwh7"]);
        $row = mysqli_fetch_row($res);
        $rekkas = $row[0];
        $rekutang = $row[1];

        //Ambil awalan dan cacah tahunbuku untuk bikin nokas;
        $sql = "SELECT awalan, cacah 
                  FROM jbsfina.tahunbuku 
                 WHERE replid = '$idtahunbuku'";
        $res = $db->QueryDb($sql);
        if (mysqli_num_rows($res) == 0)
            return json_encode([-1, "Tidak ditemukan data tahun buku /kfntd"]);
        $row = mysqli_fetch_row($res);
        $awalan = $row[0];
        $cacah = $row[1];

        $cacah += 1; // Increment cacah
        $nokas = $awalan . rpad($cacah, "0", 6); // Form nomor kas

        $sql = "SELECT SUM(kredit - debet)
                  FROM jbsfina.tabunganp
                 WHERE nip = '$nip'
                   AND idtabungan = '$idtabungan'";
        $jsaldo = $db->FetchSingle($sql, 0);

        if ($jumlah > $jsaldo)
            return json_encode([-1, "Jumlah saldo tidak mencukupi untuk penarikan"]);

        $ttarik = date("Y-m-d");
        $idpetugas = getIdUser();
        $petugas = getUserName();
        $keterangan = "Penarikan Tabungan $namatabungan pegawai $namapegawai ($nip)";
        $ketsms = "Penarikan Tabungan $namatabungan";

        $db->BeginTrans();

        $idjurnal = SimpanJurnal2($db, $idtahunbuku, $ttarik, $keterangan, $nokas, "", $idpetugas, $petugas, "tarikantabungan");

        $sql = "INSERT INTO jbsfina.tabunganp
                   SET nip='$nip', idtabungan='$idtabungan', debet='$jumlah', kredit='0',
                       keterangan = '$keterangansetor', 
                       petugas='$idpetugas', idjurnal='$idjurnal', tanggal=NOW(),
                       sumberdana = NULL, lokasidana = $lokasiDanaValue";
        $db->QueryDb($sql);

        SimpanDetailJurnal2($db, $idjurnal, "K", $rekkas, $jumlah);
        SimpanDetailJurnal2($db, $idjurnal, "D", $rekutang, $jumlah);

        $sql = "UPDATE jbsfina.tahunbuku 
                   SET cacah = cacah + 1 
                 WHERE replid = $idtahunbuku";
        $db->QueryDb($sql);

        if ($sendnotif == 1)
        {
            $jsaldo = $jsaldo - $jumlah;

            CreateSMSTabungan2($db, 'PEGTAB',
                $departemen, $nip, $namapegawai,
                RegularDateFormat($ttarik),
                FormatRupiah($jumlah),
                FormatRupiah($jsaldo),
                $ketsms,
                $keterangansetor);
        }

        $db->CommitTrans();

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        $db->RollbackTrans();
        $db->LogLastErrorIfExist();

        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k5m8t") ]);
    }
    finally
    {
        $db->Close();
    }

}

function ShowInfoTabunganPegawai($db)
{
    global $nip, $idtabungan;

    $sql = "SELECT SUM(debet), SUM(kredit)
              FROM jbsfina.tabunganp
             WHERE nip = '$nip'
               AND idtabungan = '$idtabungan'";
    $res = $db->QueryDb($sql);
    $row = mysqli_fetch_row($res);
    $jsetor = $row[1];
    $jtarik = $row[0];
    $jsaldo = $jsetor - $jtarik;

    $setorakhir = 0;
    $tglsetorakhir = "-";
    $sql = "SELECT DATE_FORMAT(tanggal, '%d-%b-%Y %H:%i:%s'), kredit
              FROM jbsfina.tabunganp
             WHERE nip = '$nip'
               AND idtabungan = '$idtabungan'
               AND kredit <> 0
             ORDER BY replid DESC
             LIMIT 1";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        $tglsetorakhir = $row[0];
        $setorakhir = $row[1];
    }

    $tarikakhir = 0;
    $tgltarikakhir = "-";
    $sql = "SELECT DATE_FORMAT(tanggal, '%d-%b-%Y %H:%i:%s'), debet
              FROM jbsfina.tabunganp
             WHERE nip = '$nip'
               AND idtabungan = '$idtabungan'
               AND debet <> 0
             ORDER BY replid DESC
             LIMIT 1";
    $res = $db->QueryDb($sql);
    if ($row = mysqli_fetch_row($res))
    {
        $tgltarikakhir = $row[0];
        $tarikakhir = $row[1];
    }

    echo "<div id='dvTabInfoTabungan'>";
    echo "<table id='tabInfoTabungan' class='rounded-box' width='100%' cellpadding='5'>";
    echo "<tr>";
    echo "<td style='width: 180px' valign='top'>";
    echo "<span style='color: #999'>Saldo</span><br>";
    echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($jsaldo) . " </span>&nbsp;&nbsp;";
    echo "</td>";
    echo "<td style='width: 180px' valign='top'>";
    echo "<span style='color: #999'>Jumlah Setoran</span><br>";
    echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($jsetor) . " </span>&nbsp;&nbsp;";
    echo "</td>";
    echo "<td style='width: 180px' valign='top'>";
    echo "<span style='color: #999'>Setoran Terakhir</span><br>";
    echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($setorakhir) . " </span><br>";
    echo "<span style='color: #999; font-size: 12px'>" . $tglsetorakhir . " </span><br>";
    echo "</td>";
    echo "<td style='width: 180px' valign='top'>";
    echo "<span style='color: #999'>Jumlah Penarikan</span><br>";
    echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($jtarik) . " </span>&nbsp;&nbsp;";
    echo "</td>";
    echo "<td style='width: 180px' valign='top'>";
    echo "<span style='color: #999'>Penarikan Terakhir</span><br>";
    echo "<span style='color: #333; font-size: 18px'>" . FormatRupiah($tarikakhir) . " </span><br>";
    echo "<span style='color: #999; font-size: 12px'>" . $tgltarikakhir . " </span><br>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";
}

function ShowTransaksiTabunganPegawai($db, $showMenu = true)
{
    global $nRowPerPage, $totalPage, $nData;
    global $nip, $idtabungan, $idtahunbuku, $page;

    echo "<table class='tab' id='tabTabunganList' border='1' style='border-collapse:collapse' width='100%'>";
    echo "<tr style='height: 30px' align='center'>";
    echo "<td class='header' width='5%'>No</td>";
    echo "<td class='header' width='18%'>No. Jurnal/Tgl</td>";
    echo "<td class='header' width='15%'>Debet</td>";
    echo "<td class='header' width='15%'>Kredit</td>";
    echo "<td class='header' width='*'>Keterangan</td>";
    echo "<td class='header' width='12%'>Petugas</td>";
    if ($showMenu)
        echo "<td class='header hide-in-report'>&nbsp;</td>";
    echo "</tr>";

    $sql = "SELECT COUNT(p.replid)
              FROM jbsfina.tabunganp p, jbsfina.jurnal j
             WHERE p.idjurnal = j.replid
               AND p.nip = '$nip'
               AND j.idtahunbuku = '$idtahunbuku'
               AND p.idtabungan = '$idtabungan'";
    $nData = $db->FetchSingle($sql, 0);
    if ($nData == 0)
    {
        echo "<tr height='100'><td colspan='7' align='center' valign='middle'><i>Belum ada data tabungan</i></td></tr>";
        echo "</table>";
        echo "<input type='hidden' id='totalpage' value='0'>";
        echo "<input type='hidden' id='ndata' value='0'>";

        return;
    }

    $totalPage = ceil($nData / $nRowPerPage);
    $startIndex = ($page - 1) * $nRowPerPage;

    $sql = "SELECT p.replid AS id, j.nokas, date_format(p.tanggal, '%d-%b-%Y %H:%i:%s') as tanggal,
                   p.keterangan, p.debet, p.kredit, p.petugas, p.idtabungan AS iddatatabungan,
                   IFNULL(p.sumberdana, '-') AS fsumberdana,
                   IFNULL(p.lokasidana, '-') AS flokasidana
              FROM jbsfina.tabunganp p, jbsfina.jurnal j
             WHERE p.idjurnal = j.replid
               AND p.nip = '$nip'
               AND j.idtahunbuku = '$idtahunbuku'
               AND p.idtabungan = '$idtabungan'
             ORDER BY p.replid DESC
             LIMIT $startIndex, $nRowPerPage";
    $result = $db->QueryDb($sql);
    $cnt = $startIndex;
    while ($row = mysqli_fetch_array($result))
    {
        $kredit = (int)$row['kredit'];
        $bgcolor = $kredit != 0 ? "#E0F3FF" : "#F9F6EA";

        $no = $nData - $cnt;
        $cnt += 1;

        $idTabungan = $row['id'];
        $idDataTabungan = $row['iddatatabungan'];
        $sql = "SELECT jenistrans
                  FROM jbsfina.paymenttrans
                 WHERE idtabunganp = $idTabungan";
        $res2 = $db->QueryDb($sql);

        $isSchoolPay = mysqli_num_rows($res2) > 0;
        $infoSchoolPay = "";
        if ($row2 = mysqli_fetch_row($res2))
        {
            $jenisTrans = $row2[0];
            if ($jenisTrans == 0)
                $jenisTrans = "&nbsp;SchoolPay&nbsp;<span style='background-color: #636363; color: #ffffff'>&nbsp;Vendor&nbsp;</span>";
            else if ($jenisTrans == 1)
                $jenisTrans = "&nbsp;SchoolPay&nbsp;<span style='background-color: #47973c; color: #ffffff'>&nbsp;Iuran&nbsp;</span>";
            else if ($jenisTrans == 2)
                $jenisTrans = "&nbsp;SchoolPay&nbsp;<span style='background-color: #9f4aa3; color: #ffffff'>&nbsp;Iuran&nbsp;</span>";

            $infoSchoolPay = $isSchoolPay ? "<span style='background-color: #296eeb; color: #ffffff; font-size: 10px;'>$jenisTrans</span>" : "";
        }

        if ($infoSchoolPay == "")
        {
            $sql = "SELECT COUNT(replid)
                      FROM jbsfina.pgtransdata
                     WHERE idtabunganp = $idDataTabungan";
            $res2 = $db->QueryDb($sql);
            $row2 = mysqli_fetch_row($res2);
            if ($row2[0] > 0)
            {
                $infoSchoolPay = "<span style='background-color: #43b9c9; color: #ffffff; font-size: 10px;'>&nbsp;OnlinePay&nbsp;</span>";
            }
        }

        $action = "setor";
        if ($row["debet"] > 0 && $row["kredit"] == 0)
            $action = "tarik";

        $keterangan = "";
        if ($row["fsumberdana"] != "-")
            $keterangan .= "<b>Sumber Dana</b>: $row[fsumberdana]<br>";
        if ($row["flokasidana"] != "-")
        {
            if ($action == 'setor')
                $keterangan .= "<b>Penyimpanan</b>: $row[flokasidana]<br>";
            else
                $keterangan .= "<b>Pengambilan</b>: $row[flokasidana]<br>";
        }

        $keterangan .= "<i>$row[keterangan]</i>";

        echo "<tr height='25' style='background-color: $bgcolor;'>";
        echo "<td align='center' class='numberColumn'>$no</td>";
        echo "<td align='center'><strong>$row[nokas]</strong><br><i>$row[tanggal]</i></td>";
        echo "<td align='right'>" . FormatRupiah($row['debet']) . "</td>";
        echo "<td align='right'>" . FormatRupiah($row['kredit']) . "</td>";
        echo "<td align='left'>$keterangan</td>";
        echo "<td align='center'>$row[petugas]</td>";

        if ($showMenu)
        {
            echo "<td align='center'class='hide-in-report'>";
            echo "<a href='#' onclick='cetakkuitansi($row[id])'><img src='../images/ico/print.png' border='0' title='cetak kuitansi'></a>&nbsp;";
            if (!$isSchoolPay && getLevel() != 2)
            {
                echo "<a href='#' onclick='editpembayaran($row[id])'><img src='../images/ico/ubah.png' border='0' title='ubah transaksi tabungan'></a>";
            };
            echo "<br>$infoSchoolPay";
            echo "</td>";
        }

        echo "</tr>";
    }
    echo "</table>";
    echo "<input type='hidden' id='totalpage' value='$totalPage'>";
    echo "<input type='hidden' id='ndata' value='$nData'>";
}

function ShowPageControl()
{
    global $page, $totalPage, $nData;

    echo "Halaman&nbsp;&nbsp;";
    echo "<input type='button' class='but' style='height: 28px; width: 28px' value=' < ' onclick='onPrevPageClick()'>";
    echo "<select id='page' class='inputbox' style='width: 50px' onchange='onChangePage()'>";
    for ($i = 1; $i <= $totalPage; $i++)
    {
        $sel = $i == $page ? "selected" : "";
        echo "<option value='$i' $sel>$i</option>";
    }
    echo "</select>";
    echo "<input type='button' class='but' style='height: 28px; width: 28px' value=' > ' onclick='onNextPageClick()'>";
    echo "&nbsp;dari $totalPage, jumlah $nData data";
}

function ShowSelectLokasiPengambilanTabungan()
{
    $db = new Db();
    try
    {
        $db->Open();

        $nip = $_REQUEST["nip"];
        $idTabungan = $_REQUEST["idtabungan"];

        $sql = "SELECT kode, nama
                  FROM jbsfina.lokasidana
                 ORDER BY urutan";
        $res = $db->QueryDb($sql);
        $lsLokasi = [];
        while($row = mysqli_fetch_row($res))
        {
            $lsLokasi[$row[0]] = $row[1];
        }

        $sql = "SELECT lokasidana, SUM(kredit - debet)
                  FROM jbsfina.tabunganp
                 WHERE idtabungan = $idTabungan
                   AND nip = '$nip'
                 GROUP BY lokasidana";
        $res = $db->QueryDb($sql);

        echo "<select id='lokasidanatarik' class='inputbox' style='width:210px'>";
        $isSel = false;
        while($row = mysqli_fetch_row($res))
        {
            $jumlah = FormatRupiah($row[1]);

            if (is_null($row[0]))
            {
                $kode = "***";
                $nama = "(tidak ada data)";
            }
            else
            {
                $kode = $row[0];
                $nama = $lsLokasi[$kode];
            }

            $lsInfo = [$kode, $row[1]];
            $jsonInfo64 = base64_encode(json_encode($lsInfo));

            $sel = "";
            if (!$isSel)
            {
                $sel = "selected";
                $isSel = true;
            }

            echo "<option value='$jsonInfo64' $sel>$nama - $jumlah</option>";
        }
        echo "</select>";
        echo "&nbsp;<img src='../images/ico/refresh.png' style='cursor: pointer' title='refresh' onclick='fetchLokasiPengambilan()'>";

    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kcm42");
    }
    finally
    {
        $db->Close();
    }
}

function ShowSelectLokasiDanaTabunganPegawai($db, $idselect)
{
    global $lokasidana;

    try
    {
        $sql = "SELECT kode, nama
                  FROM jbsfina.lokasidana
                 WHERE aktif = 1
                 ORDER BY urutan";
        $res = $db->QueryDb($sql);

        echo "<select id='$idselect' class='inputbox' style='width:210px'>";
        $sel = $lokasidana == "***" ? "selected" : "";
        echo "<option value='***' $sel>(tidak ada data)</option>";
        while($row = mysqli_fetch_row($res))
        {
            $sel = $lokasidana == $row[0] ? "selected" : "";
            echo "<option value='$row[0]' $sel>$row[0] - $row[1]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kg3zc");
    }
}

function ShowSelectSumberDanaTabunganPegawai($db)
{
    global $sumberdana;

    try
    {
        $sql = "SELECT kode, nama
                  FROM jbsfina.sumberdana
                 WHERE aktif = 1
                 ORDER BY urutan";
        $res = $db->QueryDb($sql);

        echo "<select id='sumberdana' class='inputbox' style='width:210px'>";
        $sel = $sumberdana == "***" ? "selected" : "";
        echo "<option value='***' $sel>(tidak ada data)</option>";
        while($row = mysqli_fetch_row($res))
        {
            $sel = $sumberdana == $row[0] ? "selected" : "";
            echo "<option value='$row[0]' $sel>$row[0] - $row[1]</option>";
        }
        echo "</select>";
    }
    catch (Exception $ex)
    {
        echo Msg::InfoError($ex->getMessage(), "kp1rd");
    }
}
?>