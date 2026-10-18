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
        url: "laporankelas.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spTingkat.html(html);

            onTingkatChange();

            fetchSelectJenisTabunganSiswa();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}

function fetchSelectJenisTabunganSiswa()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchtabungan");
    qsb.addInput("departemen", "departemen");

    let spTabungan = $("#spTabungan");
    spTabungan.html("memuat ..");

    $.ajax({
        url: "laporankelas.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spTabungan.html(html);
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
        url: "laporankelas.header.ajax.php",
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

function showBlankPage()
{
    parent.content.location.href = "blank.php";
}

async function showLaporan()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtingkat", "tingkat");
    qsb.add("namatingkat", $("#tingkat option:selected").text());
    qsb.addInput("idkelas", "kelas");
    qsb.add("namakelas", $("#kelas option:selected").text());
    qsb.addInput("idtabungan", "tabungan");
    qsb.add("namatabungan", $("#tabungan option:selected").text());
    qsb.add("urut", "s.nama");
    qsb.add("page", 1);

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "laporankelas.content.php?" + qsb.createQs();
}

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

function showHelp()
{
    newWindow('../help/ts_lapkelas.html', 'LapTabunganSiswaKelasHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0')
}