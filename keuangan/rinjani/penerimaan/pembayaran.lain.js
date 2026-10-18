function showPembayaran(idpembayaran)
{
    var qsb = new QsBuilder();
    qsb.add("idpembayaran", idpembayaran);
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("penerimaan", "penerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("rekkas", "rekkas");
    qsb.addInput("rekpendapatan", "rekpendapatan");

    var url = `pembayaran.lain.bayar.php?` + qsb.createQs();
    newWindow(url, "BayarLain", 580, 450);
}

function reloadRiwayatLain()
{
    var qsb = new QsBuilder();
    qsb.add("op", "riwayatlain");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");

    $("#dvRiwayatIuran").html("memuat .. ");

    $.ajax({
        url: "pembayaran.lain.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (result)
        {
            $("#dvRiwayatIuran").html(result).hide().fadeIn(400);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function getPageContent(section)
{
    if (section === "payment")
    {
        if ($("#divSectionPayment").length)
            return $("#divSectionPayment").html();

        return "-";
    }
}

function cetakHalaman()
{
    var qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('pembayaran.lain.cetak.page.php?'+qsb.createQs(), 'CetakLainPage','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function cetakKuitansi(id)
{
    newWindow('kuitansi.lain.php?id='+id, 'CetakKuitansiLain','360','650','resizable=1,scrollbars=1,status=0,toolbar=0');
}