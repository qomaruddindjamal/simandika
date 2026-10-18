$(document).ready(function ()
{
    helpBox = new DialogBox("#divHelpDialog", 620, 520);
});

function showHelp()
{
    $.ajax({
        url: "../help/sp_clienttrans.html",
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

function openSearchSiswa()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow("../library/daftarsiswa.dialog.php?" + qsb.createQs(), 300, 500, 'resizable=1,scrollbars=1,status=0,toolbar=0');
}

function acceptSiswa(kelompok, json64)
{
    let data = JSON.parse(atob(json64));

    $("#noid").val(data.NIS);
    $("#nama").val(data.Nama);
    $("#kelompok").val(kelompok);

    clearReport();
}

function openSearchPegawai()
{
    newWindow("../library/daftarpegawai.dialog.php", 300, 500, 'resizable=1,scrollbars=1,status=0,toolbar=0');
}

function acceptPegawai(kelompok, json64)
{
    let data = JSON.parse(atob(json64));

    $("#noid").val(data.NIP);
    $("#nama").val(data.Nama);
    $("#kelompok").val(kelompok);

    clearReport();
}

openSearchClient = function ()
{
    let addr = "searchclient.php";
    newWindow(addr, 'SearchClient', '550', '550', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

acceptSearch = function(data, noid, nama, kelas)
{
    $("#noid").val(noid);
    $("#nama").val(nama);
    $("#kelompok").val(data);

    clearReport();
};

clearReport = function () {
    $("#spReport").html("");
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
    let noId = $.trim($("#noid").val());
    let kelompok = $.trim($("#kelompok").val());

    let req = new RequestFactory();
    req.add("op", "2987429834783294");
    req.add("clientid", noId);
    req.add("clientgroup", kelompok);
    req.add("bulan", $("#bulan").val());
    req.add("tahun", $("#tahun").val());
    req.add("ndata", $("#ndata").val());
    req.add("page", page);

    $.ajax({
        url: "client.trans.ajax.php",
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

showClientTrans = function ()
{
    var noId = $.trim($("#noid").val());
    var kelompok = $.trim($("#kelompok").val());

    if (noId.length === 0)
        return;

    $("#btLihat").prop("disabled", true);
    $("#spReportRekap").html("memuat ..");
    $("#spReport").html("memuat ..");

    var req = new RequestFactory();
    req.add("op", "9860945869056");
    req.add("clientid", noId);
    req.add("clientgroup", kelompok);
    req.add("bulan", $("#bulan").val());
    req.add("tahun", $("#tahun").val());

    $.ajax({
        url: "client.trans.ajax.php",
        method: "POST",
        data: req.createQs(),
        success: function (html)
        {
            $("#spReportRekap").html(html).hide().fadeIn(400);

            req = new RequestFactory();
            req.add("op", "2987429834783294");
            req.add("clientid", noId);
            req.add("clientgroup", kelompok);
            req.add("bulan", $("#bulan").val());
            req.add("tahun", $("#tahun").val());
            req.add("ndata", $("#ndata").val());
            req.add("page", 1);

            $.ajax({
                url: "client.trans.ajax.php",
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
    });
};

cetakReport = function ()
{
    var noId = $.trim($("#noid").val());
    var kelompok = $.trim($("#kelompok").val());

    var req = new RequestFactory();
    req.add("clientid", noId);
    req.add("clientgroup", kelompok);
    req.add("bulan", $("#bulan").val());
    req.add("tahun", $("#tahun").val());

    var addr = "client.trans.cetak.php?" + req.createQs();
    newWindow(addr, 'CetakClientTrans', '750', '700', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

excelReport = function ()
{
    var noId = $.trim($("#noid").val());
    var kelompok = $.trim($("#kelompok").val());

    var req = new RequestFactory();
    req.add("clientid", noId);
    req.add("clientgroup", kelompok);
    req.add("bulan", $("#bulan").val());
    req.add("tahun", $("#tahun").val());

    var addr = "client.trans.excel.php?" + req.createQs();
    newWindow(addr, 'ExcelClientTrans', '250', '250', 'resizable=1,scrollbars=1,status=0,toolbar=0');
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