async function showLaporan()
{
    if ($("#departemen option").length === 0)
        return;

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");

    parent.content.location.href = "blank.php?showwait=1";

    await pause(200);

    parent.content.location.href = "laporanlokasi.content.php?" + qsb.createQs();
}

function onChangeDept()
{
    showBlankPage();
}

function showBlankPage()
{
    parent.content.location.href = "blank.php";
}

function showHelp()
{
    newWindow('../help/ts_saldolokasi.html', 'LapSaldoTabungaLokasiHelp','620','520','resizable=1,scrollbars=1,status=0,toolbar=0')
}

function pause(ms)
{
    return new Promise(resolve => setTimeout(resolve, ms));
}