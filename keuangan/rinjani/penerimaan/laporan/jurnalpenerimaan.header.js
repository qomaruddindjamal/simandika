var tanggalIx = 0;

function showPilihTanggal(ix, tanggal)
{
    tanggalIx = ix;

    let ls = tanggal.split("-");

    let qsb = new QsBuilder();
    qsb.add("tahun", ls[0]);
    qsb.add("bulan", ls[1]);
    qsb.add("pilih", tanggal);

    newWindow("../../library/calendar.dialog.php?" + qsb.createQs(), 'Kalender2','550','400','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function acceptCalendar(tanggal)
{
    let ftanggal = dateutil_formatInaDate(tanggal);
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

function onChangeDept()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchtahunbuku");
    qsb.addInput("departemen", "departemen");

    let spTahunBuku = $("#spTahunBuku");
    spTahunBuku.html("memuat ..");

    showBlankPage();

    $.ajax({
        url: "jurnalpenerimaan.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spTahunBuku.html(html);
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

async function showLaporan()
{
    if (!Vldr.HasOption("departemen", "Departemen") ||
        !Vldr.HasOption("tahunbuku", "Tahun Buku") ||
        !Vldr.HasOption("kategori", "Kategori Penerimaan"))
        return;

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "tahunbuku");
    qsb.add("namatahunbuku", $("#tahunbuku option:selected").text());
    qsb.addInput("idkategori", "kategori");
    qsb.add("namakategori", $("#kategori option:selected").text());
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.add("page", 1);
    qsb.add("urut", "tanggal DESC, replid DESC");

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "jurnalpenerimaan.laporan.php?" + qsb.createQs();
}

function pause(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}


function showHelp()
{
    newWindow('../../help/pn_jurnalpenerimaan.html', 'JurnalPenerimaanHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}