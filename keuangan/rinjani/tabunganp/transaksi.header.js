function onChangeDept()
{
    let qsb = new QsBuilder();
    qsb.add("op", "tahunbuku");
    qsb.addInput("departemen", "departemen");

    let spTahunBuku = $("#spTahunBuku");
    let spTabungan = $("#spTabungan");

    spTahunBuku.html("memuat ..");
    spTabungan.html("memuat ..");

    $.ajax({
        url: "transaksi.header.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            spTahunBuku.html(response);

            qsb = new QsBuilder();
            qsb.add("op", "tabungan");
            qsb.addInput("departemen", "departemen");

            $.ajax({
                url: "transaksi.header.ajax.php",
                method: "POST",
                data: qsb.createQs(),
                success: function (response)
                {
                    spTabungan.html(response);
                },
                error: function (xhr)
                {
                    alert(xhr.responseText);
                }
            })
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function showBlankPage()
{
    parent.content.location.href = "blank.php";
}

function showLaporan()
{
    if ($("#departemen option").length === 0)
        return;

    if ($("#tabungan option").length === 0)
        return;

    let idTahunBuku = parseInt($("#idtahunbuku").val());
    if (idTahunBuku === 0)
        return;

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "tahunbuku");
    qsb.addInput("jsontabungan", "tabungan");

    parent.content.location.href = "transaksi.content.php?" + qsb.createQs();
}

function showHelp()
{
    newWindow('../help/tp_setoran.html', 'SetoranTarikanTabunganPegawaiHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0')
}
