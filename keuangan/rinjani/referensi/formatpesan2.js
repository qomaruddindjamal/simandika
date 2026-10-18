var helpBox = null;

$(document).ready(function ()
{
    helpBox = new DialogBox("#divHelpDialog", 500, 500);

    fetchPesanNotifikasi();
});

function showHelp()
{
    $.ajax({
        url: "../help/rf_formatpesan.html",
        success: function (content)
        {
            helpBox.show(content);
        },
        error: function(xhr)
        {
            alert(xhr.responseText);
        }
    })
}

fetchPesanNotifikasi = function()
{
    if ($("#departemen option").length === 0)
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "fetch");
    qsb.addInput("departemen", "departemen");

    $.ajax({
        url: "formatpesan2.ajax.php",
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

            var json64 = atob(ls[1]);
            var lsPesan =  JSON.parse(json64);

            $("#sisformatsms").val(lsPesan[0]);
            $("#csisformatsms").val(lsPesan[1]);
            $("#tabunganformatsms").val(lsPesan[2]);
            $("#tungformatsms").val(lsPesan[3]);
            $("#paymentformatsms").val(lsPesan[4]);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })

};

change_dep = function ()
{
    refresh();
};

refresh = function ()
{
    var qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    document.location.href = "formatpesan2.php?" + qsb.createQs();
};

simpanPesanNotifikasi = function ()
{
    var isValid = Vldr.InputText("sisformatsms", "Pembayaran Siswa") &&
                  Vldr.InputText("csisformatsms", "Pembayaran Calon Siswa") &&
                  Vldr.InputText("tabunganformatsms", "Tabungan Siswa") &&
                  Vldr.InputText("tungformatsms", "Tunggakan Siswa & Calon Siswa") &&
                  Vldr.InputText("sisformatsms", "Transaksi SchoolPay Cashless Payment");

    if (!isValid)
        return;

    if (!confirm("Data sudan henar"))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("sisformatsms", "sisformatsms");
    qsb.addInput("csisformatsms", "csisformatsms");
    qsb.addInput("tabunganformatsms", "tabunganformatsms");
    qsb.addInput("tungformatsms", "tungformatsms");
    qsb.addInput("sisformatsms", "sisformatsms");

    $.ajax({
        url: "formatpesan2.ajax.php",
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

            showToast("Terimpan", 2000, "success", "bottom");
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })

};

