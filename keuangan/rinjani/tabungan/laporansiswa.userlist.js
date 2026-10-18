$(document).ready(function ()
{
    $("#tabSiswa").tabs();

    tabsiswa_setAcceptResult(acceptSiswa);
});

async function acceptSiswa(kelompok, json64)
{
    let data = JSON.parse(atob(json64));

    var qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.add("userid", data.NIS);
    qsb.add("username", data.Nama);

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "laporansiswa.laporan.php?" + qsb.createQs();
}

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}