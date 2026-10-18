$(document).ready(function ()
{
    if ($("#dvLaporan").length)
        $("#dvLaporan").hide().fadeIn(400);
});

function tambahBarang()
{
    let qsb = new QsBuilder();
    qsb.addInput("idkelompok", "idkelompok");
    qsb.addInput("namakelompok", "namakelompok");
    qsb.add("id", 0);

    newWindow("barang.dialog.php?" + qsb.createQs(),'TambahBarang3213', 500, 500, 'resizable=1,scrollbars=1,status=0,toolbar=0');
}

function refresh()
{
    let qsb = new QsBuilder();
    qsb.addInput("idgroup", "idgroup");
    qsb.addInput("namagroup", "namagroup");
    qsb.addInput("idkelompok", "idkelompok");
    qsb.addInput("namakelompok", "namakelompok");
    qsb.addInput("status", "status");

    document.location.href = "inventori.content.php?" + qsb.createQs();
}

function hoverBarang(id)
{
    $("#dvBarang-" + id).css({"background" : "#fffcca", "border" : "2px #d8d277 solid"});
}

function leaveBarang(id)
{
    $("#dvBarang-" + id).css({"background" : "", "border" : "2px #eaf4ff solid"});
}

function aktifBarang(id)
{
    let status = parseInt($("#status-" + id).val());
    let newAktif = status === 1 ? 0 : 1;

    if (newAktif === 0)
    {
        if (!confirm("Non aktif kan barang ini?"))
            return;
    }

    let qsb = new QsBuilder();
    qsb.add("op", "setaktif");
    qsb.add("id", id);
    qsb.add("newaktif", newAktif);

    $.ajax({
        url: "inventori.content.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                alert(ls[1]);
                return;
            }

            if (newAktif === 0)
            {
                $("#status-" + id).val(0);
                $("#imStatus-" + id).attr("src", "../images/ico/nonaktif.png");
            }
            else
            {
                $("#status-" + id).val(1);
                $("#imStatus-" + id).attr("src", "../images/ico/aktif.png");
            }

            //$("#dvBarang-" + id).css("visibility", "hidden");
            //$("#dvBarangMenu-" + id).css("visibility", "hidden");
            $("#dvBarang-" + id).remove();
            $("#dvBarangMenu-" + id).remove();

        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }

    })

}

function ubahBarang(id)
{
    let qsb = new QsBuilder();
    qsb.addInput("idkelompok", "idkelompok");
    qsb.addInput("namakelompok", "namakelompok");
    qsb.add("id", id);

    newWindow("barang.dialog.php?" + qsb.createQs(), 'UbahBarang3213', 500, 500, 'resizable=1,scrollbars=1,status=0,toolbar=0');
}

function hapusBarang(id)
{
    if (!confirm("Hapus barang ini?"))
        return;
    
    let qsb = new QsBuilder();
    qsb.add("op", "hapus");
    qsb.add("id", id);

    $.ajax({
        url: "inventori.content.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                alert(ls[1]);
                return;
            }

            refresh();
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }

    })
}

function viewDetail(id)
{
    let qsb = new QsBuilder();
    qsb.addInput("idgroup", "idgroup");
    qsb.addInput("namagroup", "namagroup");
    qsb.addInput("idkelompok", "idkelompok");
    qsb.addInput("namakelompok", "namakelompok");
    qsb.addInput("status", "status");
    qsb.add("id", id);

    newWindow("barang.view.php?" + qsb.createQs(), 'ViewBarang4343', 780, 640, 'resizable=1,scrollbars=1,status=0,toolbar=0');
}

function cetak()
{
    newWindow('inventori.content.cetak.php', 'CetakDaftarInventori1323','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function getPageContent(section)
{
    if (section === "kategori")
        return $("#namagroup").val();

    if (section === "kelompok")
        return $("#namakelompok").val();

    if (section === "laporan")
        return $("#dvLaporan").html();

    return "-";
}

function hoverBarang(id)
{
    $("#dvBarangMenu-" + id).css("visibility", "visible");
}

function leaveBarang(id)
{
    $("#dvBarangMenu-" + id).css("visibility", "hidden");
}
