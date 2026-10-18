$(document).ready(function ()
{
    $("#besar").focus();
});

function simpanBesarJttCalon()
{
    var isValid = Vldr.InputText('besar', 'Besar Pembayaran') &&
                  Vldr.InputText('cicilan', 'Besar Cicilan');
    if (!isValid) return;

    var idBesarJttCalon = $("#idbesarjttcalon").val();
    if (parseInt(idBesarJttCalon) > 0)
    {
        isValid = Vldr.InputText('alasan', 'Alasan Perubahan Data');
        if (!isValid) return;
    }

    var besar = rupiahToNumber($.trim($("#besar").val()));
    isValid = Vldr.IsNotEmpty(besar, "Besar Pembayaran") &&
        Vldr.IsNumericValue(besar, "Besar Pembayaran") &&
        Vldr.IsIntegerValue(besar, "Besar Pembayaran") &&
        Vldr.IsNotNegative(besar, "Besar Pembayaran") ;
    if (!isValid)
    {
        $("#besar").focus();
        return;
    }

    var cicilan = rupiahToNumber($.trim($("#cicilan").val()));
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

    let btSimpan = $("#btSimpan");
    let spInfo = $("#spInfo");

    btSimpan.prop('disabled', true);
    spInfo.css("color", "blue").html("memuat ..");

    let qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.add("idbesarjttcalon", idBesarJttCalon);
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("penerimaan", "penerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("nic", "nic");
    qsb.addInput("nama", "nama");
    qsb.addInput("idcalonsiswa", "idcalonsiswa");
    qsb.addInput("keterangan", "keterangan");
    qsb.addInput("alasan", "alasan");
    qsb.add("besar", besar);
    qsb.add("cicilan", cicilan);

    let cicilanpertama = 0;
    if ($("#cicilanpertama").length !== 0)
        cicilanpertama = $("#cicilanpertama").is(":checked") ? 1 : 0;
    qsb.add("cicilanpertama", cicilanpertama);

    $.ajax({
        url: "pembayaran.cswjb.dialog.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                btSimpan.prop('disabled', false);
                spInfo.css("color", "red").html(ls[1]);

                opener.informToast("error", ls[1]);
                alert(ls[1]);
                return;
            }

            opener.informToast("success", "Berhasil mendata besar pembayaran");
            opener.reloadBesarJttCalon();
            window.close();
        },
        error: function (xhr)
        {
            $("#btSimpan").prop('disabled', false);
            alert(xhr.responseText);
        }
    })
}