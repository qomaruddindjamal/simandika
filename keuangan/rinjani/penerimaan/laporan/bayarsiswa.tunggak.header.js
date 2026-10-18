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
        url: "bayarsiswa.kelas.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spTingkat.html(html);

            onTingkatChange();
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
        url: "bayarsiswa.kelas.header.ajax.php",
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

function onKategoriChange()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchpenerimaan");
    qsb.addInput("departemen", "departemen", "");
    qsb.addInput("idkategori", "kategori");

    let spPenerimaan = $("#spPenerimaan");
    spPenerimaan.html("memuat ..");

    showBlankPage();

    $.ajax({
        url: "bayarsiswa.kelas.header.ajax.php",
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
    });
}

function showBlankPage()
{
    parent.content.location.href = "blank.php";
}

function showPilihTanggal(tanggal)
{
    let ls = tanggal.split("-");

    let qsb = new QsBuilder();
    qsb.add("tahun", ls[0]);
    qsb.add("bulan", ls[1]);
    qsb.add("pilih", tanggal);

    newWindow("../../library/calendar.dialog.php?" + qsb.createQs(), 'Kalender3','550','400','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function acceptCalendar(tanggal)
{
    var ftanggal = dateutil_formatInaDate(tanggal);
    $("#ftanggal").val(ftanggal);
    $("#tanggal").val(tanggal);

    showBlankPage();
}

function showLaporan()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtingkat", "tingkat");
    qsb.add("namatingkat", $("#tingkat option:selected").text());
    qsb.addInput("idkelas", "kelas");
    qsb.add("namakelas", $("#kelas option:selected").text());
    qsb.addInput("idkategori", "kategori");
    qsb.add("namakategori", $("#kategori option:selected").text());
    qsb.addInput("idpenerimaan", "penerimaan");
    qsb.add("namapenerimaan", $("#penerimaan option:selected").text());
    qsb.addInput("tanggal", "tanggal");
    qsb.addInput("telat", "telat");
    qsb.add("page", 1);
    qsb.add("urut", "s.nis");

    parent.content.location.href = "blank.php?showwait=1";

    let idKategori = $("#kategori").val();
    if (idKategori === "JTT")
        parent.content.location.href = "bayarsiswa.tunggak.laporan.jtt.php?" + qsb.createQs();
    else
        parent.content.location.href = "bayarsiswa.tunggak.laporan.skr.php?" + qsb.createQs();
}

function showHelp()
{
    newWindow('../..//help/pn_bayarsiswatunggak.html', 'BayarSiswaTunggakHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}