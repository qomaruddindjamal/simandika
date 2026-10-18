function hoverKategori(id)
{
    $("#spKategori-" + id).css("visibility", "visible");
}

function leaveKateegori(id)
{
    $("#spKategori-" + id).css("visibility", "hidden");
}

function hoverKelompok(id)
{
    $("#spKelompok-" + id).css("visibility", "visible");
}

function leaveKelompok(id)
{
    $("#spKelompok-" + id).css("visibility", "hidden");
}

function tambahKategori()
{
    newWindow("kategori.dialog.php?id=0",'TambahKategori11', 500, 300, 'resizable=1,scrollbars=1,status=0,toolbar=0');
}

function editKategori(id)
{
    newWindow("kategori.dialog.php?id=" + id, 'UbahKategori22', 500, 300, 'resizable=1,scrollbars=1,status=0,toolbar=0');
}

function hapusKategori(id)
{
    if (!confirm("Hapus kategori ini?"))
        return;

    let qsb = new QsBuilder();
    qsb.add("op", "hapuskategori");
    qsb.add("id", id);

    $.ajax({
        url: "inventori.group.ajax.php",
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
    });
}

function refresh()
{
    document.location.reload();
}

function tambahKelompok(idgroup, namagroup)
{
    let qsb = new QsBuilder();
    qsb.add("idgroup", idgroup);
    qsb.add("namagroup", namagroup);
    qsb.add("id", 0);

    newWindow("kelompok.dialog.php?" + qsb.createQs(),'TambahKelompok231', 500, 300, 'resizable=1,scrollbars=1,status=0,toolbar=0');
}

function editKelompok(idgroup, namagroup, id)
{
    let qsb = new QsBuilder();
    qsb.add("idgroup", idgroup);
    qsb.add("namagroup", namagroup);
    qsb.add("id", id);

    newWindow("kelompok.dialog.php?" + qsb.createQs(),'EditKelompok342', 500, 300, 'resizable=1,scrollbars=1,status=0,toolbar=0');
}

function hapusKelompok(id)
{
    if (!confirm("Hapus kelompok ini?"))
        return;

    let qsb = new QsBuilder();
    qsb.add("op", "hapuskelompok");
    qsb.add("id", id);

    $.ajax({
        url: "inventori.group.ajax.php",
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
    });
}

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function selectKelompok(idGroup, namaGroup, idKelompok, namaKelompok)
{
    let qsb = new QsBuilder();
    qsb.add("idgroup", idGroup);
    qsb.add("namagroup", namaGroup);
    qsb.add("idkelompok", idKelompok);
    qsb.add("namakelompok", namaKelompok);
    qsb.add("status", 1);

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "inventori.content.php?" + qsb.createQs();
}

function showHelp()
{
    newWindow('../help/iv_about.html', 'InventoryHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0')
}