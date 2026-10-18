function showRiwayatTabungan(idTabungan, namaTabungan)
{
    let qsb = new QsBuilder();
    qsb.add("idtabungan", idTabungan);
    qsb.add("namatabungan", namaTabungan);
    qsb.addInput("departemen", "departemen");
    qsb.addInput("nip", "nip");
    qsb.addInput("nama", "nama");

    let addr = "laporanpegawai.laporan.riwayat.php?" + qsb.createQs();
    newWindow(addr, 'ExcelTransaksiTabunganPegawai2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function showInfoPegawai()
{
    let qsb = new QsBuilder();
    qsb.add("nip", $("#nip").val());

    newWindow('../library/infopegawai.dialog.php?'+qsb.createQs(), 'InformasiPegawai','620','520','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    let addr = "laporanpegawai.laporan.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakTransaksiTabunganPegawai2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("nip", "nip");
    qsb.addInput("nama", "nama");

    let addr = "laporanpegawai.laporan.excel.php?" + qsb.createQs();
    newWindow(addr, 'ExcelTransaksiTabunganPegawai2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function getPageContent(section)
{
    if (section === "pegawai")
    {
        if ($("#divSectionUser").length)
            return $("#divSectionUser").html();

        return "-";
    }
    else if (section === "report")
    {
        if ($("#dvSectionReport").length)
            return $("#dvSectionReport").html();

        return "-";
    }
}