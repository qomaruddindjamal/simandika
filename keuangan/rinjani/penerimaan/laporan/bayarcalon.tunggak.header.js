function onChangeDept()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchproses");
    qsb.addInput("departemen", "departemen");

    let spProses = $("#spProses");
    let spKelompok = $("#spKelompok");

    spProses.html("memuat ..");
    spKelompok.html("memuat ..");

    showBlankPage();

    $.ajax({
        url: "bayarcalon.kelompok.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spProses.html(html);

            onProsesChange();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}

function onProsesChange()
{
    let qsb = new QsBuilder();
    qsb.add("op", "fetchkelompok");
    qsb.addInput("idproses", "proses");

    let spKelompok = $("#spKelompok");
    spKelompok.html("memuat ..");

    showBlankPage();

    $.ajax({
        url: "bayarcalon.kelompok.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spKelompok.html(html);
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
        url: "bayarcalon.kelompok.header.ajax.php",
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
    qsb.addInput("idproses", "proses");
    qsb.add("namaproses", $("#proses option:selected").text());
    qsb.addInput("idkelompok", "kelompok");
    qsb.add("namakelompok", $("#kelompok option:selected").text());
    qsb.addInput("idkategori", "kategori");
    qsb.add("namakategori", $("#kategori option:selected").text());
    qsb.addInput("idpenerimaan", "penerimaan");
    qsb.add("namapenerimaan", $("#penerimaan option:selected").text());
    qsb.addInput("tanggal", "tanggal");
    qsb.addInput("telat", "telat");
    qsb.add("page", 1);
    qsb.add("urut", "c.nopendaftaran");

    let idKategori = $("#kategori").val();
    if (idKategori === "CSWJB")
        parent.content.location.href = "bayarcalon.tunggak.laporan.cswjb.php?" + qsb.createQs();
    else
        parent.content.location.href = "bayarcalon.tunggak.laporan.csskr.php?" + qsb.createQs();
}

function showHelp()
{
    newWindow('../../help/pn_bayarcalontunggak.html', 'BayarCalonTunggakHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}