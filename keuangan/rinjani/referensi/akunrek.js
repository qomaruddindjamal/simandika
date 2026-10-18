var dialogBox = null;
var helpBox = null;

$(document).ready(function ()
{
    dialogBox = new DialogBox("#divDialog", 500, 350);
    helpBox = new DialogBox("#divHelpDialog", 620, 520);

    if ($("#table").length)
        Tables('table', 1, 0);

    $("#dvTableContent").hide().fadeIn(400);
});

function showHelp()
{
    $.ajax({
        url: "../help/rf_koderek.html?" + Rnd.String(),
        success: function (content)
        {
            helpBox.show(content);
        },
        error: function(xhr)
        {
            alert(xhr.responseText);
        }
    })
}


showInfoRek = function (kodeRek)
{
    $.ajax({
        url: "akunrek.ajax.php",
        method: "POST",
        data: "op=info&koderek=" + kodeRek,
        success: function (content)
        {
            dialogBox.show(content);
        },
        error: function(xhr)
        {
            alert(xhr.responseText);
        }
    })
};

tambah = function ()
{
    var qsb = new QsBuilder();
    qsb.add("kategori", $("#kategori").val());
    qsb.add("idrekakun", 0);

    newWindow('akunrek.dialog.php?' + qsb.createQs() ,'','550','350','resizable=1,scrollbars=1,status=0,toolbar=0');
};

edit = function (idRekAkun, kode)
{
    var qsb = new QsBuilder();
    qsb.add("kategori", $("#kategori").val());
    qsb.add("idrekakun", idRekAkun);
    qsb.add("kode", kode);

    newWindow('akunrek.dialog.php?' + qsb.createQs() ,'','550','350','resizable=1,scrollbars=1,status=0,toolbar=0');
};

hapus = function (idRekAkun, kode)
{
    if (!confirm("Hapus kode rekening ini?"))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "hapus");
    qsb.add("idrekakun", idRekAkun);
    qsb.add("kode", kode);

    $.ajax({
        url: "akunrek.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            var ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                showToast(ls[1], 3000, "error", "bottom");
                alert(ls[1]);
                return;
            }

            refresh();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
};

afterSave = function ()
{
    refresh();
};

function refresh()
{
    var qsb = new QsBuilder();
    qsb.addInput("kategori", "kategori");
    qsb.addInput("from", "from");
    qsb.addInput("sourcefrom", "sourcefrom");

    document.location.href = "akunrek.php?" + qsb.createQs();
}

function change_kategori()
{
    refresh();
}

function cetak()
{
    var addr = "akunrek.cetak.php?departemen=ALL";
    newWindow(addr, 'CetakRekAkun','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function getPageContent(section)
{
    if (section === "kategori")
    {
        return $("#kategori option:selected").text();
    }
    else if (section === "table")
    {
        if ($("#dvTableContent").length)
            return $("#dvTableContent").html();
        return "-";
    }
}