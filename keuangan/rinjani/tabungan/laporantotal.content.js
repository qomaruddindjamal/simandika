function showDetail(idtabungan, namatabungan, jenis, kelompok)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.add("idtabungan", idtabungan);
    qsb.add("namatabungan", namatabungan);
    qsb.add("jenis", jenis);
    qsb.add("kelompok", kelompok);

    let addr = "laporantotal.detail.php?" + qsb.createQs();
    newWindow(addr, 'DetailLapTotal2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    let addr = "laporantotal.content.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakSaldoAkhirTabungan2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    let addr = "laporantotal.content.excel.php?" + qsb.createQs();
    newWindow(addr, 'ExcelSaldoAkhirTabungan2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
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