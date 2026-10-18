var helpBox = null;

$(document).ready(function ()
{
    helpBox = new DialogBox("#divHelpDialog", 500, 500);
});

change_dep = function ()
{
    let dep = $("#departemen").val();

    document.location.href = "multi.pengeluaran.header.php?departemen="+dep;
    parent.content.location.href = "blank.php";
};

StartExpenditure = function()
{
    let departemen = $.trim($("#departemen").val());
    if (departemen.length === 0)
    {
        alert("Belum ada data Departemen!")
        return;
    }

    let idtahunbuku = parseInt($("#idtahunbuku").val());
    if (idtahunbuku === 0)
    {
        alert("Belum ada Tahun Buku yang berjalan di Departemen terpilih!")
        return;
    }

    let tahunbuku = $("#tahunbuku").val();

    let qsb = new QsBuilder();
    qsb.add("departemen", departemen);
    qsb.add("idtahunbuku", idtahunbuku);
    qsb.add("tahunbuku", tahunbuku);

    parent.content.location.href = "multi.pengeluaran.content.php?" + qsb.createQs();
};

function showHelp()
{
    newWindow('../help/pl_transaksi.html', 'TransaksiPengeluaranHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0'		)
}