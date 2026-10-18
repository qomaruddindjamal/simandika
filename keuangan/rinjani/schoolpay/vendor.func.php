<?php
function SetVendorAktif()
{
    $db = new Db();
    try
    {
        $db->Open();

        $vendorReplid = $_REQUEST["replid"];
        $newAktif = $_REQUEST["newaktif"];

        $sql = "UPDATE jbsfina.vendor 
                   SET aktif = $newAktif 
                 WHERE replid = $vendorReplid";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k9nk7") ]);
    }
    finally
    {
        $db->Close();
    }
}


function createJsonReturn($status, $message)
{
    $ret = array($status, $message);
    return json_encode($ret);
}

function HapusVendor()
{
    $db = new Db();
    try
    {
        $db->Open();

        $vendorId = $_REQUEST["vendorid"];

        $sql = "SELECT COUNT(replid) FROM jbsfina.paymenttrans WHERE vendorid = '$vendorId'";
        $nData = $db->FetchSingle($sql, 0);
        if ($nData != 0)
            return json_encode([-1, "Tidak dapat menghapus vendor ini karena sudah digunakan dalam transaksi!"]);

        $sql = "DELETE FROM jbsfina.vendoruser 
                 WHERE vendorid = '$vendorId'";
        $db->QueryDb($sql);

        $sql = "DELETE FROM jbsfina.vendor 
                 WHERE vendorid = '$vendorId'";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k8fyb")]);
    }
    finally
    {
        $db->Close();
    }


}

function HapusUserVendor()
{
    $db = new Db();
    try
    {
        $db->Open();

        $vendorId = $_REQUEST["vendorid"];
        $userId = $_REQUEST["userid"];

        $sql = "DELETE FROM jbsfina.vendoruser 
                 WHERE vendorid = '$vendorId' 
                   AND userid = '$userId'";
        $db->QueryDb($sql);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "k8fyb")]);
    }
    finally
    {
        $db->Close();
    }
}

function ShowDaftarPetugas($db, $vendorId)
{
    $sql = "SELECT vu.userid, u.nama, vu.tingkat, vu.replid
              FROM jbsfina.vendoruser vu, jbsfina.userpos u 
             WhERE vu.userid = u.userid
               AND vu.vendorid = '$vendorId'
             ORDER BY vu.tingkat, u.nama";
    $res = $db->QueryDb($sql);
    $num = mysqli_num_rows($res);
    if ($num == 0)
    {
        echo "(belum ada data petugas)<br><br>";
        return;
    }

    $sb = new StringBuilder();
    $sb->AppendLine("<table border='0' cellspacing='0' cellpadding='2'>");
    while($row = mysqli_fetch_row($res))
    {
        $userId = $row[0];
        $rowId = $row[3];

        $sb->AppendLine("<tr id='rowVendorUser$rowId'>");
        $sb->AppendLine("<td width='180' align='left' valign='top'>");
        $sb->AppendLine($row[1]);
        $sb->AppendLine("</td>");
        $sb->AppendLine("<td width='100' align='left' valign='top'>");
        if ($row[2] == 1)
            $sb->AppendLine(" Manager");
        else
            $sb->AppendLine(" Operator");
        $sb->AppendLine("</td>");
        $sb->AppendLine("<td width='50' align='left' valign='top'>");
        if (getLevel() != 2) {
            $sb->AppendLine("<a href=\"#\" onclick=\"hapusVendorUser($rowId, '$vendorId','$userId')\"><img src='../images/ico/hapus.png' border='0'></a>");
        }
        $sb->AppendLine("</td>");
        $sb->AppendLine("</tr>");
    }
    $sb->AppendLine("</table>");

    echo $sb->ToString();
}
?>