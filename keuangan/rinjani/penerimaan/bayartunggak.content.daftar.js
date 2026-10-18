$(document).ready(function()
{
    if ($("#tabsiswa_daftar").length)
        Tables('tabsiswa_daftar', 1, 0);
})

function tabsiswa_pilih(nis, nama)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.add("nis", nis);
    qsb.add("nama", nama);

    parent.bayar.location.href = "bayartunggak.content.siswa.php?" + qsb.createQs();
}

function tabsiswa_changeUrut(urut)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("idkategori", "kategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.add("urut", urut);

    document.location.href = "bayartunggak.content.daftar.php?" + qsb.createQs();
}

function tabcalon_pilih(nic, nama, idcalon)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("idkategori", "idkategori");
    qsb.addInput("namakategori", "namakategori");
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("namapenerimaan", "namapenerimaan");
    qsb.add("nic", nic);
    qsb.add("nama", nama);
    qsb.add("idcalon", idcalon);

    parent.bayar.location.href = "bayartunggak.content.calon.php?" + qsb.createQs();
}

function onPrevPageSiswa()
{
    let page = parseInt($("#page").val());
    if (page == 1)
        return;

    page -= 1;
    $("#page").val(page);

    fetchChangePage("daftarsiswa", $("#page").val(), $("#dvDaftarSiswa"));
}

function onNextPageSiswa()
{
    let page = parseInt($("#page").val());
    let nPage = parseInt($("#npage").val());
    if (page == nPage)
        return;
    
    page += 1;
    $("#page").val(page);

    fetchChangePage("daftarsiswa", $("#page").val(), $("#dvDaftarSiswa"));
}

function onChangePageSiswa()
{
    fetchChangePage("daftarsiswa", $("#page").val(), $("#dvDaftarSiswa"));
}

function fetchChangePage(op, page, dvDaftar)
{
    let qsb = new QsBuilder();
    qsb.add("op", op);
    qsb.addInput("idpenerimaan", "idpenerimaan");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.add("page", page);
    qsb.addInput("urut", "urut");
    
    dvDaftar.html("memuat ..");

    $.ajax({
        url: "bayartunggak.content.daftar.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (html)
        {
            dvDaftar.html(html).hide().fadeIn(400);

            if ($("#tabsiswa_daftar").length)
                Tables('tabsiswa_daftar', 1, 0);

            parent.bayar.location.href = "blank.php";
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function onPrevPageCalonSiswa()
{
    let page = parseInt($("#page").val());
    if (page == 1)
        return;

    page -= 1;
    $("#page").val(page);

    fetchChangePage("daftarcalon", $("#page").val(), $("#dvDaftarCalon"));
}

function onNextPageCalonSiswa()
{
    let page = parseInt($("#page").val());
    let nPage = parseInt($("#npage").val());
    if (page == nPage)
        return;
    
    page += 1;
    $("#page").val(page);

    fetchChangePage("daftarcalon", $("#page").val(), $("#dvDaftarCalon"));
}

function onChangePageCalonSiswa()
{
    fetchChangePage("daftarcalon", $("#page").val(), $("#dvDaftarCalon"));
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
                if (data.usercol === "nis")
                {
                    tabsiswa_pilih(data.userid, data.username);
                }
                else if (data.usercol === "nic")
                {
                    tabcalon_pilih(data.userid, data.username, data.userreplid);
                }
            }
            else
            {
                $('#spScanInfo').html(data.message);
                parent.bayar.location.href = "blank.php";
            }
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    });
}


