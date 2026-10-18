$(document).ready(function ()
{
    if ($("#tableRiwayat").length)
        Tables("tableRiwayat", 1, 0);

    if ($("#spSisaPembayaran").length)
        $("#spSisaPembayaran").html($("#sisapembayaran").val());
});

function aturBesarJtt(idBesarJtt)
{
    var qsb = new QsBuilder();
    qsb.add("idbesarjtt", idBesarJtt);
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("penerimaan", "penerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nis", "userid");
    qsb.addInput("nama", "username");

    var url = `pembayaran.jtt.dialog.php?` + qsb.createQs();
    newWindow(url, "AturBesarJtt", 500, 450);
}

function reloadRiwayatJtt()
{
    $("#dvRiwayatJtt").html("memuat ..");

    var qsb = new QsBuilder();
    qsb.add("op", "riwayatjtt");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nis", "userid");

    $.ajax({
        url: "pembayaran.jtt.ajax.php",
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

function reloadBesarJtt()
{
    var qsb = new QsBuilder();
    qsb.add("op", "besarjtt");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nis", "userid");

    $("#dvBesarJttInfo").html("memuat .. ");
    $("#dvRiwayatJtt").html("");

    $.ajax({
        url: "pembayaran.jtt.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (result)
        {
            $("#dvBesarJttInfo").html(result);

            reloadRiwayatJtt();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })

}

function showPembayaran(idpembayaran)
{
    var qsb = new QsBuilder();
    qsb.add("idpembayaran", idpembayaran);
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("penerimaan", "penerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("idbesarjtt", "idbesarjtt");
    qsb.addInput("nis", "userid");
    qsb.addInput("nama", "username");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("rekkas", "rekkas");
    qsb.addInput("jcicilan", "jcicilan");
    qsb.addInput("sendnotif", "sendnotif");

    var url = `pembayaran.jtt.bayar.php?` + qsb.createQs();
    newWindow(url, "BayarJtt", 500, 550);
}

function cetakKuitansi(id)
{
    newWindow('kuitansi.wjb.php?status=siswa&id='+id, 'CetakKuitansi','360','650','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function cetakHalaman()
{
    var qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('pembayaran.jtt.cetak.page.php?'+qsb.createQs(), 'CetakKuitansiPage','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function showInfoSiswa()
{
    var qsb = new QsBuilder();
    qsb.add("nis", $("#userid").val());

    newWindow('../library/infosiswa.dialog.php?'+qsb.createQs(), 'InformasiSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
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