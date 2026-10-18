showSelectDate = function ()
{
    var selDate = $("#tglmulai").val();

    $("#txtglmulai").datepicker({
        dateFormat: "yy-mm-dd",
        defaultDate: selDate,
        onSelect: function (date)
        {
            $("#tglmulai").val(date);
            $("#txtglmulai").val(dateutil_formatInaDate(date));
        }
    }).focus();
};

simpanTahunBuku = function ()
{
    var isValid = Vldr.InputText("tahunbuku", "Tahun Buku") &&
                  Vldr.InputText("tglmulai", "Tanggal Mulai") &&
                  Vldr.InputText("awalan", "Awalan Nomor Kuitansi");

    if (!isValid)
        return;

    if (!confirm("Data sudah benar?"))
        return;

    var qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("tahunbuku", "tahunbuku");
    qsb.addInput("tglmulai", "tglmulai");
    qsb.addInput("awalan", "awalan");
    qsb.addInput("keterangan", "keterangan");

    $.ajax({
        url: "tahunbuku2.dialog.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            var ls = JSON.parse(json);
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
};