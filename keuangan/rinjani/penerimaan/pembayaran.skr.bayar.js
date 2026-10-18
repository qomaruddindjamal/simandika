$(document).ready(function ()
{
    $("#jumlah").focus();
});

function simpanBayarSkr()
{
    var temp = Rupiah.RupiahToNumber($("#jumlah").val());
    var isValid = Vldr.IsNotEmpty(temp, "Jumlah Pembayaran") &&
        Vldr.IsNumericValue(temp, "Jumlah Pembayaran") &&
        Vldr.IsIntegerValue(temp, "Jumlah Pembayaran") &&
        Vldr.IsNotNegative(temp, "Jumlah Pembayaran") &&
        Vldr.IsNotZero(temp, "Jumlah Pembayaran");

    if (!isValid)
    {
        $("#jumlah").focus();
        return false;
    }
    var jumlah = parseInt(temp);

    var idPembayaran = $("#idpembayaran").val();
    if (parseInt(idPembayaran) > 0)
    {
        var alasan = $.trim($("#alasan").val());
        if (alasan.length === 0)
        {
            alert("Alasan perubahan data belum diisikan");
            $("#alasan").focus();
            return false;
        }
    }

    if ($("#rekkas option").length === 0)
    {
        alert("Belum tersedia data kode rekening HARTA");
        $("#rekkas").focus();
        return false;
    }

    if (!confirm("Data sudah benar?"))
        return false;

    var qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idpembayaran", "idpembayaran");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("penerimaan", "penerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nis", "nis");
    qsb.addInput("nama", "nama");
    qsb.addInput("rekkas", "rekkas");
    qsb.addInput("rekpendapatan", "rekpendapatan");
    qsb.addInput("sumberdana", "sumberdana");
    qsb.addInput("keterangan", "keterangan");
    qsb.add("jumlah", jumlah);

    if (parseInt(idPembayaran) > 0)
    {
        qsb.addInput("idjurnal", "idjurnal");
        qsb.addInput("origjumlah", "origjumlah");
        qsb.addInput("origrekkas", "origrekkas");
        qsb.addInput("alasan", "alasan");
    }

    let sendnotif = $("#sendnotif").prop("checked") ? 1 : 0;
    qsb.add("sendnotif", sendnotif);

    let btSimpan = $("#btSimpan");
    let spInfo = $("#spInfo");

    btSimpan.prop("disabled", true);
    spInfo.css("color", "blue").html("memuat ..");

    $.ajax({
        url: "pembayaran.skr.bayar.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            var ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                btSimpan.prop("disabled", false);
                spInfo.css("color", "red").html(ls[1]);

                opener.informToast("error", ls[1]);
                alert(ls[1]);
                return;
            }

            opener.informToast("success", "Berhasil menerima pembayaran");
            opener.reloadRiwayatSkr();
            window.close();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }

    })
}