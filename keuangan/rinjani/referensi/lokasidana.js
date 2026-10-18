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
        url: "../help/rf_lokasidana.html?" + Rnd.String(),
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
    qsb.add("idlokasidana", 0);

    newWindow('lokasidana.dialog.php?' + qsb.createQs(),'','550','350','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function edit(idLokasiDana)
{
    var qsb = new QsBuilder();
    qsb.add("idlokasidana", idLokasiDana);

    newWindow('lokasidana.dialog.php?' + qsb.createQs(),'','550','350','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function hapus(idLokasiDana)
{
    if (!confirm("Hapus lokasi dana ini?"))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "hapus");
    qsb.add("idlokasidana", idLokasiDana);

    $.ajax({
        url: "lokasidana.ajax.php",
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

function setAktif(idLokasiDana)
{
    var statusAktif = parseInt($("#statusaktif-" + idLokasiDana).val());
    var newAktif = 1;
    if (statusAktif === 1)
        newAktif = 0;

    var msg = "";
    if (newAktif === 0)
        msg = "NON AKTIF kan lokasi dana ini?"
    else
        msg = "AKTIF kan kembali lokasi dana ini?"

    if (!confirm(msg))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "setaktif");
    qsb.add("idlokasidana", idLokasiDana);
    qsb.add("newaktif", newAktif);

    $.ajax({
        url: "lokasidana.ajax.php",
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
                $("#statusaktif-" + idLokasiDana).val(0);
                $("#imaktif-" + idLokasiDana).attr('src', '../images/ico/nonaktif.png');
            }
            else
            {
                $("#statusaktif-" + idLokasiDana).val(1);
                $("#imaktif-" + idLokasiDana).attr('src', '../images/ico/aktif.png');
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
    var addr = "lokasidana.cetak.php?departemen=ALL";
    newWindow(addr, 'CetakLokasiDana','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}