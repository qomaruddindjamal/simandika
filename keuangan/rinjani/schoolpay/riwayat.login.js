var helpBox = null;

$(document).ready(function ()
{
    helpBox = new DialogBox("#divHelpDialog", 620, 520);
});

function showHelp()
{
    $.ajax({
        url: "../help/sp_riwayatlogin.html",
        success: function (content)
        {
            helpBox.show(content);

            setTimeout(function () {
                $("#divHelpDialog").scrollTop(0);
            }, 750)
        },
        error: function(xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function showPilihTanggal(ix, tanggal)
{
    let ls = tanggal.split("-");

    let qsb = new QsBuilder();
    qsb.add("tahun", ls[0]);
    qsb.add("bulan", ls[1]);
    qsb.add("pilih", tanggal);

    newWindow("../library/calendar.dialog.php?" + qsb.createQs(), 'Kalender2','550','400','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function acceptCalendar(tanggal)
{
    let ftanggal = dateutil_formatInaDate(tanggal);
    $("#ftanggal1").val(ftanggal);
    $("#tanggal1").val(tanggal);

    clearReport();
}


showRiwayatLogin = function()
{
    if (!$("#vendor").val())
        return;

    $("#btLihat").prop("disabled", true);
    $("#spReport").html("memuat ..");

    var req = new RequestFactory();
    req.add("op", "23894723984789324");
    req.add("vendorid", $("#vendor").val());
    req.add("tanggal", $("#tanggal1").val());

    $.ajax({
        url: "riwayat.login.ajax.php",
        method: "POST",
        data: req.createQs(),
        success: function (html)
        {
            $("#btLihat").prop("disabled", false);
            $("#spReport").html(html);

            if ($("#table").length !== 0)
                Tables('table', 1, 0);
        },
        error: function (xhr)
        {
            $("#btLihat").prop("disabled", false);
            alert(xhr.responseText);
        }
    });
};

clearReport = function ()
{
    $("#spReport").html("");
};

cetakReport = function ()
{
    var req = new RequestFactory();
    req.add("vendorid", $("#vendor").val());
    req.add("tanggal", $("#tanggal1").val());

    var addr = "riwayat.login.cetak.php?" + req.createQs();
    newWindow(addr, 'CetakRiwayatTrans', '750', '700', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

excelReport = function ()
{
    var req = new RequestFactory();
    req.add("vendorid", $("#vendor").val());
    req.add("tanggal", $("#tanggal1").val());

    var addr = "riwayat.login.excel.php?" + req.createQs();
    newWindow(addr, 'ExcelRiwayatTrans', '250', '250', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};