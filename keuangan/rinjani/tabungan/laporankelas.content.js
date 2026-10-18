$(document).ready(function ()
{
    if ($("#tabDaftarTabungan").length)
    {
        $("#tabDaftarTabungan").hide().fadeIn(300);

        if ($("#tabDaftarTabungan").length)
            Tables('tabDaftarTabungan', 1, 0);
    }
});

function showRiwayatTabungan(nis, nama)
{
    let qsb = new QsBuilder();
    qsb.addInput("idtabungan", "idtabungan");
    qsb.addInput("namatabungan", "namatabungan");
    qsb.addInput("departemen", "departemen");
    qsb.add("nis", nis);
    qsb.add("nama", nama);

    let addr = "laporansiswa.laporan.riwayat.php?" + qsb.createQs();
    newWindow(addr, 'RiwayatTransaksiTabungan3','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function refresh()
{
    refreshRekap();
    refreshRiwayat();
}

function refreshRekap()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchrekap");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.addInput("nislist", "nislist");

    let dvRekapTabungan = $("#dvRekapTabungan");
    dvRekapTabungan.html("memuat ..");

    $.ajax({
        url: "laporankelas.content.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dvRekapTabungan.html(response);

            if ($("#tabDaftarTabungan").length)
                Tables('tabDaftarTabungan', 1, 0);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}

function refreshRiwayat()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchpage");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.addInput("urut", "urut");
    qsb.addInput("page", "page");
    qsb.addInput("nislist", "nislist");

    let dvDaftarTabungan = $("#dvDaftarTabungan");
    dvDaftarTabungan.html("memuat ..");

    $.ajax({
        url: "laporankelas.content.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dvDaftarTabungan.html(response).hide().fadeIn(400);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
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
    refreshRiwayat();
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    let addr = "laporankelas.content.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakTransaksiTabungan2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtingkat", "idtingkat");
    qsb.addInput("namatingkat", "namatingkat");
    qsb.addInput("idkelas", "idkelas");
    qsb.addInput("namakelas", "namakelas");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.addInput("namatabungan", "namatabungan");
    qsb.addInput("urut", "urut");

    let addr = "laporankelas.content.excel.php?" + qsb.createQs();
    newWindow(addr, 'ExcelTransaksiTabungan2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function getPageContent(section)
{
    if (section === "departemen")
    {
        return $("#departemen").val();
    }
    else if (section === "tingkatkelas")
    {
        return $("#namatingkat").val() + " - " + $("#namakelas").val();
    }
    else if (section === "tabungan")
    {
        return $("#namatabungan").val();
    }
    else if (section === "rekap")
    {
        if ($("#dvRekapTabungan").length)
            return $("#dvRekapTabungan").html();

        return "-";
    }
    else if (section === "riwayat")
    {
        if ($("#dvDaftarTabungan").length)
            return $("#dvDaftarTabungan").html();

        return "-";
    }
}

function showInfoSiswa(nis)
{
    let qsb = new QsBuilder();
    qsb.add("nis", nis);

    newWindow('../library/infosiswa.dialog.php?'+qsb.createQs(), 'InformasiSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}