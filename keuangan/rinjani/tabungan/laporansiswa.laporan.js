function showRiwayatTabungan(idTabungan, namaTabungan)
{
    let qsb = new QsBuilder();
    qsb.add("idtabungan", idTabungan);
    qsb.add("namatabungan", namaTabungan);
    qsb.addInput("departemen", "departemen");
    qsb.addInput("nis", "nis");
    qsb.addInput("nama", "nama");

    let addr = "laporansiswa.laporan.riwayat.php?" + qsb.createQs();
    newWindow(addr, 'RiwayatTransaksiTabungan2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    let addr = "laporansiswa.laporan.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakTransaksiTabunganSiswa2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function excel()
{
    var qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("nis", "nis");
    qsb.addInput("nama", "nama");

    let addr = "laporansiswa.laporan.excel.php?" + qsb.createQs();
    newWindow(addr, 'ExcelTransaksiTabunganSiswa2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function getPageContent(section)
{
    if (section === "siswa")
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

function showInfoSiswa()
{
    var qsb = new QsBuilder();
    qsb.add("nis", $("#nis").val());

    newWindow('../library/infosiswa.dialog.php?'+qsb.createQs(), 'InformasiSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}