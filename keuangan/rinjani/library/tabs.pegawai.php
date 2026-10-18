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
require_once ("tabs.pegawai.func.php");
?>
<script language="JavaScript">
    var tabpegawai_acceptResult = null;

    $(document).ready(function ()
    {
        if ($("#tabpegawai_table_pilih").length)
            Tables("tabpegawai_table_pilih", 1, 0);
    });

    function tabpegawai_pilihPegawai (kelompok, json64)
    {
        if (tabpegawai_acceptResult === null)
            return;

        tabpegawai_acceptResult(kelompok, json64);
    }

    function tabpegawai_setAcceptResult(acceptResult)
    {
        tabpegawai_acceptResult = acceptResult;
    }

    function tabpegawai_onBagianChange()
    {
        tabpegawai_reloadDaftarPegawai("p.nama");
    }

    function tabpegawai_reloadDaftarPegawai(urut)
    {
        var qsb = new QsBuilder();
        qsb.add("op", "daftar");
        qsb.add("bagian", $("#tabpegawai_bagian_pilih").val());
        qsb.add("urut", urut);

        tabpegawai_fetchDaftarPegawai(qsb.createQs());
    }

    function tabpegawai_fetchDaftarPegawai(qs)
    {
        $("#tabpegawai_dvDaftar").html("memuat ..");

        var relPath = $("#tab_relPath").val();
        $.ajax({
            url: relPath + "tabs.pegawai.ajax.php",
            method: "POST",
            data: qs,
            success: function (result)
            {
                $("#tabpegawai_dvDaftar").html(result).hide().fadeIn(400);

                if ($("#tabpegawai_table_pilih").length)
                    Tables("tabpegawai_table_pilih", 1, 0);
            },
            error: function (xhr)
            {
                alert(xhr.responseText);
            }
        })
    }

    function tabpegawai_cari(e)
    {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode !== 13)
            return;

        var search = $.trim($("#tabpegawai_search").val());
        if (search.length < 3)
        {
            $("#tabpegawai_dvCari").html("<br><br>Panjang kata kunci minimal 3 karakter");
            return;
        }

        tabpegawai_reloadCariPegawai(search, "p.nama");
    }

    function tabpegawai_reloadCariPegawai(search, urut)
    {
        var qsb = new QsBuilder();
        qsb.add("op", "cari");
        qsb.add("search", search);
        qsb.add("urut", urut);
        qsb.addInput("searchby", "tabpegawai_searchby");
        qsb.addInput("departemen", "tabpegawai_departemen_cari");

        tabpegawai_fetchCariPegawai(qsb.createQs());
    }

    function tabpegawai_fetchCariPegawai(qs)
    {
        $("#tabpegawai_dvCari").html("memuat ..");

        var relPath = $("#tab_relPath").val();
        $.ajax({
            url: relPath + "tabs.pegawai.ajax.php",
            method: "POST",
            data: qs,
            success: function (result)
            {
                $("#tabpegawai_dvCari").html(result).hide().fadeIn(300);

                if ($("#tabpegawai_table_cari").length)
                    Tables("tabpegawai_table_cari", 1, 0);
            },
            error: function (xhr)
            {
                alert(xhr.responseText);
            }
        })
    }

    function tabpegawai_changeUrut(sumber, urut)
    {
        if (sumber === "daftar")
        {
            tabpegawai_reloadDaftarPegawai(urut);
        }
        else
        {
            var search = $.trim($("#tabpegawai_search").val());
            tabpegawai_reloadCariPegawai(search, urut);
        }
    }
</script>
<input type="hidden" id="tab_relPath" value="<?=$tab_relPath?>">
<div id="tabPegawai">
    <ul>
        <li><a href="#tabs-1">Pilih Pegawai</a></li>
        <li><a href="#tabs-2">Cari Pegawai</a></li>
    </ul>
    <div id="tabs-1" style="padding: 2px">

        <table border="0" cellpadding="0" width="100%">
        <tr>
            <td width="25%">Bagian</td>
            <td width="*">
                <select id="tabpegawai_bagian_pilih" class="inputbox" style="width: 200px" onchange="tabpegawai_onBagianChange()">
                    <option value="ALL">Semua Bagian</option>
                    <option value="Akademik">Akademik</option>
                    <option value="Non Akademik">Non Akademik</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="2">

                <div id="tabpegawai_dvDaftar">
<?php               $bagian = "ALL";
                    $urut = "p.nama";
                    ShowDaftarPegawai(); ?>
                </div>

            </td>
        </tr>
        </table>

    </div>
    <div id="tabs-2" style="padding: 2px">

        <table border="0" cellpadding="0" width="100%">
        <tr>
            <td>Berdasarkan</td>
            <td>
                <select id="tabpegawai_searchby" class="inputbox" style="width: 100px">
                    <option value="nama">Nama</option>
                    <option value="nip">NIP</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Pencarian</td>
            <td>
                <input type="text" class="inputbox"
                       style="width: 170px;"
                       id="tabpegawai_search"
                       onkeyup="return tabpegawai_cari(event)">
            </td>
        </tr>
        <tr>
            <td colspan="2">

                <div id="tabpegawai_dvCari">

                </div>

            </td>
        </tr>
        </table>

    </div>
</div>