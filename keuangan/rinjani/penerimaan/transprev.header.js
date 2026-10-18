$( document ).ready(function() {
    setTimeout(function () {
        $('#txBarcode').focus();
    }, 300);
});

function scanBarcode(e)
{
    let keycode = (e.keyCode ? e.keyCode : e.which);
    if (keycode !== 13)
        return;

    let kode = $.trim($('#txBarcode').val());
    if (kode.length === 0)
        return;

    let departemen = $('#departemen').val();
    if (departemen.length === 0)
        return;

    $('#spScanInfo').html("");

    let qsb = new QsBuilder();
    qsb.add("kode", kode);
    qsb.add("departemen", departemen);

    $.ajax({
        url: "multitrans2.barcode.php",
        type: 'GET',
        data: qsb.createQs(),
        success: function (response)
        {
            $('#txBarcode').val('');

            let dataCard = $.parseJSON(response);
            if (dataCard.status === "1")
            {
                $("#kelompok").val(dataCard.data);
                $("#noid").val(dataCard.noid);
                $("#nama").val(dataCard.nama);
                $("#kelas").val(dataCard.kelas);

                StartPayment();
            }
            else
            {
                $('#spScanInfo').html(dataCard.message);
            }
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}

onChangeDept = function ()
{
    let dep = $("#departemen").val();

    document.location.href = "transprev.header.php?departemen="+dep;
    showBlankPage();

    $("#kelompok").val("");
    $("#noid").val("");
    $("#nama").val("");
};

function showBlankPage()
{
    parent.content.location.href = "blank.php";
}

function onSelKelompokChange()
{
    showBlankPage();

    $("#kelompok").val("");
    $("#noid").val("");
    $("#nama").val("");
}

SearchUser = function()
{
    let selkelompok = $("#selkelompok").val();

    let qsb = new QsBuilder();
    qsb.add("departemen", $("#departemen").val());

    let addr = "";
    if (selkelompok === "siswa")
        addr = "../library/daftarsiswa.dialog.php?" + qsb.createQs();
    else
        addr = "../library/daftarcalonsiswa.dialog.php?" + qsb.createQs();

    newWindow(addr, 'CariUser', '550', '590', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

function acceptSiswa(kelompok, json64)
{
    let data = JSON.parse(atob(json64));

    AcceptSearch("siswa", data.NIS, data.Nama, data.Kelas);
}

function acceptCalonSiswa(kelompok, json64)
{
    let data = JSON.parse(atob(json64));

    AcceptSearch("calon", data.NIC, data.Nama, data.Kelompok);
}

AcceptSearch = function(data, noid, nama, kelas)
{
    $("#kelompok").val(data);
    $("#noid").val(noid);
    $("#nama").val(nama);
    $("#kelas").val(kelas);

    StartPayment();
};

StartPayment = function()
{
    let departemen = $.trim($("#departemen").val());
    if (departemen.length === 0)
    {
        alert("Belum ada data Departemen!")
        return;
    }

    if ($("#tahunbuku option").length === 0)
    {
        alert("Belum ada data Tahun Buku");
        return;
    }

    let idtahunbuku = $("#tahunbuku").val();
    let kelompok = $("#kelompok").val();
    let noid = $("#noid").val();
    let nama = $("#nama").val();
    let kelas = $("#kelas").val();
    if ($.trim(noid).length === 0)
    {
        alert("Anda belum menentukan Siswa/Calon Siswa!")
        return;
    }

    let qsb = new QsBuilder();
    qsb.add("departemen", departemen);
    qsb.add("idtahunbuku", idtahunbuku);
    qsb.add("kelompok", kelompok);
    qsb.add("noid", noid);
    qsb.add("nama", nama);
    qsb.add("kelas", kelas);

    parent.content.location.href = "../penerimaan/transprev.content.php?" + qsb.createQs();
};

function showHelp()
{
    newWindow('../help/pn_transprev.html', 'BayarSisaTunggakanHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}