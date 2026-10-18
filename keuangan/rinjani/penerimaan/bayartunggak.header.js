function onChangeDept()
{
    let qsb = new QsBuilder();
    qsb.add("op", "tahunbuku");
    qsb.addInput("departemen", "departemen");

    let spTahunBuku = $("#spTahunBuku");
    spTahunBuku.html("memuat ..");

    showBlankPage();

    $.ajax({
        url: "bayartunggak.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spTahunBuku.html(html);

            onChangeKategori();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}

function onChangeKategori()
{
    let qsb = new QsBuilder();
    qsb.add("op", "penerimaan");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("kategori", "kategori");

    let spPenerimaan = $("#spPenerimaan");
    spPenerimaan.html("memuat ..");

    showBlankPage();

    $.ajax({
        url: "bayartunggak.header.ajax.php",
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

function showContent()
{
    if (!Vldr.HasOption("departemen", "Departemen") ||
        !Vldr.HasOption("tahunbuku", "Tahun Buku") ||
        !Vldr.HasOption("penerimaan", "Jenis Penerimaan"))
        return;

    let idKategori = $("#kategori").val();

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "tahunbuku");
    qsb.add("namatahunbuku", $("#tahunbuku option:selected").text());
    qsb.addInput("idkategori", "kategori");
    qsb.add("namakategori", $("#kategori option:selected").text());
    qsb.addInput("idpenerimaan", "penerimaan");
    qsb.add("namapenerimaan", $("#penerimaan option:selected").text());
    if (idKategori === "JTT")
        qsb.add("urut", "s.nis");
    else
        qsb.add("urut", "cs.nopendaftaran");

    parent.content.location.href = "bayartunggak.content.php?" + qsb.createQs();
}