$(document).ready(function ()
{
    if ($("#tableRiwayat").length)
        Tables("tableRiwayat", 1, 0);

    if ($("#spSisaPembayaran").length)
        $("#spSisaPembayaran").html($("#sisapembayaran").val());
});

function showInfoCalonSiswa()
{
    var qsb = new QsBuilder();
    qsb.add("nic", $("#userid").val());

    newWindow('../library/infocalonsiswa.dialog.php?'+qsb.createQs(), 'InformasiCalonSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function aturBesarJttCalon(idBesarJttCalon)
{
    var qsb = new QsBuilder();
    qsb.add("idbesarjttcalon", idBesarJttCalon);
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("penerimaan", "penerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nic", "userid");
    qsb.addInput("nama", "username");
    qsb.addInput("idcalonsiswa", "idcalonsiswa");

    var url = `pembayaran.cswjb.dialog.php?` + qsb.createQs();
    newWindow(url, "AturBesarJttCalon", 500, 450);
}

function reloadBesarJttCalon()
{
    var qsb = new QsBuilder();
    qsb.add("op", "besarjttcalon");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nic", "userid");
    qsb.addInput("idcalonsiswa", "idcalonsiswa");

    $("#dvBesarJttInfo").html("memuat .. ");
    $("#dvRiwayatJtt").html("");

    $.ajax({
        url: "pembayaran.cswjb.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (result)
        {
            $("#dvBesarJttInfo").html(result);

            reloadRiwayatJttCalon();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })

}

function showPembayaranCalon(idpembayaran)
{
    var qsb = new QsBuilder();
    qsb.add("idpembayaran", idpembayaran);
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("penerimaan", "penerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("idbesarjtt", "idbesarjtt");
    qsb.addInput("nic", "userid");
    qsb.addInput("nama", "username");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idcalonsiswa", "idcalonsiswa");
    qsb.addInput("rekkas", "rekkas");
    qsb.addInput("jcicilan", "jcicilan");
    qsb.addInput("sendnotif", "sendnotif");

    var url = `pembayaran.cswjb.bayar.php?` + qsb.createQs();
    newWindow(url, "BayarJttCalon", 500, 550);
}

function reloadRiwayatJttCalon()
{
    $("#dvRiwayatJtt").html("memuat ..");

    var qsb = new QsBuilder();
    qsb.add("op", "riwayatjttcalon");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nic", "userid");
    qsb.addInput("idcalonsiswa", "idcalonsiswa");

    $.ajax({
        url: "pembayaran.cswjb.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (result)
        {
            $("#dvRiwayatJtt").html(result).hide().fadeIn(400);

            if ($("#spSisaPembayaran").length)
                $("#spSisaPembayaran").html($("#sisapembayaran").val());
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function cetakKuitansi(id)
{
    newWindow('kuitansi.wjb.php?status=calon&id='+id, 'CetakKuitansiCsWjb','360','650','resizable=1,scrollbars=1,status=0,toolbar=0'		)
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

function cetakHalaman()
{
    var qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('pembayaran.cswjb.cetak.page.php?'+qsb.createQs(), 'CetakKuitansiCsWjbPage','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function informToast(type, message)
{
    showToast(message, 3000, type, "bottom");
}