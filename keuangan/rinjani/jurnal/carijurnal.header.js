var tanggalIx = 0;

function change_dep()
{
    let qsb = new QsBuilder();
    qsb.add("op", "tahunbuku");
    qsb.addInput("departemen", "departemen");

    let dvTahunBuku = $("#dvTahunBuku");

    dvTahunBuku.html("memuat ..");
    parent.content.location.href = "blank.php";

    $.ajax({
        url: "carijurnal.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (data)
        {
            dvTahunBuku.html(data);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });

}

function showPilihTanggal(ix, tanggal)
{
    tanggalIx = ix;

    var ls = tanggal.split("-");

    var qsb = new QsBuilder();
    qsb.add("tahun", ls[0]);
    qsb.add("bulan", ls[1]);
    qsb.add("pilih", tanggal);

    newWindow("../library/calendar.dialog.php?" + qsb.createQs(), 'Kalender','550','400','resizable=1,scrollbars=1,status=0,toolbar=0'		);
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

    parent.content.location.href = "../jurnal/blank.php";
}

function changeKriteria()
{
    var kriteria = parseInt($("#kriteria").val());

    if (kriteria === 0)
        $("#keyword").css("visibility", "hidden");
    else
        $("#keyword").css("visibility", "visible");

    parent.content.location.href = "./blank.php";
}

async function showLaporan()
{
    let idTahunBuku = parseInt($("#idtahunbuku").val());
    if (idTahunBuku === 0)
    {
        alert("Data Tahun Buku belum tersedia");
        return;
    }

    let kriteria = parseInt($("#kriteria").val());
    if (kriteria > 0)
    {
        if (!Vldr.InputText("keyword", "Kata Kunci", 3))
            return;
    }

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("tahunbuku", "tahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("kriteria", "kriteria");
    qsb.add("namakriteria", $("#kriteria option:selected").text());
    qsb.addInput("keyword", "keyword");

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "carijurnal.content.php?" + qsb.createQs();
}

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

function showHelp()
{
    newWindow('../help/ju_cari.html', 'PencarianJurnalUmumHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0')
}