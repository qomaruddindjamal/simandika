var helpBox = null;

$(document).ready(function ()
{
    if ($("#table").length)
        Tables('table', 1, 0);

    if ($("#dvTableContent").length)
        $("#dvTableContent").hide().fadeIn(300);

    helpBox = new DialogBox("#divHelpDialog", 620, 520);
});

function showHelp()
{
    $.ajax({
        url: "../help/pn_jenispenerimaan.html?" + Rnd.String(),
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

function onChangeKategori()
{
    refresh();
}

function onChangeDept()
{
    refresh();
}

function refresh()
{
    let qsb = new QsBuilder();
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("departemen", "departemen");

    document.location.href = "jenispenerimaan2.php?" + qsb.createQs();
}

function set_aktif(id)
{
    let dataaktif = parseInt($("#dataaktif-" + id).val());
    let newaktif = dataaktif === 1 ? 0 : 1;

    let msg;
    if (newaktif === 0)
        msg = "NON AKTIF kan jenis penerimaan ini?";
    else
        msg = "Aktifkan kembali jenis penerimaan ini?";

    if (!confirm(msg))
        return;

    let qsb = new QsBuilder();
    qsb.add("op", "changeaktif");
    qsb.add("id", id);
    qsb.add("newaktif", newaktif);

    $.ajax({
        url: "jenispenerimaan2.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            var ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                alert(ls[1]);
                return;
            }

            var src = "../images/ico/aktif.png";
            if (newaktif === 0)
                src = "../images/ico/nonaktif.png";

            $("#imgaktif-" + id).attr('src', src);
            $("#dataaktif-" + id).val(newaktif);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}

function hapus(id)
{
    if (!confirm("Hapus jenis penerimaan ini?"))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "hapus");
    qsb.add("id", id);

    $.ajax({
        url: "jenispenerimaan2.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            var ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                alert(ls[1]);
                return;
            }

            refresh();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    let addr = "jenispenerimaan2.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakJenisPenerimaan','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function tambah()
{
    let qsb = new QsBuilder();
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("departemen", "departemen");
    qsb.add("kategori", $("#idkategori option:selected").text());
    qsb.add("idjenis", 0);

    let url = "jenispenerimaan2.dialog.php?" + qsb.createQs();
    newWindow(url, "TambahJenisPenerimaan", 500, 500);
}

function ubah(idJenis)
{
    let qsb = new QsBuilder();
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("departemen", "departemen");
    qsb.add("kategori", $("#idkategori option:selected").text());
    qsb.add("idjenis", idJenis);

    let url = "jenispenerimaan2.dialog.php?" + qsb.createQs();
    newWindow(url, "UbahJenisPenerimaan", 500, 500);
}

function getPageContent(section)
{
    if (section === "departemen")
    {
        return $("#departemen option:selected").text();
    }
    else if (section === "kategori")
    {
        return $("#idkategori option:selected").text();
    }
    else if (section === "table")
    {
        if ($("#dvTableContent").length)
            return $("#dvTableContent").html();
        return "-";
    }
}