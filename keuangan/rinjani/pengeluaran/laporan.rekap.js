$(document).ready(function ()
{
    if ($("#table").length)
    {
        $("#table").hide().fadeIn(400);
        Tables("table", 1, 0);
    }
});

function refresh()
{
    document.location.reload();
}

async function show_detail(id, nama)
{
    let qsb = new QsBuilder();
    qsb.add("idpengeluaran", id);
    qsb.add("namapengeluaran", nama);
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.add("page", 1);

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "laporan.rincian.php?" + qsb.createQs();
}

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('laporan.rekap.cetak.php?'+qsb.createQs(), 'CetakRekapPengeluaran','780','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function getPageContent(section)
{
    if (section === "departemen")
        return $("#departemen").val();

    if (section === "tanggal1")
        return $("#tanggal1").val();

    if (section === "tanggal2")
        return $("#tanggal2").val();

    if (section === "laporan")
        return $("#dvLaporan").html();

    return "-";
}