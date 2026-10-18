var helpBox = null;

$(document).ready(function ()
{
    helpBox = new DialogBox("#divHelpDialog", 620, 520);

    if ($("#table").length)
        Tables("table", 0, 0);
});

function refresh()
{
    document.location.reload();
}

function showHelp()
{
    $.ajax({
        url: "../help/rf_sumberdana.html?" + Rnd.String(),
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

function tambah()
{
    var qsb = new QsBuilder();
    qsb.add("idsumberdana", 0);

    newWindow('sumberdana.dialog.php?' + qsb.createQs(),'','550','350','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function edit(idSumberDana)
{
    var qsb = new QsBuilder();
    qsb.add("idsumberdana", idSumberDana);

    newWindow('sumberdana.dialog.php?' + qsb.createQs(),'','550','350','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function hapus(idSumberDana)
{
    if (!confirm("Hapus sumber dana ini?"))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "hapus");
    qsb.add("idsumberdana", idSumberDana);

    $.ajax({
        url: "sumberdana.ajax.php",
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
    })
}

function setAktif(idSumberDana)
{
    var statusAktif = parseInt($("#statusaktif-" + idSumberDana).val());
    var newAktif = 1;
    if (statusAktif === 1)
        newAktif = 0;

    var msg = "";
    if (newAktif === 0)
        msg = "NON AKTIF kan sumber dana ini?"
    else
        msg = "AKTIF kan kembali sumber dana ini?"

    if (!confirm(msg))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "setaktif");
    qsb.add("idsumberdana", idSumberDana);
    qsb.add("newaktif", newAktif);

    $.ajax({
        url: "sumberdana.ajax.php",
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

            if (newAktif === 0)
            {
                $("#statusaktif-" + idSumberDana).val(0);
                $("#imaktif-" + idSumberDana).attr('src', '../images/ico/nonaktif.png');
            }
            else
            {
                $("#statusaktif-" + idSumberDana).val(1);
                $("#imaktif-" + idSumberDana).attr('src', '../images/ico/aktif.png');
            }
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function getPageContent(section)
{
    if (section === "table")
    {
        if ($("#dvTableContent").length)
            return $("#dvTableContent").html();
        return "-";
    }
}

function cetak()
{
    var addr = "sumberdana.cetak.php?departemen=ALL";
    newWindow(addr, 'CetakSumberDana','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}