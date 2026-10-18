showRekAkunDialog = function(kategori, subKategori)
{
    let qsb = new QsBuilder();
    qsb.add("kategori", kategori);
    qsb.add("subkategori", subKategori);

    let addr = '../library/rekakun.dialog.php?' + qsb.createQs();
    newWindow(addr, 'RekAkunDialog3', '760', '560', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

acceptRekAkunDialog = function(kategori, subKategori, kodeRek, namaRek)
{
    if (kategori === "HARTA")
    {
        $("#inforekkasvendor").val(kodeRek + " " + namaRek);
        $("#rekkasvendor").val(kodeRek);
    }
    else if (kategori === "UTANG")
    {
        $("#inforekutangvendor").val(kodeRek + " " + namaRek);
        $("#rekutangvendor").val(kodeRek);
    }
};

simpanKonfigurasi = function ()
{
    let jumlah = Rupiah.RupiahToNumber($.trim($("#maxtrans").val()));
    let isValid = Vldr.IsNotEmpty(jumlah, "Maksimal Transaksi") &&
        Vldr.IsNumericValue(jumlah, "Maksimal Transaksi") &&
        Vldr.IsIntegerValue(jumlah, "Maksimal Transaksi") &&
        Vldr.IsNotNegative(jumlah, "Maksimal Transaksi");
    if (!isValid)
    {
        $("#maxtrans").focus();
        return;
    }

    if (!Vldr.HasOption("tabungan", "Auto Debet Tabungan") ||
        !Vldr.InputText("rekkasvendor", "Rek Kas Vendor") ||
        !Vldr.InputText("rekutangvendor", "Rek Utang Vendor") ||
        !confirm("Data sudah benar?"))
        return;

    var dept = $("#departemen").val();
    var idPt = $("#idpt").val();
    var idTabungan = $("#tabungan").val();
    var maxTrans = $("#maxtrans").val();
    maxTrans = rupiahToNumber(maxTrans);
    if ($.trim(maxTrans).length === 0) maxTrans = 0;

    var rekKasVendor = $("#rekkasvendor").val();
    var rekUtangVendor = $("#rekutangvendor").val();

    //var data = "op=435353456346346&dept=" + dept + "&idpt=" + idPt + "&idtabungan=" + idTabungan + "&maxtrans=" + maxTrans + "&rekkas=" + rekKasVendor + "&rekutang=" + rekUtangVendor;

    var request = new RequestFactory();
    request.add("op", "435353456346346");
    request.add("dept", dept);
    request.add("idpt", idPt);
    request.add("idtabungan", idTabungan);
    request.add("maxtrans", maxTrans);
    request.add("rekkas", rekKasVendor);
    request.add("rekutang", rekUtangVendor);

    $.ajax({
        url: "konfigurasi.pegawai.dialog.ajax.php",
        data: request.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                alert(ls[1]);
                return;
            }

            opener.location.reload();
            window.close();
        },
        error: function (xhr) {
            alert(xhr.responseText);
        }
    })

};


changeDept = function()
{
    if ($("#departemen option").length === 0)
        return;

    var dept = $("#departemen").val();

    var request = new RequestFactory();
    request.add("op", "654736547624");
    request.add("dept", dept);

    $.ajax({
        url: "konfigurasi.pegawai.dialog.ajax.php",
        data: request.createQs(),
        success: function (html)
        {
            $("#spTabungan").html(html);
        },
        error: function (xhr) {
            alert(xhr.responseText);
        }
    })
};