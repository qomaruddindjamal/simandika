var tanggalIx = 0;

function showPilihTanggal(ix, tanggal)
{
    tanggalIx = ix;

    var ls = tanggal.split("-");

    var qsb = new QsBuilder();
    qsb.add("tahun", ls[0]);
    qsb.add("bulan", ls[1]);
    qsb.add("pilih", tanggal);

    newWindow("../library/calendar.dialog.php?" + qsb.createQs(), 'Kalender2','550','400','resizable=1,scrollbars=1,status=0,toolbar=0');
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

    parent.content.location.href = "../tabungan/blank.php";
}

function showLaporan()
{
    if ($("#departemen option").length === 0)
        return;

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");

    parent.content.location.href = "laporansiswa.content.php?" + qsb.createQs();
}

function onChangeDept()
{
    showBlankPage();
}

function showBlankPage()
{
    parent.content.location.href = "blank.php";
}

function showHelp()
{
    newWindow('../help/ts_lapsiswa.html', 'LaporanTabunganSiswaHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0')
}