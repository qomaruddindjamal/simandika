function onChangePage()
{
    let page = $("#page").val();

    let dvTabTabunganList = $("#dvTabTabunganList");
    dvTabTabunganList.html("memuat ..");

    let qsb = new QsBuilder();
    qsb.add("op", "riwayat");
    qsb.addInput("nip", "nip");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.add("page", page);

    $.ajax({
        url: "laporanpegawai.laporan.riwayat.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dvTabTabunganList.html(response).hide().fadeIn(400);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function onPrevPageClick()
{
    let page = parseInt($("#page").val());
    if (page === 1)
        return;

    page -= 1;
    $("#page").val(page + "");
    onChangePage();
}

function onNextPageClick()
{
    let page = parseInt($("#page").val());
    let totalpage = parseInt($("#totalpage").val());
    if (page === totalpage)
        return;

    page += 1;
    $("#page").val(page + "");
    onChangePage();
}