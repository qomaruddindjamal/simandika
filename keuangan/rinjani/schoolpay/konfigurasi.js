var helpBox = null;

$(document).ready(function() {
    setTimeout(function () {
        setTimeout(setTableStyle, 100);
    }, 300);

    helpBox = new DialogBox("#divHelpDialog", 620, 520);
});

function showHelp()
{
    $.ajax({
        url: "../help/sp_konfigurasi.html",
        success: function (content)
        {
            helpBox.show(content);

            setTimeout(function () {
                $("#divHelpDialog").scrollTop(0);
            }, 750)
        },
        error: function(xhr)
        {
            alert(xhr.responseText);
        }
    })
}

setTableStyle = function () {
    Tables('tablesis', 1, 0);
    Tables('tablepeg', 1, 0);
};

atur = function (idPt, dept)
{
    var addr = "konfigurasi.dialog.php?idpt=" + idPt + "&dept=" + dept;
    newWindow(addr, 'KonfigurasiPayment', '750', '550', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

hapus = function (idPt)
{
    if (idPt === 0)
        return;

    if (!confirm("Hapus konfigurasi ini?"))
        return;

    $.ajax({
        url: "konfigurasi.ajax.php",
        data: "op=9872894789324&idpt=" + idPt,
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                alert(ls[1]);
                return;
            }

            location.reload();
        },
        error: function (xhr) {
            alert(xhr.responseText);
        }
    })
};

aturPeg = function (idPt)
{
    var addr = "konfigurasi.pegawai.dialog.php?idpt=" + idPt;
    newWindow(addr, 'KonfigurasiPegPayment', '750', '550', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

hapusPeg = function (idPt)
{
    if (idPt === 0)
        return;

    if (!confirm("Hapus konfigurasi ini?"))
        return;

    $.ajax({
        url: "konfigurasi.ajax.php",
        data: "op=894753948579435&idpt=" + idPt,
        success: function (json)
        {
            let ls = JSON.parse(json);
            if (parseInt(ls[0]) < 0)
            {
                alert(ls[1]);
                return;
            }

            location.reload();
        },
        error: function (xhr) {
            alert(xhr.responseText);
        }
    })
};