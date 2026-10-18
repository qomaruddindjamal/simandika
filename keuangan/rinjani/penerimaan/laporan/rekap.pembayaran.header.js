var tanggalIx = 0;

function onSelectionChange()
{
    showBlankPage();
}

function onChangeDept()
{
    showBlankPage();
}

function showBlankPage()
{
    parent.content.location.href = "blank.php";
}

function showPilihTanggal(ix, tanggal)
{
    tanggalIx = ix;

    var ls = tanggal.split("-");

    var qsb = new QsBuilder();
    qsb.add("tahun", ls[0]);
    qsb.add("bulan", ls[1]);
    qsb.add("pilih", tanggal);

    newWindow("../../library/calendar.dialog.php?" + qsb.createQs(), 'Kalender2','550','400','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function acceptCalendar(tanggal)
{
    var ftanggal = dateutil_formatInaDate(tanggal);
    if (tanggalIx === 1)
    {
        $("#ftanggal1").val(ftanggal);
        $("#tanggal1").val(tanggal);
    }
    else
    {
        $("#ftanggal2").val(ftanggal);
        $("#tanggal2").val(tanggal);
    }

    showBlankPage();
}

async function showLaporan()
{
    if ($("#departemen option").length === 0)
        return;

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idkategori", "kategori");
    qsb.add("namakategori", $("#kategori option:selected").text());
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("idlaporan", "laporan");
    qsb.add("namalaporan", $("#laporan option:selected").text());
    qsb.addInput("idpetugas", "petugas");
    qsb.add("namapetugas", $("#petugas option:selected").text());

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    let idlaporan = parseInt($("#laporan").val());
    if (idlaporan === 1)
        parent.content.location.href = "rekap.pembayaran.total.php?" + qsb.createQs();
    else
        parent.content.location.href = "rekap.pembayaran.harian.php?" + qsb.createQs();
}

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

function showHelp()
{
    newWindow('../../help/pn_laprekap.html', 'LaporanRekapHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}
