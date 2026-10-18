$(document).ready(function ()
{
    $("#tabCalonSiswa").tabs();

    tabcsiswa_setAcceptResult(acceptCalonSiswa);
});

function acceptCalonSiswa(kelompok, json64)
{
    let data = JSON.parse(atob(json64));

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("tanggal1", "tanggal1");
    qsb.addInput("tanggal2", "tanggal2");
    qsb.add("userid", data.NIC);
    qsb.add("username", data.Nama);

    parent.content.location.href = "bayarcalon.laporan.php?" + qsb.createQs();
}