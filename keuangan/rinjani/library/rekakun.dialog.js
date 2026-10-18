var dialogBox = null;

$(document).ready(function()
{
    dialogBox = new DialogBox("#divDialog", 500, 350);

    setTimeout(function () {
        setTimeout(setTableStyle, 100);
    }, 300);

    $("#divDaftar").animate({scrollTop: $("#divDaftar")[0].scrollHeight}, 750);
});


setTableStyle = function ()
{
    Tables('table', 1, 0);
};

setKodeRekBaru = function ()
{
    $("#idkoderek").val(0);
    $("#kode").val("");
    $("#kode").prop('readonly', false);
    $("#nama").val("");
    $("#nama").prop('readonly', false);
    $("#keterangan").val("");
    $("#imWarning").css("visibility", "hidden");

    $("#btKodeRekBaru").css("visibility", "hidden");
    $("#spTitle").html("Tambah Kode Rekening");
};

pilihKodeRek = function (kodeRek, namaRek)
{
    var container = $("#container").val();
    var kategori = $("#kategori").val();
    var subKategori = $('#subkategori').val();

    if (container === "self")
    {
        opener.acceptRekAkunDialog(kategori, subKategori, kodeRek, namaRek);
        window.close();
    }
    else
    {
        parent.acceptRekAkunDialog(kategori, subKategori, kodeRek, namaRek);
    }
};

infoKodeRek = function (kodeRek)
{
    $.ajax({
        url: "rekakun.dialog.ajax.php",
        method: "POST",
        data: "op=9845798573948573&koderek=" + kodeRek,
        success: function (content)
        {
            dialogBox.show(content);
        },
        error: function(xhr)
        {
            alert(xhr.responseText);
        }
    })
};

editKodeRek = function (id, kode, nama, keterangan)
{
    $.ajax({
        url: "rekakun.dialog.ajax.php",
        method: "POST",
        data: "op=783468764837242&kode=" + kode,
        success: function (json)
        {
            var result = $.parseJSON(json);
            if (parseInt(result[0]) < 0)
            {
                showToast(result[1], 3000, 'error', 'bottom');
                return -1;
            }

            var nData = result[0];
            if (parseInt(nData) !== 0)
            {
                // belum digunakan
                $("#kode").prop('readonly', true);
                $("#nama").prop('readonly', true);
                $("#imWarning").css("visibility", "visible");
            }
            else
            {
                // sudah digunakan
                $("#kode").prop('readonly', false);
                $("#nama").prop('readonly', false);
                $("#imWarning").css("visibility", "hidden");
            }

            $("#idkoderek").val(id);
            $("#kode").val(kode);
            $("#nama").val(nama);
            $("#keterangan").val(keterangan);

            $("#btKodeRekBaru").css("visibility", "visible");
            $("#spTitle").html("Ubah Kode Eekening");
        },
        error: function(xhr)
        {
            alert(xhr.responseText);
        }
    })
};

simpanKodeRek = function ()
{
    var kode = $("#kode").val();
    if (kode.length < 2)
    {
        alert("Kode rekening miminal 3 karakter");
        $("#kode").focus();
        return;
    }

    var nama = $("#nama").val();
    if (nama.length < 3)
    {
        alert("Nama rekening miminal 3 karakter");
        $("#nama").focus();
        return;
    }

    var idKodeRek = $("#idkoderek").val();
    var kategori = $("#kategori").val();
    var keterangan = $("#keterangan").val();

    var request = new RequestFactory();
    request.add("op", "344234234324324");
    request.add("idkoderek", idKodeRek);
    request.add("kategori", kategori);
    request.add("kode", kode);
    request.add("nama", nama);
    request.add("keterangan", keterangan);
    var qs = request.createQs();

    $.ajax({
        url: "rekakun.dialog.ajax.php",
        method: "POST",
        data: qs,
        success: function (json)
        {
            var result = $.parseJSON(json);
            if (parseInt(result[0]) < 0)
            {
                //alert(result[1]);
                showToast(result[1], 3000, 'error', 'bottom');
                return;
            }

            refreshList();
            setKodeRekBaru();

            $("#divDaftar").animate({scrollTop: $("#divDaftar")[0].scrollHeight}, 750);
            showToast(result[1], 3000, 'success', 'bottom');
        },
        error: function(xhr)
        {
            alert(xhr.responseText);
        }
    })
};

hapusKodeRek = function (kode)
{
    $.ajax({
        url: "rekakun.dialog.ajax.php",
        method: "POST",
        data: "op=783468764837242&kode=" + kode,
        success: function (json)
        {
            var result = $.parseJSON(json);
            if (parseInt(result[0]) < 0)
            {
                showToast(result[1], 3000, 'error', 'bottom');
                return -1;
            }

            var nData = result[0];
            if (parseInt(nData) !== 0)
            {
                showToast("Tidak dapat menghapus rekening karena sudah digunakan", 3000, "error", 'bottom');
                return;
            }

            if (!confirm("Hapus rekening ini"))
                return;

            $.ajax({
                url: "rekakun.dialog.ajax.php",
                method: "POST",
                data: "op=4678732648732648732&kode=" + kode,
                success: function (json)
                {
                    var result = $.parseJSON(json);
                    if (parseInt(result[0]) < 0)
                    {
                        //alert(result[1]);
                        showToast(result[1], 3000, 'error', 'bottom');
                        return;
                    }

                    refreshList();
                    setKodeRekBaru();

                    showToast(result[1], 3000, 'success', 'bottom');
                },
                error: function(xhr)
                {
                    alert(xhr.responseText);
                }
            })
        },
        error: function(xhr)
        {
            alert(xhr.responseText);
        }
    })
};

refreshList = function ()
{
    var kategori = $("#kategori").val();

    $.ajax({
        url: "rekakun.dialog.ajax.php",
        method: "POST",
        data: "op=874897498237432&kategori=" + kategori,
        success: function (data)
        {
            $("#divDaftar").html(data);
            Tables('table', 1, 0);
        },
        error: function(xhr)
        {
            alert(xhr.responseText);
        }
    })
};