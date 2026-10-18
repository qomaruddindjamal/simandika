<?php
/**[N]**
 * JIBAS Education Community
 * Jaringan Informasi Bersama Antar Sekolah
 *
 * @version: 33.0 (Jan 05, 2026)
 * @notes:
 *
 * Copyright (C) 2024 JIBAS (http://www.jibas.net)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 **[N]**/ ?>
<?php
function ProcessLogin()
{
    $db = new Db();
    try
    {
        $db->Open();

        $login = RequestData("login", "");
        $password = RequestData("password", "");

        if (strlen($login) == 0 || strlen($password) == 0)
            return json_encode([-1, "Invalid Login Data /ks08t"]);

        $user_exists = false;
        $login = strtolower($login);
        if ($login == "jibas")
        {
            $sql = "SELECT password 
                      FROM jbsuser.landlord";
            $res = $db->QueryDb($sql) ;
            if (mysqli_num_rows($res) == 0)
                return json_encode([-1, "User login data not found /kt5pf"]);

            $row = mysqli_fetch_array($res);
            if (md5($password) == $row['password'])
            {
                $_SESSION['login'] = "landlord";
                $_SESSION['namakeuangan'] = "landlord";
                $_SESSION['tingkatkeuangan'] = "0";
                $_SESSION['departemenkeuangan'] = "ALL";
                $_SESSION['temakeuangan'] = 1;

                $user_exists = true;
            }
        }
        else
        {
            $sql = "SELECT p.aktif 
                      FROM jbsuser.login l, jbssdm.pegawai p 
                     WHERE l.login = p.nip 
                       AND l.login = '$login'";
            $res = $db->QueryDb($sql);
            if (mysqli_num_rows($res) == 0)
                return json_encode([-1, "User login data not found /kyhyh"]);

            $row = mysqli_fetch_array($res);
            if ($row['aktif'] == 0)
            {
                return json_encode([-1, "Status pengguna tidak aktif /k5f3a"]);
            }
            else
            {
                $sql = "SELECT login, password 
                          FROM jbsuser.login 
                         WHERE login = '$login' 
                           AND password = md5('$password')";
                $res = $db->QueryDb($sql);
                if (mysqli_num_rows($res) > 0)
                {
                    $sql = "SELECT h.departemen as departemen, h.tingkat as tingkat, p.nama as nama, h.theme as tema 
                              FROM jbsuser.hakakses h, jbssdm.pegawai p 
                             WHERE h.login = '$login' 
                               AND p.nip = h.login 
                               AND h.modul = 'KEUANGAN' 
                               AND p.aktif = 1";
                    $res = $db->QueryDb($sql);
                    if ($row = mysqli_fetch_array($res))
                    {
                        $_SESSION['login'] = $login;
                        $_SESSION['namakeuangan'] = $row['nama'];
                        $_SESSION['tingkatkeuangan'] = $row['tingkat'];
                        $_SESSION['temakeuangan'] = $row['tema'];
                        if ($row['tingkat'] == 2)
                            $_SESSION['departemenkeuangan'] = $row['departemen'];
                        else
                            $_SESSION['departemenkeuangan'] = "ALL";

                        $user_exists = true;
                    }
                }
            }
        }

        if (!$user_exists)
            return json_encode([-1, "Login atau Password tidak sesuai"]);

        return json_encode([1, "OK"]);
    }
    catch (Exception $ex)
    {
        return json_encode([-99, Msg::InfoError($ex->getMessage(), "kehj8")]);
    }
    finally
    {
        $db->Close();
    }
}
?>