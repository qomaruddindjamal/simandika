var helpBox = null;

$(document).ready(function ()
{
    if ($("#table").length)
        Tables('table', 1, 0);

    $("#dvTableContent").hide().fadeIn(400);

    helpBox = new DialogBox("#divHelpDialog", 600, 500);
});

function showHelp()
{
    $.ajax({
        url: "../help/rf_tahunbuku.html",
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

ubah = function (idTahunBuku)
{
    var qsb = new QsBuilder();
    qsb.add("idtahunbuku", idTahunBuku);
    qsb.addInput("departemen", "departemen");

    newWindow('tahunbuku2.dialog.php?' + qsb.createQs(),'','500','350','resizable=1,scrollbars=1,status=0,toolbar=0');
};

tambah = function ()
{
    var qsb = new QsBuilder();
    qsb.add("idtahunbuku", 0);
    qsb.addInput("departemen", "departemen");

    newWindow('tahunbuku2.dialog.php?' + qsb.createQs(),'','500','350','resizable=1,scrollbars=1,status=0,toolbar=0');
};

refresh = function ()
{
    var qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("from", "from");
    qsb.addInput("sourcefrom", "sourcefrom");

    document.location.href = "tahunbuku2.php?" + qsb.createQs();
};

function getPageContent(section)
{
    if (section === "table")
    {
        if ($("#dvTableContent").length)
            return $("#dvTableContent").html();
        return "-";
    }
}

change_dep = function ()
{
    refresh();
};

function cetak()
{
    var qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    var addr = "tahunbuku2.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakTahunBuku','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}