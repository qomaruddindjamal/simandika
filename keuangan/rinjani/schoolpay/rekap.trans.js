var tanggalIx = 0;
var helpBox = null;

$(document).ready(function ()
{
    helpBox = new DialogBox("#divHelpDialog", 620, 520);
});

function showHelp()
{
    $.ajax({
        url: "../help/sp_rekaptrans.html",
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
    tanggalIx = ix;

    var ls = tanggal.split("-");

    var qsb = new QsBuilder();
    qsb.add("tahun", ls[0]);
    qsb.add("bulan", ls[1]);
    qsb.add("pilih", tanggal);

    newWindow("../library/calendar.dialog.php?" + qsb.createQs(), 'Kalender2','550','400','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function acceptCalendar(tanggal)
{
    var ftanggal = dateutil_formatInaDate(tanggal);
    if (tanggalIx === 1)
    {
        $("#ftanggal1").val(ftanggal);
        $("#tanggal1").val(tanggal);
    }
    else
    {
        $("#ftanggal2").val(ftanggal);
        $("#tanggal2").val(tanggal);
    }

    clearReport();
}

showRekapTrans = function()
{
    if (!$("#vendor").val())
        return;

    $("#btLihat").prop("disabled", true);
    $("#spReport").html("memuat .. ");

    let vendorId = $("#vendor").val();
    let dtStart = $("#tanggal1").val();
    let dtEnd = $("#tanggal2").val();

    let req = new RequestFactory();
    req.add("op", "83482748234");
    req.add("vendorid", vendorId);
    req.add("dtstart", dtStart);
    req.add("dtend", dtEnd);

    $.ajax({
        url: "rekap.trans.ajax.php",
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

cetakReport = function ()
{
    let vendorId = $("#vendor").val();
    let dtStart = $("#tanggal1").val();
    let dtEnd = $("#tanggal2").val();

    let req = new RequestFactory();
    req.add("vendorid", vendorId);
    req.add("dtstart", dtStart);
    req.add("dtend", dtEnd);

    let addr = "rekap.trans.cetak.php?" + req.createQs();
    newWindow(addr, 'CetakRekapTrans', '750', '700', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

excelReport = function ()
{
    let vendorId = $("#vendor").val();
    let dtStart = $("#tanggal1").val();
    let dtEnd = $("#tanggal2").val();

    let req = new RequestFactory();
    req.add("vendorid", vendorId);
    req.add("dtstart", dtStart);
    req.add("dtend", dtEnd);

    let addr = "rekap.trans.excel.php?" + req.createQs();
    newWindow(addr, 'ExcelRekapTrans', '250', '250', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

clearReport = function ()
{
    $("#spReport").html("");
};