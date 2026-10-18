$(document).ready(function ()
{
    if ($("#table").length)
        Tables("table", 1, 0);

    if ($("#dvLaporan").length)
        $("#dvLaporan").hide().fadeIn(300);
});

function onPrevPage()
{
    let page = parseInt($("#page").val());
    if (page === 1)
        return;

    page -= 1;
    $("#page").val(page);

    onChangePage();
}

function onNextPage()
{
    let page = parseInt($("#page").val());
    let totalpage = parseInt($("#totalpage").val());

    if (page === totalpage)
        return;

    page += 1;
    $("#page").val(page);

    onChangePage();
}

function onChangePage()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("urut", "urut");
    qsb.addInput("page", "page");

    document.location.href = "penerimaanlain.laporan.php?" + qsb.createQs();
}

function onChangeUrut(urut)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.add("urut", urut);
    qsb.addInput("page", "page");

    document.location.href = "penerimaanlain.laporan.php?" + qsb.createQs();
}

function refresh()
{
    onChangePage();
}

function getPageContent(section)
{
    if (section === "departemen")
        return $("#departemen").val();

    if (section === "penerimaan")
        return $("#namapenerimaan").val();

    if (section === "tanggal1")
        return $("#tanggal1").val();

    if (section === "tanggal2")
        return $("#tanggal2").val();

    if (section === "laporan")
        return $("#dvLaporan").html();

    return "-";
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('penerimaanlain.laporan.cetak.php?'+qsb.createQs(), 'RekapPenerimaanLainCetak','780','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("urut", "urut");
    qsb.addInput("page", "page");

    newWindow('penerimaanlain.laporan.excel.php?'+qsb.createQs(), 'RekapPenerimaanLainExcel','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}
