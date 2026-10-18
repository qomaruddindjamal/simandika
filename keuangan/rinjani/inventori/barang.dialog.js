function simpanBarang()
{
    let isValid = Vldr.InputText("kode", "Kode Barang", 3) &&
                  Vldr.InputText("nama", "Nama Barang", 5) &&
                  Vldr.InputText("jumlah", "Jumlah Barang") &&
                  Vldr.IsInteger("jumlah", "Jumlah Barang") &&
                  Vldr.IsNotZero("jumlah", "Jumlah Barang") &&
                  Vldr.InputText("satuan", "Satuan Barang", 3);

    if (!isValid)
        return;

    let harga = Rupiah.RupiahToNumber($.trim($("#harga").val()));
    isValid = Vldr.IsNotEmpty(harga, "Harga Barang") &&
        Vldr.IsNumericValue(harga, "Harga Barang") &&
        Vldr.IsIntegerValue(harga, "Harga Barang") &&
        Vldr.IsNotNegative(harga, "Besar Pembayaran");
    if (!isValid)
    {
        $("#harga").focus();
        return;
    }

    let formData = new FormData();
    formData.append("op", "simpanbarang");
    formData.append("idkelompok", $("#idkelompok").val());
    formData.append("id", $("#id").val());
    formData.append("kode", $.trim($("#kode").val()));
    formData.append("nama", $.trim($("#nama").val()));
    formData.append("jumlah", $.trim($("#jumlah").val()));
    formData.append("satuan", $.trim($("#satuan").val()));
    formData.append("harga", harga);
    formData.append("tanggal", $.trim($("#tanggal").val()));
    formData.append("kondisi", $.trim($("#kondisi").val()));
    formData.append("keterangan", $.trim($("#keterangan").val()));
    if ($.trim($("#foto")).length === 0)
    {
        formData.append("hasfoto", 0);
    }
    else
    {
        formData.append("hasfoto", 1);
        formData.append("foto", $("#foto")[0].files[0]);
    }

    $.ajax({
        url: "barang.dialog.ajax.php",
        method: "POST",
        data: formData,
        async: false,
        cache: false,
        contentType: false,
        processData: false,
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
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
    })

}

function hitungTotal()
{
    let jumlah = $.trim($("#jumlah").val());
    let harga = Rupiah.RupiahToNumber($.trim($("#harga").val()));

    let isValid = Vldr.IsNotEmpty(jumlah) &&
                  Vldr.IsIntegerValue(jumlah) &&
                  Vldr.IsNotZero(jumlah) &&
                  Vldr.IsNotEmpty(harga) &&
                  Vldr.IsNumericValue(harga) &&
                  Vldr.IsIntegerValue(harga) &&
                  Vldr.IsNotNegative(harga);
    if (!isValid)
    {
        $("#totalharga").val("");
        return;
    }

    let total = parseInt(jumlah) * parseInt(harga);
    $("#totalharga").val(Rupiah.NumberToRupiah(total));
}

function showPilihTanggal()
{
    $("#ftanggal").datepicker({
        dateFormat: "yy-mm-dd",
        defaultDate: $("#tanggal").val(),
        onSelect: function (date)
        {
            $("#tanggal").val(date);
            $("#ftanggal").val(dateutil_formatInaDate(date));
        }
    }).focus();
};