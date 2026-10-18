$(document).ready(function () {
    $("#kode").focus();
});

function simpanLokasiDana()
{
    var isValid = Vldr.InputText("kode", "Kode Lokasi Data", 3) &&
        Vldr.InputText("nama", "Nama Lokasi Data", 3) &&
        Vldr.InputText("urutan", "Urutan Lokasi Data") &&
        Vldr.IsNumeric("urutan", "Urutan Lokasi Data") &&
        Vldr.IsInteger("urutan", "Urutan Lokasi Data");

    if (!isValid)
        return;

    if (!confirm("Data sudah benar?"))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.addInput("idlokasidana", "idlokasidana");
    qsb.addInput("kode", "kode");
    qsb.addInput("nama", "nama");
    qsb.addInput("urutan", "urutan");
    qsb.addInput("kelompok", "kelompok");
    qsb.addInput("keterangan", "keterangan");

    $.ajax({
        url: "lokasidana.dialog.ajax.php",
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