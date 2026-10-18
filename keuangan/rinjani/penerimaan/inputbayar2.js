var helpBox = null;

$(document).ready(function ()
{
    helpBox = new DialogBox("#divHelpDialog", 620, 520);
});

function showHelp()
{
    $.ajax({
        url: "../help/pn_besarpembayaran.html?" + Rnd.String(),
        success: function (content)
        {
            helpBox.show(content);

            setTimeout(function () {
                $("#divHelpDialog").scrollTop(0);
            }, 750)
        },
        error: function(xhr)
        {
            alert(xhr.responseText);
        }
    })
}


change_kategori = function ()
{
    let dept = $("#departemen").val();
    let idkategori = $("#idkategori").val();

    if (idkategori === "JTT")
    {
        $("#lbTingkat").html("Tingkat");
        $("#lbKelas").html("Kelas");
    }
    else if (idkategori === "CSWJB")
    {
        $("#lbTingkat").html("Proses Penerimaan");
        $("#lbKelas").html("Kelompok");
    }

    $("#divPenerimaan").html("memuat..");
    $("#divTingkat").html("memuat..");
    $("#divKelas").html("memuat..");

    $.ajax({
        url: "inputbayar2.ajax.php",
        data: "op=getpenerimaan&departemen=" + dept + "&idkategori=" + idkategori,
        success: function (html)
        {
            $("#divPenerimaan").html(html);

            $.ajax({
                url: "inputbayar2.ajax.php",
                data: "op=gettingkat&departemen=" + dept + "&idkategori=" + idkategori,
                success: function (html)
                {
                    $("#divTingkat").html(html);

                    if ($("#divTingkat").length === 0)
                        return;

                    let idtingkat = $("#divTingkat option:selected").val();
                    $.ajax({
                        url: "inputbayar2.ajax.php",
                        data: "op=getkelas&departemen=" + dept + "&idtingkat=" + idtingkat + "&idkategori=" + idkategori,
                        success: function (html)
                        {
                            $("#divKelas").html(html);
                        },
                        error: function (xhr)
                        {
                            alert(xhr.responseText);
                        }
                    })
                },
                error: function (xhr)
                {
                    alert(xhr.responseText);
                }
            })
        },
        error: function (xhr) {
            alert(xhr.responseText);
        }
    });
};

change_tingkat = function()
{
    let dept = $("#departemen").val();
    let idtingkat = $("#tingkat").val();
    let idkategori = $("#idkategori").val();

    $("#divKelas").html("memuat..");

    $.ajax({
        url: "inputbayar2.ajax.php",
        data: "op=getkelas&departemen=" + dept + "&idtingkat=" + idtingkat + "&idkategori=" + idkategori,
        success: function (html)
        {
            $("#divKelas").html(html);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
};

simpan = function ()
{
    if ($("#penerimaan option").length === 0)
    {
        alert("Belum ada data penerimaan");
        return;
    }

    let besar = rupiahToNumber($("#besar").val());
    if ($.trim(besar).length === 0)
    {
        alert("Besar pembayaran belum ditentukan!");
        $("#besar").focus();
        return;
    }
    else if (besar <= 0)
    {
        alert("Besar pembayaran harus positif!");
        $("#besar").focus();
        return;
    }
    else if (isNaN(besar))
    {
        alert("Besar pembayaran harus bilangan!");
        $("#besar").focus();
        return;
    }

    let cicilan = rupiahToNumber($("#cicilan").val());
    if ($.trim(cicilan).length === 0)
    {
        alert("Cicilan pembayaran belum ditentukan!");
        $("#cicilan").focus();
        return;
    }
    else if (cicilan <= 0)
    {
        alert("Cicilan pembayaran harus positif!");
        $("#cicilan").focus();
        return;
    }
    else if (isNaN(cicilan))
    {
        alert("Cicilan pembayaran harus bilangan!");
        $("#cicilan").focus();
        return;
    }

    if ($("#tingkat option").length === 0)
    {
        alert("Tidak ditemukan data tingkat/penerimaan!");
        return;
    }

    if ($("#nkelas").length === 0)
    {
        alert("Tidak ditemukan data kelas/kelompok!");
        return;
    }

    let nchecked = 0;
    let idkelas = "";
    let nkelas = $("#nkelas").val();
    for(let i = 1; i <= nkelas; i++)
    {
        if ($('#ch' + i).is(":checked"))
        {
            nchecked += 1;

            if (idkelas !== "") idkelas += ",";
            idkelas += $("#id" + i).val();
        }
    }

    if (nchecked === 0)
    {
        alert("Data kelas/kelompok belum dipilih!");
        return;
    }

    if (!confirm("Data sudah benar?"))
        return;

    let dept = $("#departemen").val();
    let idkategori = $("#idkategori").val();
    let idpenerimaan = $("#penerimaan").val();
    let idtingkat = $("#tingkat").val();
    let cicilanpertama = $("#cicilanpertama").is(":checked") ? 1 : 0;

    let data = "op=setbayar&departemen=" + dept;
    data += "&idkategori=" + idkategori;
    data += "&idpenerimaan=" + idpenerimaan;
    data += "&idtingkat=" + idtingkat;
    data += "&idkelas=" + idkelas;
    data += "&besar=" + besar;
    data += "&cicilan=" + cicilan;
    data += "&cicilanpertama=" + cicilanpertama;

    $.ajax({
        url: "inputbayar2.ajax.php",
        data: data,
        success: function (json)
        {
            $("#besar").val("");
            $("#cicilan").val("");
            $("#cicilanpertama").prop("checked", false);
            for(let i = 1; i <= nkelas; i++)
            {
                $('#ch' + i).prop("checked", false);
            }
            
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                showToast(ls[1], 3000, "error", "bottom");
                alert(ls[1]);
                return;
            }

            showToast(ls[1], 3000, "success", "bottom");
        },
        error: function (xhr) {
            alert(xhr.responseText);
        }
    });

};


























