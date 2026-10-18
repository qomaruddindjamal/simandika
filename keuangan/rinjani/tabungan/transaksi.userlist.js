$(document).ready(function ()
{
    initUi();
    tabsiswa_setAcceptResult(acceptSiswa);
});

function acceptSiswa(kelompok, json64)
{
    let data = JSON.parse(atob(json64));

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("jsontabungan", "jsontabungan");
    qsb.add("userid", data.NIS);
    qsb.add("username", data.Nama);

    parent.content.location.href = "transaksi.tabungan.php?" + qsb.createQs();
}

function initUi()
{
    $("#txBarcode").focus();

    if ($("#tabSiswa").length)
        $("#tabSiswa").tabs();
}

function scanBarcode(e)
{
    let keycode = (e.keyCode ? e.keyCode : e.which);
    if (keycode !== 13)
        return;

    let kode = $.trim($('#txBarcode').val());
    if (kode.length === 0)
        return;

    let qsb = new QsBuilder();
    qsb.add("idkategori", "TABS");
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

            let data = $.parseJSON(response);
            if (parseInt(data.status) === 1)
            {
                qsb = new QsBuilder();
                qsb.add("userid", data.userid);
                qsb.add("username", data.username);
                qsb.addInput("departemen", "departemen");
                qsb.addInput("idtahunbuku", "idtahunbuku");
                qsb.addInput("namatahunbuku", "namatahunbuku");
                qsb.addInput("jsontabungan", "jsontabungan");

                parent.content.location.href = "transaksi.tabungan.php?" + qsb.createQs();
            }
            else
            {
                $('#spScanInfo').html(data.message);
                parent.content.location.href = "blank.php";
            }
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}