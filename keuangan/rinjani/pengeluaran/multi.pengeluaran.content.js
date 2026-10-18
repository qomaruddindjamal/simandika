var dialogBox = null;
var nData = -1;
var totalPayment = 0;

$(document).ready(function ()
{
    dialogBox = new DialogBox("#divDialog", 500, 350);
});

function ChangePengeluaran()
{
    let idpengeluaran = parseInt($("#pengeluaran").val());
    if (idpengeluaran === 0)
    {
        $("#divPaymentInfo").html("");
        return;
    }

    let qsb = new QsBuilder();
    qsb.add("op", "input");
    qsb.add("idpengeluaran", idpengeluaran);

    $.ajax({
        url: "multi.pengeluaran.content.input.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            $("#divPaymentInfo").html(response).hide().fadeIn(300);

            $("#jumlah").focus();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function showSelectPengguna()
{
    let qsb = new QsBuilder();
    qsb.add("op", "selectpengguna");
    qsb.addInput("departemen", "departemen");

    $.ajax({
        url: "multi.pengeluaran.content.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dialogBox.show(response);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function pilihPengguna()
{
    if ($("#slpengguna option").length === 0)
        return;

    $("#pengguna").val($("#slpengguna").val());
    dialogBox.close();
}

function showSelectPenerima()
{
    let qsb = new QsBuilder();
    qsb.add("op", "selectpenerima");
    qsb.addInput("departemen", "departemen");

    $.ajax({
        url: "multi.pengeluaran.content.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            dialogBox.show(response);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function pilihPenerima()
{
    if ($("#slpenerima option").length === 0)
        return;

    $("#penerima").val($("#slpenerima").val());
    dialogBox.close();
}

function addToList()
{
    let jumlah = Rupiah.RupiahToNumber($.trim($("#jumlah").val()));
    let isValid = Vldr.IsNotEmpty(jumlah, "Jumlah Pengeluaran") &&
                  Vldr.IsNumericValue(jumlah, "Jumlah Pengeluaran") &&
                  Vldr.IsIntegerValue(jumlah, "Jumlah Pengeluaran") &&
                  Vldr.IsNotNegative(jumlah, "Jumlah Pengeluaran") &&
                  Vldr.IsNotZero(jumlah, "Jumlah Pengeluaran");
    if (!isValid)
    {
        $("#jumlah").focus();
        return;
    }

    isValid = Vldr.InputText("keperluan", "Keperluan", 5) &&
              Vldr.HasOption("rekkas", "Rek Kas") &&
              Vldr.InputText("pengguna", "Pengguna", 5);

    if (!isValid)
        return;

    totalPayment += parseInt(jumlah);

    let idpengeluaran = $("#pengeluaran").val();
    let namapengeluaran = $("#pengeluaran option:selected").text();
    let rekkas = $("#rekkas").val();
    let namarekkas = $("#rekkas option:selected").text();
    let rekbeban = $("#rekbeban").val();
    let keperluan = $.trim($("#keperluan").val());
    let pengguna = $.trim($("#pengguna").val());
    let penerima = $.trim($("#penerima").val());
    let keterangan = $.trim($("#keterangan").val());
    let tanggal = $("#tglJurnal").val();

    let info = keperluan  + "<br>Pengguna: " + pengguna + ", Penerima: " + penerima + "<br><i>" + keterangan + "</i>";

    nData += 1;
    let data = "";
    data += "<tr id='row" + nData + "'>\r\n";
    data += "<input type='hidden' name='i_jumlah_" + nData + "' value='" + jumlah + "'>\r\n";
    data += "<input type='hidden' name='i_tanggal_" + nData + "' value='" + tanggal + "'>\r\n";
    data += "<input type='hidden' name='i_idpengeluaran_" + nData + "' value='" + idpengeluaran + "'>\r\n";
    data += "<input type='hidden' name='i_namapengeluaran_" + nData + "' value='" + namapengeluaran + "'>\r\n";
    data += "<input type='hidden' name='i_rekkas_" + nData + "' value='" + rekkas + "'>\r\n";
    data += "<input type='hidden' name='i_rekbeban_" + nData + "' value='" + rekbeban + "'>\r\n";
    data += "<input type='hidden' name='i_keperluan_" + nData + "' value='" + keperluan + "'>\r\n";
    data += "<input type='hidden' name='i_pengguna_" + nData + "' value='" + pengguna + "'>\r\n";
    data += "<input type='hidden' name='i_penerima_" + nData + "' value='" + penerima + "'>\r\n";
    data += "<input type='hidden' name='i_keterangan_" + nData + "' value='" + keterangan + "'>\r\n";
    data += "<td align='left'>" + namapengeluaran + "</td>\r\n";
    data += "<td align='left'>" + info + "</td>\r\n";
    data += "<td align='left'>" + namarekkas + "</td>\r\n";
    data += "<td align='right'>" + Rupiah.NumberToRupiah(jumlah) + "</td>\r\n";
    data += "<td align='center'><img src='../images/ico/hapus.png' title='hapus' onclick='DeletePayment(" + nData + ")'></td>\r\n";
    data += "</tr>\r\n";

    $("#tabPaymentList > tbody:last").append(data);
    $("#spanTotalInfo").text(numberToRupiah(totalPayment));

    if ($("#flagrow" + nData).length > 0)
    {
        $("#flagrow" + nData).val(1);
    }
    else
    {
        data = "<input type='hidden' name='flagrow" + nData + "' id='flagrow" + nData + "' value='1'>\r\n";
        $("#mainForm").append(data);
        $("#nflagrow").val(nData + 1);
    }

    $("#divPaymentInfo").text("");
}

ValidateSave = function()
{
    if (nData < 0)
    {
        alert("Anda perlu memasukan minimal satu transaksi!");
        return false;
    }

    let nNonZero = 0;
    for (let i = 0; i <= nData; i++)
    {
        let flag = parseInt($("#flagrow" + i).val());
        if (flag !== 0)
            nNonZero += 1;
    }

    if (nNonZero === 0)
    {
        alert("Anda perlu memasukan minimal satu transaksi!");
        return false;
    }

    return confirm("Semua data sudah benar?");
};

function showPilihTanggal()
{
    let selDate = $("#tglJurnal").val();

    $("#txTglJurnal").datepicker({
        dateFormat: "yy-mm-dd",
        defaultDate: selDate,
        onSelect: function (date)
        {
            $("#tglJurnal").val(date);
            $("#txTglJurnal").val(dateutil_formatInaDate(date));
        }
    }).focus();
}

function PrintDetail()
{
    let addr = "multi.pengeluaran.content.cetak.php?departemen="+dept+"&jenis="+jenis+"&noid="+noid+"&jumlah="+jumlah+"&ktransaksi="+ktransaksi;
    OpenWindow(addr, 'PrintDetail', '790', '590', 'resizable=1,scrollbars=1,status=0,toolbar=0');
}

function DeletePayment(rowno)
{
    if (!confirm("Hapus data ini?"))
        return;

    totalPayment -= parseInt($("#i_jumlah_" + rowno).val());

    $("#spanTotalInfo").text(numberToRupiah(totalPayment));

    $("#row" + rowno).remove();
    $("#flagrow" + rowno).val(0);
}