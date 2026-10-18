$(document).ready(function()
{
    $("#tabContent").hide().fadeIn(400);
});

function fetchPage(page)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.add("page", page);
    qsb.addInput("urut", "urut");

    parent.content.location.href = "jurnalpengeluaran2.content.php?" + qsb.createQs();
}

function changePage()
{
    let page = $("#page").val();
    fetchPage(page);
}

function prevPage()
{
    let page = parseInt($("#page").val());
    if (page === 1)
        return;

    fetchPage(page - 1);
}

function nextPage()
{
    let page = parseInt($("#page").val());
    let totalpage = parseInt($("#totalpage").val());
    if (page === totalpage)
        return;

    fetchPage(page + 1);
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
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("page", "page");

    let addr = "jurnalpengeluaran2.content.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakJurnalPengeluran','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}