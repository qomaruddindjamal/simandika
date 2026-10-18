$(document).ready(function ()
{
    if ($("#tabTabunganList").length)
    {
        $("#tabTabunganList").hide().fadeIn(400);
        Tables('tabTabunganList', 1, 0);
    }
});

function showInfoSiswa()
{
    var qsb = new QsBuilder();
    qsb.add("nis", $("#userid").val());

    newWindow('../library/infosiswa.dialog.php?'+qsb.createQs(), 'InformasiSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function showSetoranInput()
{
    let viewSetoranInput = parseInt($("#viewSetoranInput").val());
    if (viewSetoranInput === 0)
    {
        $("#tabSetoranInput tbody").fadeIn(400);
        $("#viewSetoranInput").val(1)
        $("#jsetor").focus();
    }
    else
    {
        $("#tabSetoranInput tbody").css("display", "none");
        $("#viewSetoranInput").val(0)
    }

    let viewTarikanInput = parseInt($("#viewTarikanInput").val());
    if (viewTarikanInput === 1)
    {
        $("#tabTarikanInput tbody").css("display", "none");
        $("#viewTarikanInput").val(0)
    }
}

function showTarikanInput()
{
    let viewSetoranInput = parseInt($("#viewSetoranInput").val());
    if (viewSetoranInput === 1)
    {
        $("#tabSetoranInput tbody").css("display", "none");
        $("#viewSetoranInput").val(0)
    }

    let viewTarikanInput = parseInt($("#viewTarikanInput").val());
    if (viewTarikanInput === 0)
    {
        $("#tabTarikanInput tbody").fadeIn(400);
        $("#viewTarikanInput").val(1)
        $("#jtarik").focus();
    }
    else
    {
        $("#tabTarikanInput tbody").css("display", "none");
        $("#viewTarikanInput").val(0)
    }

    fetchLokasiPengambilan();
}

function fetchLokasiPengambilan()
{
    let qsb = new QsBuilder();
    qsb.add("op", "lokasiambil");
    qsb.addInput("nis", "userid");
    qsb.addInput("idtabungan", "idtabungan");

    let spPengambilan = $("#spPengambilan");
    spPengambilan.html("memaut ..");

    $.ajax({
        url: "transaksi.tabungan.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            spPengambilan.html(html).hide().fadeIn(400);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function onPrevPageClick()
{
    let page = parseInt($("#page").val());
    if (page === 1)
        return;

    page -= 1;
    $("#page").val(page + "");
    onChangePage();
}

function onNextPageClick()
{
    let page = parseInt($("#page").val());
    let totalpage = parseInt($("#totalpage").val());
    if (page === totalpage)
        return;

    page += 1;
    $("#page").val(page + "");
    onChangePage();
}

function onChangePage()
{
    let page = $("#page").val();

    let dvTabTabunganList = $("#dvTabTabunganList");
    dvTabTabunganList.html("memuat ..");

    let qsb = new QsBuilder();
    qsb.add("op", "riwayat");
    qsb.addInput("nis", "userid");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.add("page", page);

    $.ajax({
        url: "transaksi.tabungan.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dvTabTabunganList.html(response).hide().fadeIn(400);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function refreshInfo()
{
    let divSectionTabunganInfo = $("#divSectionTabunganInfo");
    divSectionTabunganInfo.html("memuat ..");

    let qsb = new QsBuilder();
    qsb.add("op", "info");
    qsb.addInput("nis", "userid");
    qsb.addInput("idtabungan", "idtabungan");
    //qsb.add("page", page);

    $.ajax({
        url: "transaksi.tabungan.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            divSectionTabunganInfo.html(response).hide().fadeIn(400);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function refreshRiwayat()
{
    let dvTabTabunganList = $("#dvTabTabunganList");
    dvTabTabunganList.html("memuat ..");

    let qsb = new QsBuilder();
    qsb.add("op", "riwayat");
    qsb.addInput("nis", "userid");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.add("page", 1);

    $.ajax({
        url: "transaksi.tabungan.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dvTabTabunganList.html(response).hide().fadeIn(400);

            refreshPageControl();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function refresh()
{
    refreshInfo();
    refreshRiwayat();
    fetchLokasiPengambilan();
}

function refreshPageControl()
{
    let totalpage = parseInt($("#totalpage").val());
    let ndata = parseInt($("#ndata").val());

    if (totalpage === 0 || ndata === 0)
        return;

    let qsb = new QsBuilder();
    qsb.add("op", "pagecontrol");
    qsb.add("totalpage", totalpage);
    qsb.add("ndata", ndata);
    qsb.add("page", 1);

    let dvPageControl = $("#dvPageControl");
    dvPageControl.html("memuat ..");

    $.ajax({
        url: "transaksi.tabungan.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dvPageControl.html(response).hide().fadeIn(300);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function simpanSetoran()
{
    let jumlah = Rupiah.RupiahToNumber($.trim($("#jsetor").val()));
    let isValid = Vldr.IsNotEmpty(jumlah, "Jumlah Setoran") &&
        Vldr.IsNumericValue(jumlah, "Jumlah Setoran") &&
        Vldr.IsIntegerValue(jumlah, "Jumlah Setoran") &&
        Vldr.IsNotNegative(jumlah, "Jumlah Setoran") &&
        Vldr.IsNotZero(jumlah, "Jumlah Setoran");
    if (!isValid)
    {
        $("#jsetor").focus();
        return;
    }

    let sendnotif = $("#sendnotifsetor").is(":checked") ? 1 : 0;

    let qsb = new QsBuilder();
    qsb.add("op", "setoran");
    qsb.add("jumlah", jumlah);
    qsb.addInput("sumberdana", "sumberdana");
    qsb.addInput("lokasidanasetor", "lokasidanasetor");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("keterangan", "keterangansetor");
    qsb.addInput("nis", "userid");
    qsb.addInput("namasiswa", "username");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.addInput("namatabungan", "namatabungan");
    qsb.add("sendnotif", sendnotif);

    $.ajax({
        url: "transaksi.tabungan.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                showToast(ls[1], 3000, "error", "top");

                alert(ls[1]);
                return;
            }

            successToast();

            clearInput();
            refresh();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function successToast()
{
    showToast("Berhasil", 2000, "success", "top");
}

function simpanTarikan()
{
    let jumlah = Rupiah.RupiahToNumber($.trim($("#jtarik").val()));
    let isValid = Vldr.IsNotEmpty(jumlah, "Jumlah Penarikan") &&
        Vldr.IsNumericValue(jumlah, "Jumlah Penarikan") &&
        Vldr.IsIntegerValue(jumlah, "Jumlah Penarikan") &&
        Vldr.IsNotNegative(jumlah, "Jumlah Penarikan") &&
        Vldr.IsNotZero(jumlah, "Jumlah Penarikan") &&
        Vldr.HasOption('lokasidanatarik', 'Lokasi Pengambilan Dana');

    if (!isValid)
    {
        $("#jtarik").focus();
        return;
    }
    
    let jsonInfo = atob($("#lokasidanatarik").val());
    let lsInfo = JSON.parse(jsonInfo);
    let kodeLokasi = lsInfo[0];
    let saldoLokasi = parseInt(lsInfo[1]);

    if (jumlah > saldoLokasi)
    {
        alert("Saldo tabungan tidak muncukupi untuk penarikan");
        $("#jtarik").focus();
        return;
    }

    let sendnotif = $("#sendnotiftarik").is(":checked") ? 1 : 0;

    let qsb = new QsBuilder();
    qsb.add("op", "tarikan");
    qsb.add("jumlah", jumlah);
    //qsb.addInput("lokasidanatarik", "lokasidanatarik");
    qsb.add("lokasidanatarik", kodeLokasi);
    qsb.addInput("departemen", "departemen");
    qsb.addInput("keterangan", "keterangantarik");
    qsb.addInput("nis", "userid");
    qsb.addInput("namasiswa", "username");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.addInput("namatabungan", "namatabungan");
    qsb.add("sendnotif", sendnotif);

    $.ajax({
        url: "transaksi.tabungan.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                showToast(ls[1], 3000, "error", "top");

                alert(ls[1]);
                return;
            }

            successToast();

            clearInput();
            
            refresh();

            fetchLokasiPengambilan();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function clearInput()
{
    $("#jsetor").val("");
    $("#keterangansetor").val("");

    $("#jtarik").val("");
    $("#keterangantarik").val("");

    $("#sumberdana option").first().prop('selected', true);

    if ($("#lokasidanasetor").length)
        $("#lokasidanasetor option").first().prop('selected', true);

    if ($("#lokasidanatarik").length)
        $("#lokasidanatarik option").first().prop('selected', true);
}

function cetakkuitansi(id)
{
    newWindow('transaksi.kuitansi.php?id='+id, 'CetakKuitansiTabungan','360','650','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function editpembayaran(id)
{
    newWindow('transaksi.tabungan.edit.php?idpembayaran='+id,'EditPembayaranTabungan','425','450','resizable=1,scrollbars=1,status=0,toolbar=0')
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    let addr = "transaksi.tabungan.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakTransaksiTabungan2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function getPageContent(section)
{
    if (section === "departemen")
    {
        return $("#departemen").val();
    }
    else if (section === "siswa")
    {
        return $("#username").val() + " (" + $("#userid").val() + ")";
    }
    else if (section === "tabungan")
    {
        return $("#namatabungan").val();
    }
    else if (section === "info")
    {
        if ($("#dvTabInfoTabungan").length)
            return $("#dvTabInfoTabungan").html();

        return "-";
    }
    else if (section === "riwayat")
    {
        if ($("#dvTabTabunganList").length)
            return $("#dvTabTabunganList").html();

        return "-";
    }
}