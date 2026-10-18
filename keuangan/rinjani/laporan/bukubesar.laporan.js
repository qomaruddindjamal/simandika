$(document).ready(function ()
{
    if ($("#table").length)
        Tables("table", 1, 0);
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
    let totalPage = parseInt($("#totalpage").val());
    if (page === totalPage)
        return;

    page += 1;
    $("#page").val(page);

    onChangePage();
}

function onChangePage()
{
    let qsb = new QsBuilder();
    qsb.add("op", "daftar");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("kategori", "kategori");
    qsb.addInput("koderek", "koderek");
    qsb.addInput("namarek", "namarek");
    qsb.addInput("page", "page");
    qsb.addInput("urut", "urut");

    let dvLaporanData = $("#dvLaporanData");
    dvLaporanData.html("memuat ..");

    $.ajax({
        url: "bukubesar.laporan.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dvLaporanData.html(response);
            dvLaporanData.hide().fadeIn(400);

            if ($("#table").length)
                Tables('table', 1, 0);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('bukubesar.laporan.cetak.php?'+qsb.createQs(), 'CetakBukuBesar1223','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("kategori", "kategori");
    qsb.addInput("koderek", "koderek");
    qsb.addInput("namarek", "namarek");
    qsb.addInput("urut", "urut");
    qsb.add("page", 1);

    newWindow('bukubesar.laporan.excel.php?'+qsb.createQs(), 'ExcelBukuBesar1223','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function getPageContent(section)
{
    if (section === "departemen")
        return $("#departemen").val();

    if (section === "tahunbuku")
        return $("#namatahunbuku").val();

    if (section === "tanggal1")
        return $("#tanggal1").val();

    if (section === "tanggal2")
        return $("#tanggal2").val();

    if (section === "rekening")
        return $("#koderek").val() + " " + $("#namarek").val();

    if (section === "laporanjumlah")
        return $("#dvLaporanJumlah").html();

    if (section === "laporandata")
        return $("#dvLaporanData").html();

    return "-";
}