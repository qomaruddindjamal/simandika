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
require_once ("tabs.siswa.func.php");
?>
<script language="JavaScript">
    var tabsiswa_acceptResult = null;

    $(document).ready(function ()
    {
        if ($("#tabsiswa_table_pilih").length)
            Tables("tabsiswa_table_pilih", 1, 0);
    });

    function tabsiswa_pilihSiswa (kelompok, json64)
    {
        if (tabsiswa_acceptResult === null)
            return;

        tabsiswa_acceptResult(kelompok, json64);
    }

    function tabsiswa_setAcceptResult(acceptResult)
    {
        tabsiswa_acceptResult = acceptResult;
    }

    function tabsiswa_onTingkatChange()
    {
        var qsb = new QsBuilder();
        qsb.add("op", "kelas");
        qsb.add("idtingkat", $("#tabsiswa_tingkat").val());

        $("#tabsiswa_dvKelas").html("memuat ..");
        $("#tabsiswa_dvDaftar").html("memuat ..");

        var relPath = $("#tab_relPath").val();
        $.ajax({
            url: relPath + "tabs.siswa.ajax.php",
            method: "POST",
            data: qsb.createQs(),
            success: function (result)
            {
                $("#tabsiswa_dvKelas").html(result);

                tabsiswa_reloadDaftarSiswa("s.nama");
            },
            error: function (xhr)
            {
                alert(xhr.responseText);
            }
        })
    }

    function tabsiswa_onKelasChange()
    {
        tabsiswa_reloadDaftarSiswa("s.nama");
    }

    function tabsiswa_reloadDaftarSiswa(urut)
    {
        if ($("#tabsiswa_tingkat").length === 0)
        {
            $("#tabsiswa_dvDaftar").html("<i>belum tersedia data Tingkat</i>");
            return;
        }

        if ($("#tabsiswa_kelas").length === 0)
        {
            $("#tabsiswa_dvDaftar").html("<i>belum tersedia data Kelas</i>");
            return;
        }

        var qsb = new QsBuilder();
        qsb.add("op", "daftar");
        qsb.add("departemen", $("#tabsiswa_departemen_pilih").val());
        qsb.add("idtingkat", $("#tabsiswa_tingkat").val());
        qsb.add("tingkat", $("#tabsiswa_tingkat option:selected").text());
        qsb.add("idkelas", $("#tabsiswa_kelas").val());
        qsb.add("kelas", $("#tabsiswa_kelas option:selected").text());
        qsb.add("urut", urut);

        tabsiswa_fetchDaftarSiswa(qsb.createQs());
    }

    function tabsiswa_fetchDaftarSiswa(qs)
    {
        $("#tabsiswa_dvDaftar").html("memuat ..");

        var relPath = $("#tab_relPath").val();
        $.ajax({
            url: relPath + "tabs.siswa.ajax.php",
            method: "POST",
            data: qs,
            success: function (result)
            {
                let newData = $(result).hide();
                $("#tabsiswa_dvDaftar").html(newData);
                newData.fadeIn(500);

                if ($("#tabsiswa_table_pilih").length)
                    Tables("tabsiswa_table_pilih", 1, 0);
            },
            error: function (xhr)
            {
                alert(xhr.responseText);
            }
        })
    }

    function tabsiswa_cari(e)
    {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode !== 13)
            return;

        var search = $.trim($("#tabsiswa_search").val());
        if (search.length < 3)
        {
            $("#tabsiswa_dvCari").html("<br><br>Panjang kata kunci minimal 3 karakter");
            return;
        }

        tabsiswa_reloadCariSiswa(search, "s.nama");
    }

    function tabsiswa_reloadCariSiswa(search, urut)
    {
        var qsb = new QsBuilder();
        qsb.add("op", "cari");
        qsb.add("search", search);
        qsb.add("urut", urut);
        qsb.addInput("searchby", "tabsiswa_searchby");
        qsb.addInput("departemen", "tabsiswa_departemen_cari");

        tabsiswa_fetchCariSiswa(qsb.createQs());
    }

    function tabsiswa_fetchCariSiswa(qs)
    {
        $("#tabsiswa_dvCari").html("memuat ..");

        var relPath = $("#tab_relPath").val();
        $.ajax({
            url: relPath + "tabs.siswa.ajax.php",
            method: "POST",
            data: qs,
            success: function (result)
            {
                $("#tabsiswa_dvCari").html(result);

                if ($("#tabsiswa_table_cari").length)
                    Tables("tabsiswa_table_cari", 1, 0);
            },
            error: function (xhr)
            {
                alert(xhr.responseText);
            }
        })
    }

    function tabsiswa_changeUrut(sumber, urut)
    {
        if (sumber === "daftar")
        {
            tabsiswa_reloadDaftarSiswa(urut);
        }
        else
        {
            var search = $.trim($("#tabsiswa_search").val());
            tabsiswa_reloadCariSiswa(search, urut);
        }
    }
</script>
<input type="hidden" id="tabsiswa_departemen" value="<?= $departemen ?>">
<input type="hidden" id="tab_relPath" value="<?=$tab_relPath?>">
<div id="tabSiswa">
    <ul>
        <li><a href="#tabs-1">Pilih Siswa</a></li>
        <li><a href="#tabs-2">Cari Siswa</a></li>
    </ul>
    <div id="tabs-1" style="padding: 2px">

        <table border="0" cellpadding="0" width="100%">
        <tr>
            <td width="25%">Departemen</td>
            <td width="*">
                <input type="text" class="inputbox" readonly
                       style="background-color: #efefef; width: 170px;"
                       id="tabsiswa_departemen_pilih" value="<?=$departemen?>">
            </td>
        </tr>
        <tr>
            <td>Tingkat</td>
            <td>
<?php
                $idTingkat = "";
                $tingkat = "";
                ShowSelectTingkatSiswa();
?>
            </td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>
                <div id="tabsiswa_dvKelas">
<?php
                $idKelas = "";
                $kelas = "";
                ShowSelectKelasSiswa();
?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">

            <div id="tabsiswa_dvDaftar">
<?php
            $urut = "s.nama";
            ShowDaftarSiswa();
?>
            </div>

            </td>
        </tr>
        </table>

    </div>
    <div id="tabs-2" style="padding: 2px">

        <table border="0" cellpadding="0" width="100%">
        <tr>
            <td width="25%">Departemen</td>
            <td width="*">
                <input type="text" class="inputbox"
                       style="background-color: #efefef; width: 170px;" readonly
                       id="tabsiswa_departemen_cari" value="<?=$departemen?>">
            </td>
        </tr>
        <tr>
            <td>Berdasarkan</td>
            <td>
                <select id="tabsiswa_searchby" class="inputbox" style="width: 100px">
                    <option value="nama">Nama</option>
                    <option value="nis">NIS</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Pencarian</td>
            <td>
                <input type="text" class="inputbox"
                       style="width: 170px;"
                       id="tabsiswa_search"
                       onkeyup="return tabsiswa_cari(event)">
            </td>
        </tr>
        <tr>
            <td colspan="2">

                <div id="tabsiswa_dvCari">

                </div>

            </td>
        </tr>
        </table>

    </div>
</div>