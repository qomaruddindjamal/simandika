$(document).ready(function ()
{
    if($("#table1").length)
        Tables('table1', 0, 0);

    if($("#table2").length)
        Tables('table2', 0, 0);

    if($("#table3").length)
        Tables('table3', 0, 0);

    if($("#table4").length)
        Tables('table4', 0, 0);
});

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('neraca.content.cetak.php?'+qsb.createQs(), 'CetakPerubahanModal_23487','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");

    newWindow('neraca.content.excel.php?'+qsb.createQs(), 'ExcelPerubahanModal_4645','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
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