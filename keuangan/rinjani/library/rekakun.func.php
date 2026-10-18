<?php
function CountRekAkunUsage($db, $kodeRek)
{
    $sql = "SELECT
              (SELECT COUNT(replid) FROM jbsfina.datapenerimaan WHERE rekkas = '$kodeRek' OR rekpendapatan = '$kodeRek' OR rekpiutang = '$kodeRek') +
              (SELECT COUNT(replid) FROM jbsfina.datapengeluaran WHERE rekdebet = '$kodeRek' OR rekkredit = '$kodeRek') +
              (SELECT COUNT(replid) FROM jbsfina.datatabungan WHERE rekkas = '$kodeRek' OR rekutang = '$kodeRek') +
              (SELECT COUNT(replid) FROM jbsfina.datatabunganp WHERE rekkas = '$kodeRek' OR rekutang = '$kodeRek') +
              (SELECT COUNT(id) FROM jbsfina.pgservicefee2 WHERE rekkas = '$kodeRek' OR rekpendapatan = '$kodeRek') +
              (SELECT COUNT(replid) FROM jbsfina.paymenttabungan WHERE rekkasvendor = '$kodeRek' OR rekutangvendor = '$kodeRek')";
    $res = $db->QueryDb($sql);
    $row = mysqli_fetch_row($res);
    return $row[0];
}

function ListRekAkunUsage($db, $kodeRek)
{
    $sql = "SELECT GROUP_CONCAT(nama SEPARATOR ', ')
              FROM (
                    SELECT nama FROM jbsfina.datapenerimaan WHERE rekkas = '$kodeRek' OR rekpendapatan = '$kodeRek' OR rekpiutang = '$kodeRek'
                    UNION
                    SELECT nama FROM jbsfina.datapengeluaran WHERE rekdebet = '$kodeRek' OR rekkredit = '$kodeRek'
                    UNION
                    SELECT nama FROM jbsfina.datatabungan WHERE rekkas = '$kodeRek' OR rekutang = '$kodeRek'
                    UNION
                    SELECT nama FROM jbsfina.datatabunganp WHERE rekkas = '$kodeRek' OR rekutang = '$kodeRek'
                    UNION
                    SELECT nama FROM jbsfina.pgservicefee2 WHERE rekkas = '$kodeRek' OR rekpendapatan = '$kodeRek'
                    UNION
                    SELECT 'Pembayaran Vendor SchoolPay' AS nama FROM jbsfina.paymenttabungan WHERE rekkasvendor = '$kodeRek'  OR rekutangvendor = '$kodeRek'
                   ) AS x";
    $res = $db->QueryDb($sql);
    $row = mysqli_fetch_row($res);
    return $row[0];
}
?>