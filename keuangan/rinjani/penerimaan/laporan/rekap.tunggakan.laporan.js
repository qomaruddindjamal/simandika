$(document).ready(function () {
    if ($("#table").length)
    {
        Tables('table', 1, 0);

        $("#table").hide().fadeIn(300);
    }
});

function refresh()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtingkat", "idtingkat");
    qsb.addInput("namatingkat", "namatingkat");
    qsb.addInput("idkelas", "idkelas");
    qsb.addInput("namakelas", "namakelas");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");

    document.location.href = "rekap.tunggakan.laporan.php?" + qsb.createQs();
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtingkat", "idtingkat");
    qsb.addInput("namatingkat", "namatingkat");
    qsb.addInput("idkelas", "idkelas");
    qsb.addInput("namakelas", "namakelas");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");

    newWindow('rekap.tunggakan.laporan.excel.php?'+qsb.createQs(), 'RekapTunggakanExcel','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function showInfoSiswa(nis)
{
    let qsb = new QsBuilder();
    qsb.add("nis", nis);

    newWindow('../../library/infosiswa.dialog.php?'+qsb.createQs(), 'InformasiSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}
