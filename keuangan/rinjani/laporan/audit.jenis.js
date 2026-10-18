$(document).ready(function () {
    if ($("#table").length)
        Tables('table', 1, 0);
});

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function show_detail(lap)
{
    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("idtahunbuku", "idtahunbuku");
    qsb.addInput("namatahunbuku", "namatahunbuku");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.add("lap", lap);
    qsb.add("page", 1);

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    if (lap === 'penerimaanjtt' || lap === 'penerimaanjttcalon')
    {
        parent.content.location.href = "audit.laporan.jtt.php?" + qsb.createQs();
    }
    else if (lap === 'penerimaaniuran' || lap === 'penerimaaniurancalon')
    {
        parent.content.location.href = "audit.laporan.skr.php?" + qsb.createQs();
    }
    else if (lap === 'penerimaanlain')
    {
        parent.content.location.href = "audit.laporan.lain.php?" + qsb.createQs();
    }
    else if (lap === 'pengeluaran')
    {
        parent.content.location.href = "audit.laporan.pengeluaran.php?" + qsb.createQs();
    }
    else if (lap === 'jurnalumum')
    {
        parent.content.location.href = "audit.laporan.jurnalumum.php?" + qsb.createQs();
    }
    else if (lap === 'tabungan')
    {
        parent.content.location.href = "audit.laporan.tabungan.php?" + qsb.createQs();
    }
    else if (lap === 'tabunganp')
    {
        parent.content.location.href = "audit.laporan.tabunganp.php?" + qsb.createQs();
    }
    else if (lap === 'besarjtt' || lap === 'besarjttcalon')
    {
        parent.content.location.href = "audit.laporan.besarjtt.php?"+qsb.createQs();
    }
}