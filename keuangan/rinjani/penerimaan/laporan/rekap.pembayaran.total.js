$(document).ready(function ()
{
    if ($("#table").length !== 0)
        Tables('table', 1, 0);

    if ($("#dvLaporan").length)
        $("#dvLaporan").hide().fadeIn(500);
});

function showDetail(dept, idtahunbuku, idkategori, idpenerimaan, tanggal1, tanggal2, petugas)
{
    let qsb = new QsBuilder();
    qsb.add("dept", dept);
    qsb.add("idtahunbuku", idtahunbuku);
    qsb.add("idkategori", idkategori);
    qsb.add("idpenerimaan", idpenerimaan);
    qsb.add("tanggal1", tanggal1);
    qsb.add("tanggal2", tanggal2);
    qsb.add("petugas", petugas);

    let addr = "rekap.pembayaran.total.detail.php?" + qsb.createQs();
    newWindow(addr, 'DetailLapRekap','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function getPageContent(section)
{
    if (section === "departemen")
        return $("#departemen").val();

    if (section === "kategori")
        return $("#namakategori").val();

    if (section === "tanggal1")
        return $("#tanggal1").val();

    if (section === "tanggal2")
        return $("#tanggal2").val();

    if (section === "namalaporan")
        return $("#namalaporan").val();

    if (section === "petugas")
        return $("#namapetugas").val();

    if (section === "laporan")
        return $("#dvLaporan").html();

    return "-";
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('rekap.pembayaran.total.cetak.php?'+qsb.createQs(), 'RekapPenerimaanCetak','780','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("idlaporan", "idlaporan");
    qsb.addInput("namalaporan", "namalaporan");
    qsb.addInput("idpetugas", "idpetugas");
    qsb.addInput("namapetugas", "namapetugas");

    newWindow('rekap.pembayaran.total.excel.php?'+qsb.createQs(), 'RekapPenerimaanExcel','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function refresh()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("idlaporan", "idlaporan");
    qsb.addInput("namalaporan", "namalaporan");
    qsb.addInput("idpetugas", "idpetugas");
    qsb.addInput("namapetugas", "namapetugas");

    document.location.href = "rekap.pembayaran.total.php?" + qsb.createQs();
}