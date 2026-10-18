$(document).ready(function () {
    $("#passwordlama").focus();
});

function simpan()
{
    let passlama = $.trim($("#passwordlama").val());
    let passbaru = $.trim($("#passwordbaru").val());
    let konfirmasi = $.trim($("#konfirmasi").val());

    if (passlama.length === 0)
    {
        alert("Password belum diisi!");
        $("#passwordlama").focus();
        return;
    }

    if (passbaru.length < 5)
    {
        alert("Password baru minimal 5 karakter!");
        $("#passwordbaru").focus();
        return;
    }

    if (passbaru !== konfirmasi)
    {
        alert("Password tidak sama");
        $("#passwordbaru").focus();
        return;
    }

    if (!confirm("Ganti password?"))
        return;

    let btSimpan = $("#btSimpan");
    let btTutup = $("#btTutup");
    let spInfo = $("#spInfo");

    btSimpan.prop("disabled", true);
    btTutup.prop("disabled", true);
    spInfo.css("color", "blue").html("memuat ..");

    let qsb = new QsBuilder();
    qsb.add("op", "ganti");
    qsb.addInput("login", "login");
    qsb.addInput("nip", "nip");
    qsb.addInput("passwordlama", "passwordlama");
    qsb.addInput("passwordbaru", "passwordbaru");
    qsb.addInput("konfirmasi", "konfirmasi");

    $.ajax({
        url: "changepwd.dialog.ajax.php",
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

            alert("Password telah berubah");
            window.close();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}