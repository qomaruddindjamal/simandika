$(document).ready(function () {
    $("#jcicilan").focus();
});

function simpanBayarCsWjb()
{
    var temp = Rupiah.RupiahToNumber($("#jcicilan").val());
    var isValid = Vldr.IsNotEmpty(temp, "Jumlah Cicilan") &&
        Vldr.IsNumericValue(temp, "Jumlah Cicilan") &&
        Vldr.IsIntegerValue(temp, "Jumlah Cicilan") &&
        Vldr.IsNotNegative(temp, "Jumlah Cicilan");
    if (!isValid)
    {
        $("#jcicilan").focus();
        return false;
    }
    var jcicilan = parseInt(temp);

    temp = Rupiah.RupiahToNumber($("#jdiskon").val());
    isValid = Vldr.IsNotEmpty(temp, "Jumlah Diskon") &&
        Vldr.IsNumericValue(temp, "Jumlah Diskon") &&
        Vldr.IsIntegerValue(temp, "Jumlah Diskon") &&
        Vldr.IsNotNegative(temp, "Jumlah Diskon");
    if (!isValid)
    {
        $("#jdiskon").focus();
        return false;
    }
    var jdiskon = parseInt(temp);

    if (jdiskon > jcicilan)
    {
        alert("Jumlah diskon haruslah lebih kecil daripada jumlah cicilan");
        $("#jdiskon").focus();
        return false;
    }

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
    qsb.addInput("idpembayaran", "idpembayaran");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("penerimaan", "penerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("idbesarjtt", "idbesarjtt");
    qsb.addInput("nic", "nic");
    qsb.addInput("nama", "nama");
    qsb.addInput("idcalonsiswa", "idcalonsiswa");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("rekkas", "rekkas");
    qsb.addInput("sumberdana", "sumberdana");
    qsb.addInput("kcicilan", "kcicilan");
    //qsb.addInput("smsinfo", "smsinfo");
    qsb.add("jcicilan", jcicilan);
    qsb.add("jdiskon", jdiskon);
    if (parseInt(idPembayaran) > 0)
        qsb.addInput("alasan", "alasan");

    let sendnotif = $("#sendnotif").prop("checked") ? 1 : 0;
    qsb.add("sendnotif", sendnotif);

    let btSimpan = $("#btSimpan");
    let spInfo = $("#spInfo");

    btSimpan.prop("disabled", true);
    spInfo.css("color", "blue").html("memuat ..");

    $.ajax({
        url: "pembayaran.cswjb.bayar.ajax.php",
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
            opener.reloadBesarJttCalon();
            window.close();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function hitungJumlahBayar()
{
    var temp = Rupiah.RupiahToNumber($("#jcicilan").val());
    var isValid = Vldr.IsNotEmpty(temp, "") &&
        Vldr.IsNumericValue(temp, "") &&
        Vldr.IsIntegerValue(temp, "") &&
        Vldr.IsNotNegative(temp, "");
    if (!isValid)
    {
        $("#jbayar").val("INVALID");
        return;
    }

    var jcicilan = parseInt(temp);

    temp = Rupiah.RupiahToNumber($("#jdiskon").val());
    isValid = Vldr.IsNotEmpty(temp, "") &&
        Vldr.IsNumericValue(temp, "") &&
        Vldr.IsIntegerValue(temp, "") &&
        Vldr.IsNotNegative(temp, "");
    if (!isValid)
    {
        $("#jbayar").val("INVALID");
        return;
    }

    var jdiskon = parseInt(temp);
    if (jdiskon > jcicilan)
    {
        $("#jbayar").val("INVALID");
        return;
    }

    var jbayar = jcicilan - jdiskon;
    $("#jbayar").val(Rupiah.NumberToRupiah(jbayar));
}