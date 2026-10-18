$(document).ready(function ()
{
    if ($("#tablejtt").length)
        Tables('tablejtt', 1, 0);

    if ($("#tableskr").length)
        Tables('tableskr', 1, 0);

    $("#dvLaporan").hide().fadeIn(400);

});

function showInfoSiswa()
{
    let qsb = new QsBuilder();
    qsb.addInput("nis", "nis");

    newWindow('../../library/infosiswa.dialog.php?'+qsb.createQs(), 'InformasiSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function showRiwayatJtt(idpenerimaan, namapenerimaan)
{
    let qsb = new QsBuilder();
    qsb.add("idpenerimaan", idpenerimaan);
    qsb.add("namapenerimaan", namapenerimaan);
    qsb.addInput("userid", "nis");
    qsb.addInput("username", "nama");
    qsb.addInput("idtahunbuku", "idtahunbuku");

    newWindow('bayarsiswa.riwayat.jtt.php?'+qsb.createQs(), 'RiwayatJttSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function showRiwayatSkr(idpenerimaan, namapenerimaan)
{
    let qsb = new QsBuilder();
    qsb.add("idpenerimaan", idpenerimaan);
    qsb.add("namapenerimaan", namapenerimaan);
    qsb.addInput("userid", "nis");
    qsb.addInput("username", "nama");
    qsb.addInput("idtahunbuku", "idtahunbuku");

    newWindow('bayarsiswa.riwayat.skr.php?'+qsb.createQs(), 'RiwayatSkrSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('bayarsiswa.laporan.cetak.php?'+qsb.createQs(), 'CetakBayarSiswa23232','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function getPageContent(section)
{
    if (section === "departemen")
        return $("#departemen").val();

    if (section === "tanggal1")
        return $("#tanggal1").val();

    if (section === "tanggal2")
        return $("#tanggal2").val();

    if (section === "siswa")
        return $("#divSectionUser").html();

    if (section === "laporan")
        return $("#dvLaporan").html();

    return "-";
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("userid", "nis");
    qsb.addInput("username", "nama");

    newWindow('bayarsiswa.laporan.excel.php?'+qsb.createQs(), 'ExcelBayarSiswaSkr645645','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}