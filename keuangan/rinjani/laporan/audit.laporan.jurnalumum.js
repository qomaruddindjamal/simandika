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
    qsb.addInput("lap", "lap");
    qsb.addInput("page", "page");

    let dvDaftarAudit = $("#dvDaftarAudit");
    dvDaftarAudit.html("memuat ..");

    $.ajax({
        url: "audit.laporan.jurnalumum.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dvDaftarAudit.html(response);
            dvDaftarAudit.hide().fadeIn(400);
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

    newWindow('audit.laporan.jurnalumum.cetak.php?'+qsb.createQs(), 'CetakAuditJurnalUmum','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
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

    if (section === "jenis")
        return $("#jenis").val();

    if (section === "laporan")
        return $("#dvDaftarAudit").html();

    return "-";
}