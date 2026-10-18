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
    qsb.addInput("idproses", "idproses");
    qsb.addInput("namaproses", "namaproses");
    qsb.addInput("idkelompok", "idkelompok");
    qsb.addInput("namakelompok", "namakelompok");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("status", "status");
    qsb.addInput("namastatus", "namastatus");
    qsb.addInput("urut", "urut");
    qsb.addInput("page", "page");

    document.location.href = "bayarcalon.kelompok.cswjb.php?" + qsb.createQs();
}

function changeUrutan(urut)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idproses", "idproses");
    qsb.addInput("namaproses", "namaproses");
    qsb.addInput("idkelompok", "idkelompok");
    qsb.addInput("namakelompok", "namakelompok");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("status", "status");
    qsb.addInput("namastatus", "namastatus");
    qsb.add("urut", urut);
    qsb.add("page", 1);

    document.location.href = "bayarcalon.kelompok.cswjb.php?" + qsb.createQs();
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('bayarcalon.kelompok.cswjb.cetak.php?'+qsb.createQs(), 'CetakBayarCalonKelompok','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idproses", "idproses");
    qsb.addInput("namaproses", "namaproses");
    qsb.addInput("idkelompok", "idkelompok");
    qsb.addInput("namakelompok", "namakelompok");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("status", "status");
    qsb.addInput("namastatus", "namastatus");
    qsb.addInput("urut", "urut");

    newWindow('bayarcalon.kelompok.cswjb.excel.php?'+qsb.createQs(), 'ExcelBayarCalonKelompok','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function getPageContent(section)
{
    if (section === "departemen")
        return $("#departemen").val();

    if (section === "proses")
        return $("#namaproses").val();

    if (section === "kelompok")
        return $("#namakelompok").val();

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

function showInfoCalonSiswa(nic)
{
    var qsb = new QsBuilder();
    qsb.add("nic", nic);

    newWindow('../../library/infocalonsiswa.dialog.php?'+qsb.createQs(), 'InformasiCalonSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0');
}