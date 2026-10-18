$(document).ready(function () {
   $("#besar").focus();
});

function simpanBesarJtt()
{
    var isValid = Vldr.InputText('besar', 'Besar Pembayaran') &&
                  Vldr.InputText('cicilan', 'Besar Cicilan');
    if (!isValid) return;

    var idBesarJtt = $("#idbesarjtt").val();
    if (parseInt(idBesarJtt) > 0)
    {
        isValid = Vldr.InputText('alasan', 'Alasan Perubahan Data');
        if (!isValid) return;
    }

    var besar = Rupiah.RupiahToNumber($.trim($("#besar").val()));
    isValid = Vldr.IsNotEmpty(besar, "Besar Pembayaran") &&
              Vldr.IsNumericValue(besar, "Besar Pembayaran") &&
              Vldr.IsIntegerValue(besar, "Besar Pembayaran") &&
              Vldr.IsNotNegative(besar, "Besar Pembayaran") ;
    if (!isValid)
    {
        $("#besar").focus();
        return;
    }

    var cicilan = Rupiah.RupiahToNumber($.trim($("#cicilan").val()));
    isValid = Vldr.IsNotEmpty(cicilan, "Besar Cicilan") &&
              Vldr.IsNumericValue(cicilan, "Besar Cicilan") &&
              Vldr.IsIntegerValue(cicilan, "Besar Cicilan") &&
              Vldr.IsNotNegative(cicilan, "Besar Cicilan");
    if (!isValid)
    {
        $("#cicilan").focus();
        return;
    }

    if (parseInt(besar) < parseInt(cicilan))
    {
        alert("Besar pembayaran harus lebih besar daripada cicilan");
        $("#besar").focus();
        return;
    }

    if (!confirm("Data sudah benar?"))
        return;

    $("#btSimpan").prop('disabled', true);

    var alasan = "";
    if (parseInt(idBesarJtt) > 0)
        alasan = $.trim($("#alasan").val());

    var keterangan = $.trim($("#keterangan").val());

    var qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.add("idbesarjtt", idBesarJtt);
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("penerimaan", "penerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nis", "nis");
    qsb.addInput("nama", "nama");
    qsb.add("besar", besar);
    qsb.add("cicilan", cicilan);
    qsb.add("keterangan", keterangan);
    qsb.add("alasan", alasan);

    let cicilanpertama = 0;
    if ($("#cicilanpertama").length !== 0)
        cicilanpertama = $("#cicilanpertama").is(":checked") ? 1 : 0;
    qsb.add("cicilanpertama", cicilanpertama);

    let btSimpan = $("#btSimpan");
    let spInfo = $("#spInfo");

    btSimpan.prop("disabled", true);
    spInfo.css("color", "blue");
    spInfo.html("memuat .. ");

    $.ajax({
        url: "pembayaran.jtt.dialog.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            var ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                btSimpan.prop("disabled", false);
                spInfo.css("color", "red");
                spInfo.html(ls[1]);

                opener.informToast("error", ls[1]);
                alert(ls[1]);
                return;
            }

            opener.reloadBesarJtt();
            opener.informToast("success", "Berhasil mendata besar pembayaran");
            window.close();
        },
        error: function (xhr)
        {
            $("#btSimpan").prop('disabled', false);
            alert(xhr.responseText);
        }
    })
}