$(document).ready(function ()
{
    if($("#table").length)
        Tables('table', 0, 0);
});

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('modal.content.cetak.php?'+qsb.createQs(), 'CetakPerubahanModal_23487','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");

    newWindow('modal.content.excel.php?'+qsb.createQs(), 'ExcelPerubahanModal_4645','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
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
        return $("#dvLaporan").html();

    return "-";
}