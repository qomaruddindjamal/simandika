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
    qsb.addInput("page", "page");
    qsb.addInput("urut", "urut");

    let dvDaftarTransaksi = $("#dvDaftarTransaksi");
    dvDaftarTransaksi.html("memuat ..");

    $.ajax({
        url: "transaksi.content.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dvDaftarTransaksi.html(response);
            dvDaftarTransaksi.hide().fadeIn(400);

            if ($("#table").length)
                Tables('table', 1, 0);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function refresh()
{
    onChangePage();
}

function onChangeUrut(urut)
{
    let qsb = new QsBuilder();
    qsb.add("op", "daftar");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.add("page", 1);
    qsb.add("urut", urut);

    let dvDaftarTransaksi = $("#dvDaftarTransaksi");
    dvDaftarTransaksi.html("memuat ..");

    $.ajax({
        url: "transaksi.content.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dvDaftarTransaksi.html(response);
            dvDaftarTransaksi.hide().fadeIn(400);

            $("#page").val(1);

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

    newWindow('transaksi.content.cetak.php?'+qsb.createQs(), 'CetakTransaksiKeuangan','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.add("op", "daftar");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("urut", "urut");

    newWindow('transaksi.content.excel.php?'+qsb.createQs(), 'ExcelTransaksiKeuangan','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
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

    if (section === "laporan")
        return $("#dvDaftarTransaksi").html();

    return "-";
}