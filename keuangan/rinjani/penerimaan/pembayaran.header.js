function change_kate()
{
    var qsb = new QsBuilder();
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("departemen", "departemen");

    document.location.href = "pembayaran.header.php?" + qsb.createQs();
    parent.contentblank.location.href = "blank.php";
}

function change_dep()
{
    change_kate();
}

function change_penerimaan()
{
    parent.contentblank.location.href = "blank.php";
}

function show_pembayaran()
{
    var isValid = Vldr.HasOption("departemen", "Departemen") &&
                  Vldr.HasOption("idkategori", "Kategori Penerimaan") &&
                  Vldr.HasOption("idpenerimaan", "Jenis Penerimaan") &&
                  Vldr.InputText("tahunbuku", "Tahun Buku");

    if (!isValid)
        return;

    var qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");

    var idKategori = $("#idkategori").val();
    if (idKategori === "LNN")
    {
        parent.contentblank.location.href = "pembayaran.lain.php?" + qsb.createQs();
    }
    else
    {
        parent.contentblank.location.href = "pembayaran.content.php?" + qsb.createQs();
    }

}

function showHelp()
{
    newWindow('../help/pn_singlepayment.html', 'SinglePaymentHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}
