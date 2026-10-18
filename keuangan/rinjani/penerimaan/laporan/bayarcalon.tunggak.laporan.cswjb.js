var dialogBox = null;

$(document).ready(function ()
{
    if ($("#table").length !== 0)
        Tables('table', 1, 0);

    if ($("#dvLaporan").length)
        $("#dvLaporan").hide().fadeIn(500);

    dialogBox = new DialogBox("#divDialog", 500, 350);
});

function refresh()
{
    onChangePage();
}

function showFormatPesanDialog()
{
    $("#txformat").val( $("#formatnotif").val() );
    dialogBox.show();
}

function closeFormatNotifDialog()
{
    dialogBox.close();
}

function saveFormatNotif()
{
    $("#formatnotif").val( $.trim($("#txformat").val()) );
    dialogBox.close();
}

function sendFormatNotif()
{
    if (!confirm("Kirim Pesan Notifikasi Tunggakan?"))
        return;

    let nRow = parseInt($("#nrow").val());
    if (nRow === 0)
        return;

    let n = 0;
    let qsb = new QsBuilder();
    qsb.add("op", "sendnotif");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("formatnotif", "formatnotif");
    for(let i = 1; i <= nRow; i++)
    {
        if (!$("#ckKirim-" + i).is(":checked"))
            continue;

        n += 1;
        qsb.add("tunggakan-" + n, $("#tunggakan-" + i).val());
        qsb.add("nama-" + n, $("#nama-" + i).val());
        qsb.add("nic-" + n, $("#nic-" + i).val());
        qsb.add("idcalon-" + n, $("#idcalon-" + i).val());
    }
    qsb.add("ndata", n);

    if (n === 0)
    {
        alert("Pilih minimal satu siswa untuk pengiriman notifikasi tunggakan");
        return;
    }

    let spInfo = $("#spInfo");
    let btKirim = $("#btKirim");

    btKirim.prop("disabled", true);
    spInfo.css('color', '#666').html("memuat ..");

    $.ajax({
        url: "bayarcalon.tunggak.laporan.cswjb.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (data)
        {
            btKirim.prop("disabled", false);

            let lsData = JSON.parse(data);
            if (parseInt(lsData[0]) < 0)
            {
                spInfo.css('color', 'red').html(lsData[1]);
                showToast(lsData[1], 3000, 'error', 'bottom');
                return;
            }

            spInfo.css('color', 'blue').html(lsData[1]);
            showToast(lsData[1], 3000, 'success', 'bottom');
        },
        error: function (xhr)
        {
            btKirim.prop("disabled", false);
            alert(xhr.responseText);
        }
    })
}

function refresh()
{
    onChangePage();
}

function onChangePage()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idproses", "idproses");
    qsb.addInput("namaproses", "namaproses");
    qsb.addInput("idkelompok", "idkelompok");
    qsb.addInput("namakelompok", "namakelompok");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("telat", "telat");
    qsb.addInput("tanggal", "tanggal");
    qsb.addInput("urut", "urut");
    qsb.addInput("page", "page");

    document.location.href = "bayarcalon.tunggak.laporan.cswjb.php?" + qsb.createQs();
}

function onChangeUrut(urut)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idproses", "idproses");
    qsb.addInput("namaproses", "namaproses");
    qsb.addInput("idkelompok", "idkelompok");
    qsb.addInput("namakelompok", "namakelompok");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("telat", "telat");
    qsb.addInput("tanggal", "tanggal");
    qsb.add("urut", urut);
    qsb.addInput("page", "page");

    document.location.href = "bayarcalon.tunggak.laporan.cswjb.php?" + qsb.createQs();
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('bayarcalon.tunggak.laporan.cswjb.cetak.php?'+qsb.createQs(), 'CetakBayarCalonSiswaTunggak','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function excel()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idproses", "idproses");
    qsb.addInput("namaproses", "namaproses");
    qsb.addInput("idkelompok", "idkelompok");
    qsb.addInput("namakelompok", "namakelompok");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.addInput("telat", "telat");
    qsb.addInput("tanggal", "tanggal");
    qsb.addInput("urut", "urut");

    newWindow('bayarcalon.tunggak.laporan.cswjb.excel.php?'+qsb.createQs(), 'ExcelBayarCalonSiswaTunggak','1000','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function getPageContent(section)
{
    if (section === "departemen")
        return $("#departemen").val();

    if (section === "proses")
        return $("#namaproses").val();

    if (section === "kelompok")
        return $("#namakelompok").val();

    if (section === "kategori")
        return $("#namakategori").val();

    if (section === "penerimaan")
        return $("#namapenerimaan").val();

    if (section === "laporan")
        return $("#dvLaporan").html();

    if (section === "telat")
        return $("#telat").val();

    if (section === "tanggal")
        return $("#tanggal").val();

    return "-";
}

function onCkKirimChange()
{
    let nRow = parseInt($("#nrow").val());

    let isChecked = $("#ckKirimToggle").is(":checked");
    for(let i = 1; i <= nRow; i++)
    {
        $("#ckKirim-" + i).prop("checked", isChecked);
    }
}

function showInfoCalonSiswa(nic)
{
    var qsb = new QsBuilder();
    qsb.add("nic", nic);

    newWindow('../../library/infocalonsiswa.dialog.php?'+qsb.createQs(), 'InformasiCalonSiswa','620','520','resizable=1,scrollbars=1,status=0,toolbar=0');
}