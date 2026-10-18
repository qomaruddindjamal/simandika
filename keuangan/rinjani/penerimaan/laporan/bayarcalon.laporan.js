$(document).ready(function ()
{
    if ($("#tablejtt").length)
        Tables('tablejtt', 1, 0);

    if ($("#tableskr").length)
        Tables('tableskr', 1, 0);

    $("#dvLaporan").hide().fadeIn(400);

});

function showInfoCalonSiswa()
{
    var qsb = new QsBuilder();
    qsb.addInput("nic", "nic");

    newWindow('../../library/infocalonsiswa.dialog.php?'+qsb.createQs(), 'InformasiCalonSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0');
}

$(document).ready(function ()
{
    if ($("#tablejtt").length)
        Tables('tablejtt', 1, 0);

    if ($("#tableskr").length)
        Tables('tableskr', 1, 0);

});

function showRiwayatJtt(idpenerimaan, namapenerimaan)
{
    let qsb = new QsBuilder();
    qsb.add("idpenerimaan", idpenerimaan);
    qsb.add("namapenerimaan", namapenerimaan);
    qsb.addInput("userid", "nis");
    qsb.addInput("username", "nama");
    qsb.addInput("idcalon", "idcalon");
    qsb.addInput("idtahunbuku", "idtahunbuku");

    newWindow('bayarcalon.riwayat.cswjb.php?'+qsb.createQs(), 'RiwayatCsWjbCalonSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function showRiwayatSkr(idpenerimaan, namapenerimaan)
{
    let qsb = new QsBuilder();
    qsb.add("idpenerimaan", idpenerimaan);
    qsb.add("namapenerimaan", namapenerimaan);
    qsb.addInput("userid", "nis");
    qsb.addInput("username", "nama");
    qsb.addInput("idcalon", "idcalon");
    qsb.addInput("idtahunbuku", "idtahunbuku");

    newWindow('bayarcalon.riwayat.csskr.php?'+qsb.createQs(), 'RiwayatCsSkrCalonSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('bayarcalon.laporan.cetak.php?'+qsb.createQs(), 'CetakBayarCalonSiswa23232','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
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
    qsb.addInput("userid", "nic");
    qsb.addInput("username", "nama");

    newWindow('bayarcalon.laporan.excel.php?'+qsb.createQs(), 'ExcelBayarCalonSiswaSkr645645','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}