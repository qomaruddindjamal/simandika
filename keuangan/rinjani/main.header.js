$(document).ready(function()
{
    $('.menu-item').on('click', function()
    {
        $('.menu-item').removeClass('active');
        $(this).addClass('active');

        // Get clicked menu id
        let clickedId = this.id;
        showPage(clickedId);
    });
});

function showPage(mnPage)
{
    switch (mnPage)
    {
        case "mnReferensi":
            parent.maincontent.location.href = "referensi/referensi.php";
            break;
        case "mnPenerimaan":
            parent.maincontent.location.href = "penerimaan/penerimaan.php";
            break;
        case "mnPengeluaran":
            parent.maincontent.location.href = "pengeluaran/pengeluaran.php";
            break;
        case "mnJurnalUmum":
            parent.maincontent.location.href = "jurnal/jurnalumum.php";
            break;
        case "mnTabunganSiswa":
            parent.maincontent.location.href = "tabungan/tabungan.php";
            break;
        case "mnTabunganPegawai":
            parent.maincontent.location.href = "tabunganp/tabunganp.php";
            break;
        case "mnSchoolPay":
            parent.maincontent.location.href = "schoolpay/schoolpay.php";
            break;
        case "mnLaporanKeuangan":
            parent.maincontent.location.href = "laporan/laporan.php";
            break;
        case "mnPengaturan":
            parent.maincontent.location.href = "pengaturan/pengaturan.php";
            break;
        case "mnInventori":
            parent.maincontent.location.href = "inventori/inventori.php";
            break;
    }
}

function confirmLogout()
{
    if (!confirm("Keluar dari JIBAS Keuangan?"))
        return;

    $.ajax({
        url: "logout.php",
        success: function (json)
        {
            top.window.location = 'login.php';
        },
        error: function (xhr)
        {
            alert(xhr.responseText);
        }
    })
}