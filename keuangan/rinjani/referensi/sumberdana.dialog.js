$(document).ready(function () {
    $("#kode").focus();
});

function simpanSumberDana()
{
    var isValid = Vldr.InputText("kode", "Kode Sumber Data", 3) &&
                  Vldr.InputText("nama", "Nama Sumber Data", 3) &&
                  Vldr.InputText("urutan", "Urutan Sumber Data") &&
                  Vldr.IsNumeric("urutan", "Urutan Sumber Data") &&
                  Vldr.IsInteger("urutan", "Urutan Sumber Data");

    if (!isValid)
        return;

    if (!confirm("Data sudah benar?"))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.addInput("idsumberdana", "idsumberdana");
    qsb.addInput("kode", "kode");
    qsb.addInput("nama", "nama");
    qsb.addInput("urutan", "urutan");
    qsb.addInput("kelompok", "kelompok");
    qsb.addInput("keterangan", "keterangan");

    $.ajax({
        url: "sumberdana.dialog.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            var ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
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