function showInfoCalonSiswa()
{
    var qsb = new QsBuilder();
    qsb.add("nic", $("#userid").val());

    newWindow('../library/infocalonsiswa.dialog.php?'+qsb.createQs(), 'InformasiCalonSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function showPembayaran(idpembayaran)
{
    var qsb = new QsBuilder();
    qsb.add("idpembayaran", idpembayaran);
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("penerimaan", "penerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nic", "userid");
    qsb.addInput("idcalonsiswa", "idcalonsiswa");
    qsb.addInput("nama", "username");
    qsb.addInput("rekkas", "rekkas");
    qsb.addInput("rekpendapatan", "rekpendapatan");
    qsb.addInput("sendnotif", "sendnotif");

    var url = `pembayaran.csskr.bayar.php?` + qsb.createQs();
    newWindow(url, "BayarSkrCalonSiswa", 500, 450);
}

function reloadRiwayatCsSkr()
{
    var qsb = new QsBuilder();
    qsb.add("op", "riwayatskr");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nic", "userid");
    qsb.addInput("idcalonsiswa", "idcalonsiswa");

    $("#dvRiwayatIuran").html("memuat .. ");

    $.ajax({
        url: "pembayaran.csskr.ajax.php",
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
    newWindow('kuitansi.skr.php?status=calon&id='+id, 'CetakKuitansiSkr','360','650','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function cetakHalaman()
{
    var qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('pembayaran.csskr.cetak.page.php?'+qsb.createQs(), 'CetakLainPage','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
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