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
    newWindow(addr, 'RekAkunDialog2', '760', '560', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

acceptRekAkunDialog = function(kategori, subKategori, kodeRek, namaRek)
{
    if (kategori === "HARTA")
    {
        $("#inforekkas").val(kodeRek + " " + namaRek);
        $("#rekkas").val(kodeRek);
    }
    else if (kategori === "BIAYA")
    {
        $("#inforekbeban").val(kodeRek + " " + namaRek);
        $("#rekbeban").val(kodeRek);

    }
};

simpanJenisPengeluaran = function()
{
    let idJenis = $("#idjenis").val();
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

    let rekBeban = $.trim($("#rekbeban").val());
    if (rekBeban.length === 0)
    {
        alert("Rek Beban belum ditentukan");
        $("#inforekbeban").focus();
        return;
    }

    let keterangan = $.trim($("#keterangan").val());

    let qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.add("nama", nama);
    qsb.add("idjenis", idJenis);
    qsb.add("departemen", departemen);
    qsb.add("rekkas", rekKas);
    qsb.add("rekbeban", rekBeban);
    qsb.add("keterangan", keterangan);

    $.ajax({
        url: "jenispengeluaran2.dialog.ajax.php",
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
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
};