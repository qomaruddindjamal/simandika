function showDetail(ixData)
{
    let stIdList64 = $("#stidlist64-" + ixData).val();
    let idtabungan = $("#idtab-" + ixData).val();
    let namatabungan = $("#nmtab-" + ixData).val();
    let kelompok = $("#kelompok-" + ixData).val();
    let namalokasi = $("#namalokasi-" + ixData).val();
    let kodelokasi = $("#kodelokasi-" + ixData).val();

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.add("idtabungan", idtabungan);
    qsb.add("namatabungan", namatabungan);
    qsb.add("stidlist64", stIdList64);
    qsb.add("kelompok", kelompok);
    qsb.add("namalokasi", namalokasi);
    qsb.add("kodelokasi", kodelokasi);

    let addr = "laporanlokasi.detail.php?" + qsb.createQs();
    newWindow(addr, 'DetailLapLokasi3','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    let addr = "laporanlokasi.content.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakSaldoAkhirTabungan2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    let addr = "laporanlokasi.content.excel.php?" + qsb.createQs();
    newWindow(addr, 'ExcelSaldoAkhirLokasi2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
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

function pindahLokasiDana(ixData)
{
    let stIdList64 = $("#stidlist64-" + ixData).val();
    let idtabungan = $("#idtab-" + ixData).val();
    let namatabungan = $("#nmtab-" + ixData).val();
    let kelompok = $("#kelompok-" + ixData).val();
    let namalokasi = $("#namalokasi-" + ixData).val();
    let kodelokasi = $("#kodelokasi-" + ixData).val();

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.add("idtabungan", idtabungan);
    qsb.add("namatabungan", namatabungan);
    qsb.add("stidlist64", stIdList64);
    qsb.add("kelompok", kelompok);
    qsb.add("namalokasi", namalokasi);
    qsb.add("kodelokasi", kodelokasi);

    let addr = "laporanlokasi.content.pindah.php?" + qsb.createQs();
    newWindow(addr, 'PindahLokasi3','425','450','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function refreshPage()
{
    document.location.reload();
}