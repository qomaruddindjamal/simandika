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
require_once ("tabs.calonsiswa.func.php");
?>
<script language="JavaScript">
    var tabcsiswa_acceptResult = null;

    $(document).ready(function ()
    {
        if ($("#tabcsiswa_table_pilih").length)
            Tables("tabcsiswa_table_pilih", 1, 0);
    });

    function tabcsiswa_pilihCalonSiswa (nis, nama)
    {
        if (tabcsiswa_acceptResult === null)
            return;

        tabcsiswa_acceptResult(nis, nama);
    }

    function tabcsiswa_setAcceptResult(acceptResult)
    {
        tabcsiswa_acceptResult = acceptResult;
    }

    function tabcsiswa_onProsesChange()
    {
        var qsb = new QsBuilder();
        qsb.add("op", "kelompok");
        qsb.add("idproses", $("#tabcsiswa_proses").val());

        $("#tabcsiswa_dvKelompok").html("memuat ..");
        $("#tabcsiswa_dvDaftar").html("memuat ..");

        var relPath = $("#tab_relPath").val();
        $.ajax({
            url: relPath + "tabs.calonsiswa.ajax.php",
            method: "POST",
            data: qsb.createQs(),
            success: function (result)
            {
                $("#tabcsiswa_dvKelompok").html(result);

                tabcsiswa_reloadDaftarCalonSiswa();
            },
            error: function (xhr)
            {
                alert(xhr.responseText);
            }
        })
    }

    function tabcsiswa_reloadDaftarCalonSiswa()
    {
        if ($("#tabcsiswa_proses").length === 0)
        {
            $("#tabcsiswa_dvDaftar").html("<i>belum tersedia data Proses Penerimaan</i>");
            return;
        }

        if ($("#tabcsiswa_kelompok").length === 0)
        {
            $("#tabcsiswa_kelompok").html("<i>belum tersedia data Kelompok Penerimaan</i>");
            return;
        }

        var qsb = new QsBuilder();
        qsb.add("op", "daftar");
        qsb.add("departemen", $("#tabcsiswa_departemen_pilih").val());
        qsb.add("idproses",  $("#tabcsiswa_proses").val());
        qsb.add("proses",  $("#tabcsiswa_proses option:selected").text());
        qsb.add("idkelompok",  $("#tabcsiswa_kelompok").val());
        qsb.add("kelompok",  $("#tabcsiswa_kelompok option:selected").text());
        qsb.add("urut", "cs.nama");

        tabcsiswa_fetchDaftarCalonSiswa(qsb.createQs());
    }

    function tabcsiswa_onKelompokChange()
    {
        tabcsiswa_reloadDaftarCalonSiswa();
    }

    function tabcsiswa_fetchDaftarCalonSiswa(qs)
    {
        $("#tabcsiswa_dvDaftar").html("memuat ..");

        var relPath = $("#tab_relPath").val();
        $.ajax({
            url: relPath + "tabs.calonsiswa.ajax.php",
            method: "POST",
            data: qs,
            success: function (result)
            {
                let newData = $(result).hide();
                $("#tabcsiswa_dvDaftar").html(newData);
                newData.fadeIn(500);

                if ($("#tabcsiswa_table_pilih").length)
                    Tables("tabcsiswa_table_pilih", 1, 0);
            },
            error: function (xhr)
            {
                alert(xhr.responseText);
            }
        })
    }

    function tabcsiswa_cari(e)
    {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode !== 13)
            return;

        var search = $.trim($("#tabcsiswa_search").val());
        if (search.length < 3)
        {
            $("#tabcsiswa_dvCari").html("<br><br>Panjang kata kunci minimal 3 karakter");
            return;
        }

        var qsb = new QsBuilder();
        qsb.add("op", "cari");
        qsb.add("search", search);
        qsb.add("urut", "cs.nama");
        qsb.addInput("searchby", "tabcsiswa_searchby");
        qsb.addInput("departemen", "tabcsiswa_departemen_cari");

        tabcsiswa_showCariCalonSiswa(qsb.createQs());
    }

    function tabcsiswa_showCariCalonSiswa(qs)
    {
        $("#tabcsiswa_dvCari").html("memuat ..");

        var relPath = $("#tab_relPath").val();
        $.ajax({
            url: relPath + "tabs.calonsiswa.ajax.php",
            method: "POST",
            data: qs,
            success: function (result)
            {
                $("#tabcsiswa_dvCari").html(result);

                if ($("#tabcsiswa_table_cari").length)
                    Tables("tabcsiswa_table_cari", 1, 0);
            },
            error: function (xhr)
            {
                alert(xhr.responseText);
            }
        })
    }

    function tabcsiswa_changeUrut(sumber, urut)
    {
        if (sumber === "daftar")
        {
            tabcsiswa_reloadDaftarCalonSiswa();
        }
        else
        {
            var search = $.trim($("#tabcsiswa_search").val());

            var qsb = new QsBuilder();
            qsb.add("op", "cari");
            qsb.add("search", search);
            qsb.add("urut", urut);
            qsb.addInput("searchby", "tabcsiswa_searchby");
            qsb.addInput("departemen", "tabcsiswa_departemen_cari");

            tabcsiswa_showCariCalonSiswa(qsb.createQs());
        }
    }
</script>
<input type="hidden" id="tab_relPath" value="<?=$tab_relPath?>">
<div id="tabCalonSiswa">
    <ul>
        <li><a href="#tabs-1">Pilih Calon Siswa</a></li>
        <li><a href="#tabs-2">Cari Calon Siswa</a></li>
    </ul>
    <div id="tabs-1" style="padding: 2px">

        <table border="0" cellpadding="0" width="100%">
            <tr>
                <td width="25%">Departemen</td>
                <td width="*">
                    <input type="text" class="inputbox" readonly
                           style="background-color: #efefef; width: 170px;"
                           id="tabcsiswa_departemen_pilih" value="<?=$departemen?>">
                </td>
            </tr>
            <tr>
                <td>Proses</td>
                <td>
<?php
                    $idProses = "";
                    $proses = "";
                    ShowSelectProsesCalonSiswa($departemen);
?>
                </td>
            </tr>
            <tr>
                <td>Kelompok</td>
                <td>
                    <div id="tabcsiswa_dvKelompok">
<?php
                        $idKelompok = "";
                        $kelompok = "";
                        ShowSelectKelompokCalonSiswa();
?>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">

                    <div id="tabcsiswa_dvDaftar">
<?php
                        $urut = "cs.nama";
                        ShowDaftarCalonSiswa();
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
                           id="tabcsiswa_departemen_cari" value="<?=$departemen?>">
                </td>
            </tr>
            <tr>
                <td>Berdasarkan</td>
                <td>
                    <select id="tabcsiswa_searchby" class="inputbox" style="width: 100px">
                        <option value="nama">Nama</option>
                        <option value="nop">No Pendaftaran</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Pencarian</td>
                <td>
                    <input type="text" class="inputbox"
                           style="width: 170px;"
                           id="tabcsiswa_search"
                           onkeyup="return tabcsiswa_cari(event)">
                </td>
            </tr>
            <tr>
                <td colspan="2">

                    <div id="tabcsiswa_dvCari">

                    </div>

                </td>
            </tr>
        </table>

    </div>
</div>