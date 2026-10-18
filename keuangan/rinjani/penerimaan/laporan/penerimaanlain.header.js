var tanggalIx = 0;

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

    parent.content.location.href = "../penerimaan/laporan/blank.php";
}

async function showLaporan()
{
    if (!Vldr.HasOption("departemen", "Departemen") ||
        !Vldr.HasOption("penerimaan", "Penerimaan Lain"))
        return;

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("idpenerimaan", "penerimaan");
    qsb.add("namapenerimaan", $("#penerimaan option:selected").text());
    qsb.add("page", 1);
    qsb.add("urut", "p.tanggal DESC, p.replid DESC");

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "penerimaanlain.laporan.php?" + qsb.createQs();
}


function pause(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function showBlankPage()
{
    parent.content.location.href = "blank.php";
}

function onChangeDept()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchpenerimaan");
    qsb.addInput("departemen", "departemen");

    let spPenerimaan = $("#spPenerimaan");
    spPenerimaan.html("memuat ..");

    $.ajax({
        url: "penerimaanlain.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spPenerimaan.html(html);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function showHelp()
{
    newWindow('../../help/pn_penerimaanlain.html', 'PenerimaanLainHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}
