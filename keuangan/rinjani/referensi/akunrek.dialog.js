$(document).ready(function() {
   $("#kode").focus();
});

simpanRekAkun = function ()
{
    var isValid = Vldr.InputText("kode", "Kode Rekening") &&
                  Vldr.IsInteger("kode", "Kode Rekening") &&
                  Vldr.InputText("nama", "Nama Rekening");

    if (!isValid)
        return;

    if (!confirm("Data sudah benar?"))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.addInput("idrekakun", "idrekakun");
    qsb.addInput("kategori", "kategori");
    qsb.addInput("kode", "kode");
    qsb.addInput("nama", "nama");
    qsb.addInput("keterangan", "keterangan");

    $.ajax({
        url: "akunrek.dialog.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            var ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                alert(ls[1]);
                showToast(ls[1], 3000, "error", "bottom");
                return;
            }

            opener.afterSave();
            window.close();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
};