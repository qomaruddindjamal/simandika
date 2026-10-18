var helpBox = null;

$(document).ready(function ()
{
    helpBox = new DialogBox("#divHelpDialog", 620, 520);
});

function showHelp()
{
    $.ajax({
        url: "../help/sp_dailytrans.html",
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

function showPilihTanggal(tanggal)
{
    var ls = tanggal.split("-");

    var qsb = new QsBuilder();
    qsb.add("tahun", ls[0]);
    qsb.add("bulan", ls[1]);
    qsb.add("pilih", tanggal);

    newWindow("../library/calendar.dialog.php?" + qsb.createQs(), 'Kalender2','550','400','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function acceptCalendar(tanggal)
{
    let ftanggal = dateutil_formatInaDate(tanggal);
    $("#ftanggal").val(ftanggal);
    $("#tanggal").val(tanggal);

    clearReport();
}

clearReport = function ()
{
    $("#spReport").html("");
    $("#spReportRekap").html("");
};

changePetugas = function ()
{
    var petugas = $("#petugas").val();

    $.ajax({
        url: "dailytrans.ajax.php",
        method: "POST",
        data: "op=getvendor&petugas=" + petugas,
        success: function (html) {
            $("#spCbVendor").html(html);
            clearReport();
        },
        error: function (xhr) {
            alert(xhr.responseText);
        }
    })
};

function changePage()
{
    let page = $("#page").val();
    fetchPage(page);
}

function prevPage()
{
    let page = parseInt($("#page").val());
    if (page === 1)
        return;

    fetchPage(page - 1);
}

function nextPage()
{
    let page = parseInt($("#page").val());
    let totalpage = parseInt($("#totalpage").val());

    if (page === totalpage)
        return;

    fetchPage(page + 1);
}

function fetchPage(page)
{
    $("#spReport").html("memuat ..");

    let req = new RequestFactory();
    req.add("op", "8374687234678324");
    req.add("tahun", $("#tahun").val());
    req.add("bulan", $("#bulan").val());
    req.add("tanggal", $("#tanggal").val());
    req.add("petugas", $("#petugas").val());
    req.add("vendor", $("#vendor").val());
    req.add("ndata", $("#ndata").val());
    req.add("page", page);

    $.ajax({
        url: "dailytrans.ajax.php",
        method: "POST",
        data: req.createQs(),
        success: function (html)
        {
            $("#spReport").html(html).hide().fadeIn(400);

            if ($("#table").length !== 0)
                Tables('table', 1, 0);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

showDailyTrans = function ()
{
    $("#btLihat").prop("disabled", true);
    $("#spReportRekap").html("memuat ..");
    $("#spReport").html("memuat ..");

    var req = new RequestFactory();
    req.add("op", "3459843759834759");
    req.add("tahun", $("#tahun").val());
    req.add("bulan", $("#bulan").val());
    req.add("tanggal", $("#tanggal").val());
    req.add("petugas", $("#petugas").val());
    req.add("vendor", $("#vendor").val());

    $.ajax({
        url: "dailytrans.ajax.php",
        method: "POST",
        data: req.createQs(),
        success: function (html)
        {
            $("#spReportRekap").html(html).hide().fadeIn(400);

            req = new RequestFactory();
            req.add("op", "8374687234678324");
            req.add("tahun", $("#tahun").val());
            req.add("bulan", $("#bulan").val());
            req.add("tanggal", $("#tanggal").val());
            req.add("petugas", $("#petugas").val());
            req.add("vendor", $("#vendor").val());
            req.add("ndata", $("#ndata").val());
            req.add("page", 1);

            $.ajax({
                url: "dailytrans.ajax.php",
                method: "POST",
                data: req.createQs(),
                success: function (html)
                {
                    $("#btLihat").prop("disabled", false);
                    $("#spReport").html(html).hide().fadeIn(400);

                    if ($("#table").length !== 0)
                        Tables('table', 1, 0);
                },
                error: function (xhr)
                {
                    $("#btLihat").prop("disabled", false);
                    alert(xhr.responseText);
                }
            })
        },
        error: function (xhr)
        {
            $("#btLihat").prop("disabled", false);
            alert(xhr.responseText);
        }
    })
};

cetakReport = function ()
{
    var req = new RequestFactory();
    req.add("tahun", $("#tahun").val());
    req.add("bulan", $("#bulan").val());
    req.add("tanggal", $("#tanggal").val());
    req.add("petugas", $("#petugas").val());
    req.add("vendor", $("#vendor").val());

    var addr = "dailytrans.cetak.php?" + req.createQs();
    newWindow(addr, 'CetakDailyTrans', '750', '700', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

excelReport = function ()
{
    var req = new RequestFactory();
    req.add("tahun", $("#tahun").val());
    req.add("bulan", $("#bulan").val());
    req.add("tanggal", $("#tanggal").val());
    req.add("petugas", $("#petugas").val());
    req.add("vendor", $("#vendor").val());

    var addr = "dailytrans.excel.php?" + req.createQs();
    newWindow(addr, 'ExcelDailyTrans', '250', '250', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

cetakKuitansi = function(transid)
{
    newWindow('trans.kuitansi.php?transid='+transid, 'CetakKuitansiSchoolPay','360','650','resizable=1,scrollbars=1,status=0,toolbar=0');
};

function getPageContent(section)
{
    if (section === "rekap")
    {
        if ($("#spReportRekap").length)
            return $("#spReportRekap").html();
        return "-";
    }
    else if (section === "list")
    {
        if ($("#spReport").length)
            return $("#spReport").html();
        return "-";
    }
}


function showInfoSiswa(nis)
{
    let qsb = new QsBuilder();
    qsb.add("nis", nis);

    newWindow('../library/infosiswa.dialog.php?'+qsb.createQs(), 'InformasiSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function showInfoPegawai(nip)
{
    let qsb = new QsBuilder();
    qsb.add("nip", nip);

    newWindow('../library/infopegawai.dialog.php?'+qsb.createQs(), 'InformasiSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}