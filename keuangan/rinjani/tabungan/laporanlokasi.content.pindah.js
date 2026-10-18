function simpanPindahLokasi()
{
    let jumlah = parseInt($("#jumlah").val());
    let saldo = parseInt($("#saldo").val());
    if (jumlah === 0 || saldo === 0)
    {
        alert("Tidak ada transaksi tabungan yang dapat dipindahkan lokasi dana nya");
        return;
    }

    let nSelect = parseInt($("#lokasitujuan").find("option").length);
    if (nSelect === 0)
    {
        alert("Data lokasi dana tujuan tidak tersedia");
        return;
    }

    if (!Vldr.InputText("alasan", "Alasan Pemindahan Dana", 10))
        return;

    if (!confirm("Data sudah benar?"))
        return;

    let btSimpan = $("#btSimpan");
    btSimpan.prop("disabled", true);

    let spInfo = $("#spInfo");
    spInfo.css("color", "blue").html("memuat ..");

    let qsb = new QsBuilder();
    qsb.add("op", "pindah");
    qsb.add("saldo", saldo);
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtabungan", "idtabungan");
    qsb.addInput("namatabungan", "namatabungan");
    qsb.addInput("kelompok", "kelompok");
    qsb.addInput("namalokasi", "namalokasi");
    qsb.addInput("kodelokasi", "kodelokasi");
    qsb.addInput("stidlist64", "stidlist64");
    qsb.addInput("lokasitujuan", "lokasitujuan");
    qsb.addInput("keterangan", "keterangan");
    qsb.addInput("alasan", "alasan");

    $.ajax({
        url: "laporanlokasi.content.pindah.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                spInfo.css("color", "red").html(ls[1]);
                btSimpan.prop("disabled", false);

                alert(ls[1]);
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