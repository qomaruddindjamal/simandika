$(document).ready(function () {
    $("#jbayar").focus();
});

function simpanEditTabungan()
{
    let jbayar = Rupiah.RupiahToNumber($.trim($("#jbayar").val()));
    let isValid = Vldr.IsNotEmpty(jbayar, "Jumlah") &&
        Vldr.IsNumericValue(jbayar, "Jumlah") &&
        Vldr.IsIntegerValue(jbayar, "Jumlah") &&
        Vldr.IsNotNegative(jbayar, "Jumlah");
    if (!isValid)
    {
        $("#jbayar").focus();
        return;
    }

    let action = $("#action").val();
    let kodeLokasi = "";
    if (action === "tarik")
    {
        let jsonInfo = atob($("#lokasidanatarik").val());
        let lsInfo = JSON.parse(jsonInfo);
        kodeLokasi = lsInfo[0];
        let saldoLokasi = parseInt(lsInfo[1]);

        if (jbayar > saldoLokasi)
        {
            alert("Saldo tabungan tidak muncukupi untuk penarikan");
            $("#jbayar").focus();
            return;
        }
    }
    else 
    {
        kodeLokasi = $("#lokasidana").val();
    }

    if (!Vldr.InputText("alasan", "Alasan Perubahan Data", 10))
        return;

    let btSimpan = $("#btSimpan");
    let btTutup = $("#btTutup");
    let spInfo = $("#spInfo");

    btSimpan.prop("disabled", true);
    btTutup.prop("disabled", true);
    spInfo.html("memuat ..");

    let qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.addInput("idpembayaran", "idpembayaran");
    qsb.addInput("nis", "nis");
    qsb.addInput("idjurnal", "idjurnal");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.addInput("debet", "debet");
    qsb.addInput("kredit", "kredit");
    qsb.addInput("action", "action");
    qsb.addInput("rekkastrans", "rekkastrans");
    qsb.addInput("rekutang", "rekutang");
    qsb.add("jbayar", jbayar);
    qsb.addInput("keterangan", "keterangan");
    qsb.addInput("alasan", "alasan");
    qsb.addInput("sumberdana", "sumberdana");
    qsb.add("lokasidana", kodeLokasi);

    $.ajax({
        url: "transaksi.tabungan.edit.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            console.log(json);


            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                alert(ls[1]);

                btSimpan.prop("disabled", false);
                btTutup.prop("disabled", false);
                spInfo.css("color", "red").html(ls[1]);

                return;
            }

            window.close();

            opener.successToast();
            
            opener.refresh();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function fetchLokasiPengambilan()
{
    let qsb = new QsBuilder();
    qsb.add("op", "lokasiambil");
    qsb.addInput("nis", "nis");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.addInput("deflokasidana", "deflokasidana");

    let spPengambilan = $("#spPengambilan");
    spPengambilan.html("memaut ..");

    $.ajax({
        url: "transaksi.tabungan.edit.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spPengambilan.html(html).hide().fadeIn(400);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}