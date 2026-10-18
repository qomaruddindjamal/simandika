var dialogBox = null;

$(document).ready(function ()
{
    dialogBox = new DialogBox("#divDialog", 500, 350);

    $("#jumlah").focus();
});

function showPilihTanggal()
{
    let selDate = $("#tglJurnal").val();

    $("#txTglJurnal").datepicker({
        dateFormat: "yy-mm-dd",
        defaultDate: selDate,
        onSelect: function (date)
        {
            $("#tglJurnal").val(date);
            $("#txTglJurnal").val(dateutil_formatInaDate(date));
        }
    }).focus();
}

function showSelectPengguna()
{
    let qsb = new QsBuilder();
    qsb.add("op", "selectpengguna");
    qsb.addInput("departemen", "departemen");

    $.ajax({
        url: "laporan.rincian.edit.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dialogBox.show(response);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function pilihPengguna()
{
    if ($("#slpengguna option").length === 0)
        return;

    $("#pengguna").val($("#slpengguna").val());
    dialogBox.close();
}

function showSelectPenerima()
{
    let qsb = new QsBuilder();
    qsb.add("op", "selectpenerima");
    qsb.addInput("departemen", "departemen");

    $.ajax({
        url: "laporan.rincian.edit.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dialogBox.show(response);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function pilihPenerima()
{
    if ($("#slpenerima option").length === 0)
        return;

    $("#penerima").val($("#slpenerima").val());
    dialogBox.close();
}

function simpan()
{
    let jumlah = Rupiah.RupiahToNumber($.trim($("#jumlah").val()));
    let isValid = Vldr.IsNotEmpty(jumlah, "Jumlah Pengeluaran") &&
        Vldr.IsNumericValue(jumlah, "Jumlah Pengeluaran") &&
        Vldr.IsIntegerValue(jumlah, "Jumlah Pengeluaran") &&
        Vldr.IsNotNegative(jumlah, "Jumlah Pengeluaran") &&
        Vldr.IsNotZero(jumlah, "Jumlah Pengeluaran");
    if (!isValid)
    {
        $("#jumlah").focus();
        return;
    }

    isValid = Vldr.InputText("keperluan", "Keperluan", 5) &&
        Vldr.HasOption("rekkas", "Rek Kas") &&
        Vldr.InputText("pengguna", "Pengguna", 5) &&
        Vldr.InputText("alasan", "Alasan Perubahan Data", 5);

    if (!isValid)
        return;

    if (!confirm("Data sudah benar?"))
        return;

    let qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.add("jumlah", jumlah);
    qsb.addInput("idtransaksi", "idtransaksi");
    qsb.addInput("idjurnal", "idjurnal");
    qsb.addInput("keperluan", "keperluan");
    qsb.addInput("rekkas", "rekkas");
    qsb.addInput("rekbeban", "rekbeban");
    qsb.addInput("tanggal", "tglJurnal");
    qsb.addInput("pengguna", "pengguna");
    qsb.addInput("penerima", "penerima");
    qsb.addInput("keterangan", "keterangan");
    qsb.addInput("alasan", "alasan");

    let btSimpan = $("#btSimpan");
    btSimpan.prop("disabled", true);

    $.ajax({
        url: "laporan.rincian.edit.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                btSimpan.prop("disabled", false);
                alert(ls[1]);
                return;
            }

            opener.refresh();
            window.close();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })


}