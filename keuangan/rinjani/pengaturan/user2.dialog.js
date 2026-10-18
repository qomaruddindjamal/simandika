function openSearchPegawai()
{
    newWindow("../library/daftarpegawai.dialog.php?departemen=SMA", 300, 580, 'resizable=1,scrollbars=1,status=0,toolbar=0');
}

function acceptPegawai(kelompok, json64)
{
    let data = JSON.parse(atob(json64));

    $("#nip").val(data.NIP);
    $("#nama").val(data.Nama);

    cekHasLogin();
}

function cekHasLogin()
{
    let qsb = new QsBuilder();
    qsb.add("op", "checklogin");
    qsb.addInput("nip", "nip");

    $.ajax({
        url: "user2.dialog.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                $("#haslogin").val(0);
                alert(ls[1]);
                return;
            }

            if (parseInt(ls[0]) === 0)
            {
                $("#trPassword").css("display", "table-row");
                $("#trKonfirmasi").css("display", "table-row");
                $("#haslogin").val(0);
            }
            else
            {
                $("#trPassword").css("display", "none");
                $("#trKonfirmasi").css("display", "none");
                $("#haslogin").val(1);
            }
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function change_tingkat()
{
    let qsb = new QsBuilder();
    qsb.add("op", "departemen");
    qsb.addInput("status_user", "status_user");
    qsb.addInput("orig_departemen", "orig_departemen");

    let spDepartemen = $("#spDepartemen");
    spDepartemen.html("memuat ..");

    $.ajax({
        url: "user2.dialog.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (response)
        {
            spDepartemen.html(response);
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}

function simpan()
{
    let nip = $.trim($("#nip").val());
    let nama = $.trim($("#nama").val());
    if (nip.length === 0 || nama.length === 0)
    {
        alert("Identitas pengguna belum ditentukan");
        $("#nip").focus();
        return;
    }

    let iduser = parseInt($("#iduser").val());
    let haslogin = parseInt($("#haslogin").val());
    if (iduser === 0 && haslogin === 0)
    {
        let isOk = Vldr.InputText("password", "Password", 5) &&
                   Vldr.InputText("konfirmasi", "Konfirmasi Password", 5);
        if (!isOk)
            return;

        let pass1 = $.trim($("#password").val());
        let pass2 = $.trim($("#konfirmasi").val());

        if (pass1 !== pass2)
        {
            alert("Password tidak sama");
            $("#password").focus();
            return;
        }
    }

    if (!confirm("Data sudah benar?"))
        return;

    let qsb = new QsBuilder();
    qsb.add("op", "simpan");
    qsb.addInput("iduser", "iduser");
    qsb.addInput("haslogin", "haslogin");
    qsb.addInput("nip", "nip");
    qsb.addInput("nama", "nama");
    qsb.addInput("password", "password");
    qsb.addInput("status_user", "status_user");
    qsb.addInput("departemen", "departemen");
    qsb.addInput("keterangan", "keterangan");

    let btSimpan = $("#btSimpan");
    let btTutup = $("#btTutup");
    let spInfo = $("#spInfo");

    btSimpan.prop("disabled", true);
    btTutup.prop("disabled", true);
    spInfo.css("color", "blue").html("memuat ..");

    $.ajax({
        url: "user2.dialog.ajax.php",
        method: "POST",
        data: qsb.createQs(),
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                alert(ls[1]);

                btSimpan.prop("disabled", false);
                btTutup.prop("disabled", false);
                spInfo.css("color", "red").html(ls[1]);

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