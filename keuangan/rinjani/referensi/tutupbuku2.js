var helpBox = null;

$(document).ready(function ()
{
    helpBox = new DialogBox("#divHelpDialog", 500, 500);
});

function showHelp()
{
    $.ajax({
        url: "../help/rf_tutupbuku.html",
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

showPilihTanggal = function ()
{
    var selDate = $("#ttutup").val();

    $("#txttutup").datepicker({
        dateFormat: "yy-mm-dd",
        defaultDate: selDate,
        onSelect: function (date)
        {
            $("#ttutup").val(date);
            $("#txttutup").val(dateutil_formatInaDate(date));
        }
    }).focus();
};

showPilihTanggalMulai = function ()
{
    var selDate = $("#tawal").val();

    $("#txtawal").datepicker({
        dateFormat: "yy-mm-dd",
        defaultDate: selDate,
        onSelect: function (date)
        {
            $("#tawal").val(date);
            $("#txtawal").val(dateutil_formatInaDate(date));
        }
    }).focus();
};

prosesTutupBuku1 = function ()
{
    if ($("#departemen option").length === 0)
        return;

    if ($.trim($("#ttutup").val()).length === 0)
        return;

    if (!confirm("Data sudah benar?"))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "tutupbuku1");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("ttutup", "ttutup");

    $.ajax({
        url: "tutupbuku2.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            var ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                $("#tabError").css("visibility", "visible");
                $("#spError").html(ls[1]);
                return;
            }

            document.location.replace("tutupbuku22.php");
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
};

prosesTutupBuku2 = function ()
{

    var isValid = Vldr.InputText("departemen", "Departemen") &&
                  Vldr.InputText("ttutup", "Tanggal Tutup Buku") &&
                  Vldr.InputText("tahunbuku", "Tahun Tutup Buku") &&
                  Vldr.InputText("tawal", "Tanggal Mulai") &&
                  Vldr.InputText("awalan", "Awalan Tahun Buku") &&
                  Vldr.HasOption("rekre", "Retained Earning");

    if (!isValid)
        return;

    if (!confirm("Data sudah benar?\nNOTE: Setelah selesai, proses tutup buku tidak bisa diulangi kembali"))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "tutupbuku2");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("ttutup", "ttutup");
    qsb.addInput("tahunbuku", "tahunbuku");
    qsb.addInput("tawal", "tawal");
    qsb.addInput("awalan", "awalan");
    qsb.addInput("rekre", "rekre");
    qsb.addInput("keterangan", "keterangan");

    $.ajax({
        url: "tutupbuku2.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            var ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                $("#tabError").css("visibility", "visible");
                $("#spError").html(ls[1]);
                return;
            }

            document.location.replace("tutupbuku23.php");
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
};