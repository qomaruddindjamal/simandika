function showDetail(idtabungan, namatabungan, jenis, kelompok)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.add("idtabungan", idtabungan);
    qsb.add("namatabungan", namatabungan);
    qsb.addInput("tanggal1", "datetime1");
    qsb.addInput("tanggal2", "datetime2");
    qsb.addInput("idpetugas", "idpetugas");
    qsb.addInput("namapetugas", "namapetugas");
    qsb.add("jenis", jenis);
    qsb.add("kelompok", kelompok);

    let addr = "laporanrekap.detail.php?" + qsb.createQs();
    newWindow(addr, 'DetailLapRekap2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    let addr = "laporanrekap.content.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakRekapTabungan2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("idpetugas", "idpetugas");
    qsb.addInput("namapetugas", "namapetugas");

    let addr = "laporanrekap.content.excel.php?" + qsb.createQs();
    newWindow(addr, 'ExcelRekapTabungan2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function getPageContent(section)
{
    if (section === "departemen")
    {
        return $("#departemen").val();
    }
    else if (section === "petugas")
    {
        return $("#namapetugas").val();
    }
    else if (section === "tanggal1")
    {
        return $("#tanggal1").val();
    }
    else if (section === "tanggal2")
    {
        return $("#tanggal2").val();
    }
    else if (section === "rekap")
    {
        if ($("#dvRekap").length)
            return $("#dvRekap").html();

        return "-";
    }
}