var tanggalIx = 0;

function showPilihTanggal(ix)
{
    tanggalIx = ix;

    let tanggal = "";
    if (ix === 1)
        tanggal = $("#tanggal1").val();
    else
        tanggal = $("#tanggal2").val();

    let ls = tanggal.split("-");

    let qsb = new QsBuilder();
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

    parent.content.location.href = "../laporan/blank.php";
}

async function showLaporan()
{
    if ($("#departemen option").length === 0)
        return;

    if ($("#tahunbuku option").length === 0)
        return;

    let data64 = $("#tahunbuku").val();
    let ls = JSON.parse(atob(data64));
    let idtahunbuku = ls[0];
    let namatahunbuku = ls[1];

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.add("idtahunbuku", idtahunbuku);
    qsb.add("namatahunbuku", namatahunbuku);
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "rugilaba.content.php?" + qsb.createQs();
}

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

function onTahunBukuChange()
{
    let data64 = $("#tahunbuku").val();
    let ls = JSON.parse(atob(data64));
    let tanggalmulai = ls[3];

    $("#ftanggal1").val(dateutil_formatInaDate(tanggalmulai));
    $("#tanggal1").val(tanggalmulai);

    showBlankPage();
}

function onChangeDept()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchtahunbuku");
    qsb.addInput("departemen", "departemen");

    let spTahunBuku = $("#spTahunBuku");
    spTahunBuku.html("memuat ..");

    showBlankPage();

    $.ajax({
        url: "common.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spTahunBuku.html(html);

            if ($("#tahunbuku option").length === 0)
                return;

            let data64 = $("#tahunbuku").val();
            let ls = JSON.parse(atob(data64));
            let tanggalmulai = ls[3];

            $("#ftanggal1").val(dateutil_formatInaDate(tanggalmulai));
            $("#tanggal1").val(tanggalmulai);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}

function showBlankPage()
{
    parent.content.location.href = "blank.php";
}

function showHelp()
{
    newWindow('../help/lap_rugilaba.html', 'LaporanRugiLabaHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0')
}
