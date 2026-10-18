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

    parent.content.location.href = "../pengeluaran/blank.php";
}

async function showLaporan()
{
    if ($("#departemen option").length === 0)
        return;

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "tahunbuku");
    qsb.add("namatahunbuku", $("#tahunbuku option:selected").text());
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "laporan.content.php?" + qsb.createQs();
}

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

function showHelp()
{
    newWindow('../help/pl_laporan.html', 'LaporanPengeluaranHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0')
}

function onChangeDept()
{
    let qsb = new QsBuilder();
    qsb.add("op", "tahunbuku");
    qsb.addInput("departemen", "departemen");

    $("#dvTahunBuku").html("memuat..");

    $.ajax({
        url: "laporan.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            $("#dvTahunBuku").html(html);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}