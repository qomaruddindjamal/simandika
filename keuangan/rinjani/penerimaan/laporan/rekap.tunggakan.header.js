function onChangeDept()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchtingkat");
    qsb.addInput("departemen", "departemen");

    let spTingkat = $("#spTingkat");
    let spKelas = $("#spKelas");

    spTingkat.html("memuat ..");
    spKelas.html("memuat ..");

    showBlankPage();

    $.ajax({
        url: "rekap.tunggakan.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spTingkat.html(html);

            onTingkatChange();
            fetchTahunBuku();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}

function onTingkatChange()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchkelas");
    qsb.addInput("idtingkat", "tingkat");

    let spKelas = $("#spKelas");
    spKelas.html("memuat ..");

    showBlankPage();

    $.ajax({
        url: "rekap.tunggakan.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spKelas.html(html);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}

function fetchTahunBuku()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchtahunbuku");
    qsb.addInput("departemen", "departemen");

    let spTahunBuku = $("#spTahunBuku");
    spTahunBuku.html("memuat ..");

    showBlankPage();

    $.ajax({
        url: "rekap.tunggakan.header.ajax.php",
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
        !Vldr.HasOption("tingkat", "Tingkat") ||
        !Vldr.HasOption("kelas", "Kelas") ||
        !Vldr.HasOption("tahunbuku", "Tahun Buku"))
        return;

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtingkat", "tingkat");
    qsb.add("namatingkat", $("#tingkat option:selected").text());
    qsb.addInput("idkelas", "kelas");
    qsb.add("namakelas", $("#kelas option:selected").text());
    qsb.addInput("idtahunbuku", "tahunbuku");
    qsb.add("namatahunbuku", $("#tahunbuku option:selected").text());

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "rekap.tunggakan.laporan.php?" + qsb.createQs();
}

function pause(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function showHelp()
{
    newWindow('../../help/pn_lapsisa.html', 'LaporanTunggakanSiswaHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}