$(document).ready(function ()
{
    loadPageData(1);
});

function changePage()
{
    var page = parseInt($("#page").val());
    loadPageData(page);
}

function prevPage()
{
    var page = parseInt($("#page").val());
    if (page === 1)
        return;

    page -= 1;
    loadPageData(page);
}

function nextPage()
{
    var page = parseInt($("#page").val());
    var nPage = parseInt($("#npage").val());
    if (page === nPage)
        return;

    page += 1;
    loadPageData(page);
}

function currPage()
{
    var page = $("#page").val();
    loadPageData(page);
}

function refreshPage()
{
    var page = $("#page").val();
    loadPageData(page);
}

function loadPageData(page)
{
    let qsb = new QsBuilder();
    qsb.add("op", "loadpage");
    qsb.add("page", page);
    qsb.addInput("kriteria", "kriteria");
    qsb.addInput("keyword", "keyword");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("npage", "npage");
    qsb.addInput("ndata", "ndata");

    $.ajax({
        url: "carijurnal.content.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            let $newTable = $(html).hide(); // hide first
            $('#dvContent').html($newTable);
            $newTable.fadeIn(500);
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
        if ($("#dvContent").length)
            return $("#dvContent").html();

        return "-";
    }
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("kriteria", "kriteria");
    qsb.addInput("namakriteria", "namakriteria");
    qsb.addInput("keyword", "keyword");
    qsb.addInput("tahunbuku", "tahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("page", "page");

    var addr = "carijurnal.content.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakCariJurnal','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function edit(idJurnal)
{
    var qsb = new QsBuilder();
    qsb.add("idjurnal", idJurnal);
    qsb.addInput("departemen", "departemen");

    var addr = "editjurnal2.php?" + qsb.createQs();
    newWindow(addr, 'EditJurnal','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}