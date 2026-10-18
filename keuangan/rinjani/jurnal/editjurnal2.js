let selectedIx = 0;

$(document).ready(function ()
{
    Tables('table', 1, 0);
});

showPilihTanggal = function ()
{
    var selDate = $("#tglJurnal").val();

    $("#txTglJurnal").datepicker({
        dateFormat: "yy-mm-dd",
        defaultDate: selDate,
        onSelect: function (date)
        {
            $("#tglJurnal").val(date);
            $("#txTglJurnal").val(dateutil_formatInaDate(date));
        }
    }).focus();
};

pilihrek = function (ix)
{
    selectedIx = ix;

    let addr = '../library/select.rekakun.dialog.php';
    newWindow(addr, 'SelectRekAkunDialog', '760', '560', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

acceptRekAkunDialog = function(kategori, subKategori, kodeRek, namaRek)
{
    if (selectedIx === 0)
        return;

    $("#koderek" + selectedIx).val(kodeRek);
    $("#namarek" + selectedIx).val(namaRek);
};

function hitungJumlah(kas, cnt)
{
    let i = 1;
    let isValid = true;
    let totaldebet = 0;
    let totalkredit = 0;

    if (kas === 'debet')
    {
        let tmp = $("#debet" + cnt).val();
        let debet = Rupiah.RupiahToNumber(tmp);
        if (Rupiah.IsNumber(debet))
        {
            if (debet.length === 0)
            {
                $("#kredit" + cnt).val("");
            }
            else if (parseInt(debet) !== 0)
            {
                $("#kredit" + cnt).val(0);
                Rupiah.FormatRupiah('kredit' + cnt);
            }
        }
        else
        {
            isValid = false;
            $("#totaldebet").val("ERROR");
        }
    }
    else
    {
        let tmp = $("#kredit" + cnt).val();
        let kredit = Rupiah.RupiahToNumber(tmp);
        if (Rupiah.IsNumber(kredit))
        {
            if (kredit.length === 0)
            {
                $("#debet" + cnt).val("");
            }
            else if (parseInt(kredit) !== 0)
            {
                $("#debet" + cnt).val(0);
                Rupiah.FormatRupiah('debet' + cnt);
            }
        }
        else
        {
            isValid = false;
            $("#totalkredit").val("ERROR");
        }
    }

    if (!isValid)
    {
        $("#totalstatus").val(-1);
        return;
    }

    let maxInputJurnal = parseInt($("#maxInputJurnal").val());
    while (i <= maxInputJurnal)
    {
        let jdebet = $.trim($("#debet" + i ).val());
        if (jdebet.length === 0)
            jdebet = 0;
        else
            jdebet = Rupiah.RupiahToNumber(jdebet);
        totaldebet = parseFloat(totaldebet) + parseFloat(jdebet);

        let jkredit = $.trim($("#kredit" + i).val());
        if (jkredit.length === 0)
            jkredit = 0;
        else
            jkredit = Rupiah.RupiahToNumber(jkredit);
        totalkredit = parseFloat(totalkredit) + parseFloat(jkredit);

        i = i + 1;
    }

    $("#totaldebet").val(totaldebet);
    $("#totalkredit").val(totalkredit);

    Rupiah.FormatRupiah('totaldebet');
    Rupiah.FormatRupiah('totalkredit');

    $("#totalstatus").val(1);
}

function validateJumlah()
{
    let totalStatus = parseInt($("#totalstatus").val());
    if (totalStatus === -1)
    {
        alert("Jumlah jurnal ada kesalahan");
        return false;
    }

    let isValid = true;
    let totalDebet = 0;
    let totalKredit = 0;
    let terisi = 0;

    let maxInputJurnal = parseInt($("#maxInputJurnal").val());
    for(let i = 0; isValid && i < maxInputJurnal; i++)
    {
        let kodeRek = $.trim($("#koderek" + i).val());

        let jDebet = 0;
        let debet = $.trim($("#debet" + i).val());
        if (debet.length === 0)
        {
            $("#debet" + i).val(0);
        }
        else
        {
            let tmp = Rupiah.RupiahToNumber(debet);
            if (Rupiah.IsNumber(tmp))
            {
                jDebet = parseInt(tmp);
                totalDebet += jDebet;
            }
            else
            {
                alert("Jumlah debet belum benar!");
                $("#debet" + i).focus();
                isValid = false;
                break;
            }
        }

        let jKredit = 0;
        let kredit = $.trim($("#kredit" + i).val());
        if (kredit.length === 0)
        {
            $("#kredit" + i).val(0);
        }
        else
        {
            let tmp = Rupiah.RupiahToNumber(kredit);
            if (Rupiah.IsNumber(tmp))
            {
                jKredit = parseInt(tmp);
                totalKredit += jKredit;
            }
            else
            {
                alert("Jumlah kredit belum benar!");
                $("#kredit" + i).focus();
                isValid = false;
                break;
            }
        }

        if (kodeRek.length > 0)
        {
            if (jDebet === 0 && jKredit === 0)
            {
                alert ("Anda harus mengisikan data di kolom debet atau kredit!");
                $("#debet" + i).focus();
                isValid = false;
                break;
            }

            terisi = 1;
        }
        else
        {
            if (jDebet !== 0 || jKredit !== 0)
            {
                alert ("Anda harus mengisikan data untuk kode rekening!");
                isValid = false;
                break;
            }
        }
    } // for

    if (!isValid)
        return false;

    if (terisi === 0)
    {
        alert ("Anda harus mengisi setidaknya satu data untuk transaksi!");
        return false;
    }

    if (totalDebet !== totalKredit)
    {
        alert ("Total Debet tidak sama dengan Total Kredit!");
        return false;
    }

    return true;
}

function simpanJurmalUmum()
{
    let isValid = Vldr.InputText('departemen', 'Departemen') &&
        Vldr.InputText('tahunbuku', 'Tahun Buku') &&
        Vldr.InputText('tglJurnal', 'Tanggal Jurnal') &&
        Vldr.InputText('keperluan', 'Keperluan', 5, 255) &&
        Vldr.InputText('alasan', 'Alasan Perubahan Data', 5, 255) &&
        validateJumlah() &&
        confirm("Data sudah benar?");

    if (!isValid)
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.addInput("idjurnal", "idjurnal");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("tgljurnal", "tglJurnal");
    qsb.addInput("keperluan", "keperluan");
    qsb.addInput("keterangan", "keterangan");
    qsb.addInput("alasan", "alasan");

    let nData = 0;
    let maxInputJurnal = parseInt($("#maxInputJurnal").val());
    for(let i = 1; i <= maxInputJurnal; i++)
    {
        let koderek = $.trim($("#koderek" + i).val());
        let debet = $.trim($("#debet" + i).val());
        let kredit = $.trim($("#kredit" + i).val());

        if (koderek.length === 0 && debet.length === 0 && kredit.length === 0)
            continue;

        let jDebet = parseInt(Rupiah.RupiahToNumber(debet));
        let jKredit = parseInt(Rupiah.RupiahToNumber(kredit));

        if (koderek.length === 0 && jDebet === 0 && jKredit === 0)
            continue;

        nData += 1;
        qsb.add("koderek" + nData, koderek);
        qsb.add("debet" + nData, jDebet);
        qsb.add("kredit" + nData, jKredit);
    }
    qsb.add("ndata", nData);

    $("#btSimpan").prop("disabled", true);
    $("#spInfo").css("color", "blue");
    $("#spInfo").html("memuat ..");

    $.ajax({
        url: "editjurnal2.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                $("#btSimpan").prop("disabled", false);
                $("#spInfo").css("color", "red");
                $("#spInfo").html(ls[1]);
                return;
            }

            opener.refreshPage();
            window.close();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}