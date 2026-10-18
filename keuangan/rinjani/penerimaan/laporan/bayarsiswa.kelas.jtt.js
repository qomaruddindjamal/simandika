$(document).ready(function ()
{
    if ($("#table").length !== 0)
        Tables('table', 1, 0);

    $("#dvLaporan").hide().fadeIn(400);
});

function refresh()
{
    onChangePage();
}

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
    qsb.addInput("idtingkat", "idtingkat");
    qsb.addInput("namatingkat", "namatingkat");
    qsb.addInput("idkelas", "idkelas");
    qsb.addInput("namakelas", "namakelas");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("status", "status");
    qsb.addInput("namastatus", "namastatus");
    qsb.addInput("urut", "urut");
    qsb.addInput("page", "page");

    document.location.href = "bayarsiswa.kelas.jtt.php?" + qsb.createQs();
}

function changeUrutan(urut)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtingkat", "idtingkat");
    qsb.addInput("namatingkat", "namatingkat");
    qsb.addInput("idkelas", "idkelas");
    qsb.addInput("namakelas", "namakelas");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("status", "status");
    qsb.addInput("namastatus", "namastatus");
    qsb.add("urut", urut);
    qsb.add("page", 1);

    document.location.href = "bayarsiswa.kelas.jtt.php?" + qsb.createQs();
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('bayarsiswa.kelas.jtt.cetak.php?'+qsb.createQs(), 'CetakBayarSiswaKelas','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtingkat", "idtingkat");
    qsb.addInput("namatingkat", "namatingkat");
    qsb.addInput("idkelas", "idkelas");
    qsb.addInput("namakelas", "namakelas");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("status", "status");
    qsb.addInput("namastatus", "namastatus");
    qsb.addInput("urut", "urut");

    newWindow('bayarsiswa.kelas.jtt.excel.php?'+qsb.createQs(), 'ExcelBayarSiswaKelas','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function getPageContent(section)
{
    if (section === "departemen")
        return $("#departemen").val();

    if (section === "tingkat")
        return $("#namatingkat").val();

    if (section === "kelas")
        return $("#namakelas").val();

    if (section === "kategori")
        return $("#namakategori").val();

    if (section === "penerimaan")
        return $("#namapenerimaan").val();

    if (section === "status")
        return $("#namastatus").val();

    if (section === "laporan")
        return $("#dvLaporan").html();

    return "-";
}

function showInfoSiswa(nis)
{
    let qsb = new QsBuilder();
    qsb.add("nis", nis);

    newWindow('../../library/infosiswa.dialog.php?'+qsb.createQs(), 'InformasiSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}