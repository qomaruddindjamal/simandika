$(document).ready(function () {
    $("#nama").focus();
});

function simpanKategori()
{
    if (!Vldr.InputText("nama", "Kategori", 5))
        return;

    let qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.addInput("id", "id");
    qsb.addInput("nama", "nama");
    qsb.addInput("keterangan", "keterangan");

    let btSimpan = $("#btSimpan");
    let btTutup = $("#btTutup");
    let spInfo = $("#spInfo");

    btSimpan.prop("disabled", true);
    btTutup.prop("disabled", true);
    spInfo.css("color", "blue").html("memuat ..");

    $.ajax({
        url: "kategori.dialog.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                alert(ls[1]);

                btSimpan.prop("disabled", false);
                btTutup.prop("disabled", false);
                spInfo.css("color", "red").html(ls[1]);

                return;
            }

            opener.refresh();
            window.close();
        }
    })
}