$(document).ready(function ()
{
    $("#nama").focus();
});

showRekAkunDialog = function(kategori, subKategori)
{
    let qsb = new QsBuilder();
    qsb.add("kategori", kategori);
    qsb.add("subkategori", subKategori);

    let addr = '../library/rekakun.dialog.php?' + qsb.createQs();
    newWindow(addr, 'RekAkunDialog', '760', '560', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

acceptRekAkunDialog = function(kategori, subKategori, kodeRek, namaRek)
{
    if (kategori === "HARTA")
    {
        $("#inforekkas").val(kodeRek + " " + namaRek);
        $("#rekkas").val(kodeRek);
    }
    else if (kategori === "PENDAPATAN")
    {
        if (subKategori === "PENDAPATAN")
        {
            $("#inforekpendapatan").val(kodeRek + " " + namaRek);
            $("#rekpendapatan").val(kodeRek);
        }
        else
        {
            $("#inforekdiskon").val(kodeRek + " " + namaRek);
            $("#rekdiskon").val(kodeRek);
        }
    }
    else if (kategori === "PIUTANG")
    {
        $("#inforekpiutang").val(kodeRek + " " + namaRek);
        $("#rekpiutang").val(kodeRek);
    }
};


simpanJenisPenerimaan = function()
{
    let idJenis = $("#idjenis").val();
    let idKategori = $("#idkategori").val();
    let departemen = $("#departemen").val();

    let nama = $.trim($("#nama").val());
    if (nama.length === 0)
    {
        alert("Nama jenis penerimaan belum ditentukan");
        $("#nama").focus();
        return;
    }

    let rekKas = $.trim($("#rekkas").val());
    if (rekKas.length === 0)
    {
        alert("Rek Kas belum ditentukan");
        $("#inforekkas").focus();
        return;
    }

    let rekPendapatan = $.trim($("#rekpendapatan").val());
    if (rekPendapatan.length === 0)
    {
        alert("Rek Pendapatan belum ditentukan");
        $("#inforekpendapatan").focus();
        return;
    }

    let rekPiutang = "";
    let rekDiskon = "";
    if (idKategori === "JTT" || idKategori === "CSWJB")
    {
        rekPiutang = $.trim($("#rekpiutang").val());
        if (rekPiutang.length === 0)
        {
            alert("Rek Piutang belum ditentukan");
            $("#inforekpiutang").focus();
            return;
        }

        rekDiskon = $.trim($("#rekdiskon").val());
        if (rekDiskon.length === 0)
        {
            alert("Rek Diskon belum ditentukan");
            $("#inforekdiskon").focus();
            return;
        }
    }

    let keterangan = $.trim($("#keterangan").val());
    let sendNotif = $("#sendnotif").prop("checked") ? 1 : 0;

    let qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.add("nama", nama);
    qsb.add("idjenis", idJenis);
    qsb.add("idkategori", idKategori);
    qsb.add("departemen", departemen);
    qsb.add("rekkas", rekKas);
    qsb.add("rekpendapatan", rekPendapatan);
    if (idKategori === "JTT" || idKategori === "CSWJB")
    {
        qsb.add("rekpiutang", rekPiutang);
        qsb.add("rekdiskon", rekDiskon);
    }
    qsb.add("keterangan", keterangan);
    qsb.add("sendnotif", sendNotif);

    $.ajax({
        url: "jenispenerimaan2.dialog.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) !== 1)
            {
                alert(ls[1]);
                return;
            }

            opener.refresh();
            window.close();
        },
        error: function (xhr) {
            alert(xhr.responseText);
        }
    });
};