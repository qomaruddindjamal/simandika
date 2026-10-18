<?php
function SetUserAktif()
{
    $db = new Db();
    try
    {
        $db->Open();

        $userReplid = $_REQUEST["replid"];
        $newAktif = $_REQUEST["newaktif"];

        $sql = "UPDATE jbsfina.userpos 
                   SET aktif = $newAktif 
                 WHERE replid = $userReplid";
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

function HapusUser()
{
    $db = new Db();
    try
    {
        $db->Open();

        $userId = $_REQUEST["userid"];

        $sql = "SELECT COUNT(replid) FROM jbsfina.paymenttrans WHERE userid = '$userId'";
        $nData = $db->FetchSingle($sql, 0);
        if ($nData != 0)
            return json_encode([-1, "Tidak dapat menghapus petugas ini karena sudah digunakan dalam transaksi!"]);

        $sql = "DELETE FROM jbsfina.vendoruser WHERE userid = '$userId'";
        $db->QueryDb($sql);

        $sql = "DELETE FROM jbsfina.userpos WHERE userid = '$userId'";
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
?>