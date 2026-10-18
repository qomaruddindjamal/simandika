$(document).ready(function ()
{
    if ($("#table").length)
        Tables("table", 0, 0);
});

function refresh()
{
    document.location.reload();
}

function tambah()
{
    let url = "user2.dialog.php?iduser=0";
    newWindow(url, "TambahPengguna2", 550, 400);
}

function edit(iduser)
{
    let url = "user2.dialog.php?iduser=" + iduser;
    newWindow(url, "UbahPengguna2", 550, 400);
}

function hapus(iduser)
{
    if (!confirm("Hapus pengguna ini?"))
        return;

    let qsb = new QsBuilder();
    qsb.add("op", "hapus");
    qsb.add("iduser", iduser);

    $.ajax({
        url: "user2.ajax.php",
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

function cetak()
{
    let qsb = new QsBuilder();
    qsb.add("departemen", "ALL");

    let addr = "user2.cetak.php?" + qsb.createQs();
    newWindow(addr, 'CetakDaftarPengguna2','790','630','resizable=1,scrollbars=1,status=0,toolbar=0');
}

function getPageContent(section)
{
    if (section === "table")
    {
        if ($("#dvTableContent").length)
            return $("#dvTableContent").html();
        return "-";
    }
}