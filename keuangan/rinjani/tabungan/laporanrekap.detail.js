$(document).ready(function ()
{
    if ($("#tabDetail").length)
        Tables('tabDetail', 1, 0);
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
    let npage = parseInt($("#npage").val());

    if (page === npage)
        return;

    page += 1;
    $("#page").val(page);

    onChangePage();
}

function onChangePage()
{
    $("#dvContent").html("memuat ..");

    let qsb = new QsBuilder();
    qsb.add("op", "detail");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.addInput("namatabungan", "namatabungan");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("idpetugas", "idpetugas");
    qsb.addInput("namapetugas", "namapetugas");
    qsb.addInput("jenis", "jenis");
    qsb.addInput("kelompok", "kelompok");
    qsb.addInput("page", "page");
    qsb.addInput("ndata", "ndata");

    $.ajax({
        url: "laporanrekap.detail.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            $("#dvContent").html(html).hide().fadeIn(400);

            if ($("#tabDetail").length)
                Tables('tabDetail', 1, 0);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}