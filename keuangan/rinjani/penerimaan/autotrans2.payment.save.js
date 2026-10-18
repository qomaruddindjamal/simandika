var win = null;

OpenWindow = function(mypage, myname, w, h, features)
{
    let winl = (screen.width-w)/2;
    let wint = (screen.height-h)/2;
    if (winl < 0) winl = 0;
    if (wint < 0) wint = 0;

    let settings = 'height=' + h + ',';
    settings += 'width=' + w + ',';
    settings += 'top=' + wint + ',';
    settings += 'left=' + winl + ',';
    settings += features;

    win = window.open(mypage,myname,settings);
    win.window.focus();
};

GetReportDetail = function()
{
    return $("#divReportDetail").html();
};

PrintDetail = function()
{
    /*
    let dept = $("#departemen").val();
    let jenis = $("#kelompok").val();
    let noid = $("#studentid").val();
    let jumlah = $("#total").val();
    let ktransaksi = $("#ktransaksi").val();

    let addr = "multitrans2.content.print.detail.php?departemen="+dept+"&jenis="+jenis+"&noid="+noid+"&jumlah="+jumlah+"&ktransaksi="+ktransaksi;
    */

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("jenis", "kelompok");
    qsb.addInput("noid", "studentid");
    qsb.addInput("jumlah", "total");
    qsb.addInput("ktransaksi", "ktransaksi");

    let addr = "multitrans2.content.print.detail.php?" + qsb.createQs();
    OpenWindow(addr, 'PrintDetail', '790', '590', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};

PrintCompact = function()
{
    /*
    let dept = $("#departemen").val();
    let jenis = $("#kelompok").val();
    let noid = $("#studentid").val();
    let jumlah = $("#total").val();
    let ktransaksi = $("#ktransaksi").val();
    let paymentlist = $("#paymentlist").val();

    let addr = "multitrans2.content.print.compact.php?departemen="+dept+"&jenis="+jenis+"&noid="+noid+"&jumlah="+jumlah+"&ktransaksi="+ktransaksi+"&paymentlist="+paymentlist;
    */

    let qsb = new QsBuilder();
    qsb.addInput("departemen", "departemen");
    qsb.addInput("jenis", "kelompok");
    qsb.addInput("noid", "studentid");
    qsb.addInput("jumlah", "total");
    qsb.addInput("ktransaksi", "ktransaksi");
    qsb.addInput("paymentlist", "paymentlist");

    let addr = "multitrans2.content.print.compact.php?" + qsb.createQs();
    OpenWindow(addr, 'PrintCompact', '390', '590', 'resizable=1,scrollbars=1,status=0,toolbar=0');
};