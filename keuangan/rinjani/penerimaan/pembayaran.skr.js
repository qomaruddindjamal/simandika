function showInfoSiswa()
{
    var qsb = new QsBuilder();
    qsb.addInput("nis", "userid");

    newWindow('../library/infosiswa.dialog.php?'+qsb.createQs(), 'InformasiSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0')
}

function showPembayaran(idpembayaran)
{
    var qsb = new QsBuilder();
    qsb.add("idpembayaran", idpembayaran);
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("penerimaan", "penerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nis", "userid");
    qsb.addInput("nama", "username");
    qsb.addInput("rekkas", "rekkas");
    qsb.addInput("rekpendapatan", "rekpendapatan");
    qsb.addInput("sendnotif", "sendnotif");

    var url = `pembayaran.skr.bayar.php?` + qsb.createQs();
    newWindow(url, "BayarSkr", 500, 470);
}

function reloadRiwayatSkr()
{
    var qsb = new QsBuilder();
    qsb.add("op", "riwayatskr");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nis", "userid");

    $("#dvRiwayatIuran").html("memuat .. ");

    $.ajax({
        url: "pembayaran.skr.ajax.php",
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

function cetakKuitansi(id)
{
    newWindow('kuitansi.skr.php?status=siswa&id='+id, 'CetakKuitansiSkr','360','650','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function cetakHalaman()
{
    var qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('pembayaran.skr.cetak.page.php?'+qsb.createQs(), 'CetakLainPage','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function getPageContent(section)
{
    if (section === "user")
    {
        if ($("#divSectionUser").length)
            return $("#divSectionUser").html();

        return "-";
    }
    else if (section === "payment")
    {
        if ($("#divSectionPayment").length)
            return $("#divSectionPayment").html();

        return "-";
    }
}

function informToast(type, message)
{
    showToast(message, 3000, type, "bottom");
}

