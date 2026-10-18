$(document).ready(function ()
{
    if ($("#table").length)
        Tables("table", 1, 0);
});

async function show_detail(kode, nama)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.addInput("kategori", "kategori");
    qsb.add("koderek", kode);
    qsb.add("namarek", nama);
    qsb.add("urut", "nokas");
    qsb.add("page", 1);

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "bukubesar.laporan.php?" + qsb.createQs();
}

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

function cetak()
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    newWindow('bukubesar.rekap.cetak.php?'+qsb.createQs(), 'CetakRekapBukuBesar431241','790','630','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}

function getPageContent(section)
{
    if (section === "departemen")
        return $("#departemen").val();

    if (section === "tahunbuku")
        return $("#namatahunbuku").val();

    if (section === "tanggal1")
        return $("#tanggal1").val();

    if (section === "tanggal2")
        return $("#tanggal2").val();

    if (section === "kategori")
    {
        let kategori = $("#kategori").val();
        if (kategori === "ALL")
            kategori = "Semua Kategori";
        return kategori;
    }

    if (section === "laporan")
        return $("#dvRekap").html();

    return "-";
}