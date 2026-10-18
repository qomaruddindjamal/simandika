$(document).ready(function ()
{
    initUi();

    let usergroup = $("#usergroup").val();

    if (usergroup === "siswa")
        tabsiswa_setAcceptResult(acceptSiswa);
    else
        tabcsiswa_setAcceptResult(acceptCalonSiswa);
});

function acceptSiswa(kelompok, json64)
{
    let data = JSON.parse(atob(json64));

    var qsb = new QsBuilder();
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("usergroup", "usergroup");
    qsb.add("userid", data.NIS);
    qsb.add("username", data.Nama);

    parent.content.location.href = "pembayaran.decide.php?" + qsb.createQs();
}

function acceptCalonSiswa(kelompok, json64)
{
    let data = JSON.parse(atob(json64));

    var qsb = new QsBuilder();
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("usergroup", "usergroup");
    qsb.add("userid", data.NIC);
    qsb.add("username", data.Nama);

    parent.content.location.href = "pembayaran.decide.php?" + qsb.createQs();
}

function initUi()
{
    $("#txBarcode").focus();

    if ($("#tabSiswa").length)
    {
        $("#tabSiswa").tabs();
    }
    else
    {
        if ($("#tabCalonSiswa").length)
        {
            $("#tabCalonSiswa").tabs();
        }
    }
}

function scanBarcode(e)
{
    var keycode = (e.keyCode ? e.keyCode : e.which);
    if (keycode !== 13)
        return;

    var kode = $.trim($('#txBarcode').val());
    if (kode.length === 0)
        return;

    var qsb = new QsBuilder();
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("departemen", "departemen");
    qsb.add("kode", kode);

    $('#spScanInfo').html("");
    $.ajax({
        url: "../library/scanbarcode.ajax.php",
        type: 'POST',
        data: qsb.createQs(),
        success: function (response)
        {
            $('#txBarcode').val('');

            var data = $.parseJSON(response);
            if (parseInt(data.status) === 1)
            {
                qsb = new QsBuilder();
                qsb.add("userid", data.userid);
                qsb.add("username", data.username);
                qsb.addInput("idkategori", "idkategori");
                qsb.addInput("departemen", "departemen");
                qsb.addInput("idtahunbuku", "idtahunbuku");
                qsb.addInput("idpenerimaan", "idpenerimaan");
                qsb.addInput("usergroup", "usergroup");

                parent.content.location.href = "pembayaran.decide.php?" + qsb.createQs();
            }
            else
            {
                $('#spScanInfo').html(data.message);
                parent.content.location.href = "pembayaran.blank.php";
            }
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}



