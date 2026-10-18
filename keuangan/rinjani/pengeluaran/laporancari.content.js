$(document).ready(function ()
{
    if ($("#table").length)
    {
        $("#table").hide().fadeIn(400);
        Tables("table", 1, 0);
    }
});

function onPrevPage()
{
    let page = parseInt($("#page").val());
    if (page === 1)
        return;

    $("#page").val(page - 1);
    onChangePage();
}

function onNextPage()
{
    let page = parseInt($("#page").val());
    let totalpage = parseInt($("#totalpage").val());
    if (page === totalpage)
        return;

    $("#page").val(page + 1);
    onChangePage();
}

function onChangePage()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("kriteria", "kriteria");
    qsb.addInput("namakriteria", "namakriteria");
    qsb.addInput("keyword", "keyword");
    qsb.addInput("page", "page");

    document.location.href = "laporancari.content.php?" + qsb.createQs();
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("kriteria", "kriteria");
    qsb.addInput("namakriteria", "namakriteria");
    qsb.addInput("keyword", "keyword");

    newWindow('laporancari.content.excel.php?'+qsb.createQs(), 'ExcelCariRincianPengeluaran','780','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('laporancari.content.cetak.php?'+qsb.createQs(), 'CetakCariRincianPengeluaran','780','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function refresh()
{
    document.location.reload();
}

function getPageContent(section)
{
    if (section === "departemen")
        return $("#departemen").val();

    if (section === "kriteria")
        return $("#namakriteria").val();

    if (section === "keyword")
        return $("#keyword").val();

    if (section === "tahunbuku")
        return $("#namatahunbuku").val();

    if (section === "tanggal1")
        return $("#tanggal1").val();

    if (section === "tanggal2")
        return $("#tanggal2").val();

    if (section === "laporan")
        return $("#dvLaporan").html();

    return "-";
}

function cetakbukti(id)
{
    let qsb = new QsBuilder();
    qsb.add("idtransaksi", id);

    newWindow('laporan.kuitansi.php?'+qsb.createQs(), 'BuktiPengeluaran','360','600','resizable=1,scrollbars=1,status=0,toolbar=0')
}

function edit(id)
{
    let qsb = new QsBuilder();
    qsb.add("idtransaksi", id);
    qsb.addInput("departemen", "departemen");

    newWindow('laporan.rincian.edit.php?'+qsb.createQs(), 'EditPengeluaran','550','500','resizable=1,scrollbars=1,status=0,toolbar=0')
}