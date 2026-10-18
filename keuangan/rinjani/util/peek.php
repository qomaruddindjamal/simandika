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
require_once ("randomutil.php");

class Peek
{
    private static $LsColor = ["#368fba", "#9928a6", "#209ea0", "#b02323", "#000000"];
    private static $ColorIx = -1;

    public static function Show($data1, $data2 = "", $data3 = "", $data4 = "", $data5 = "")
    {
        $date = date('His');
        $date .= "." . RandomUtil::RandInt(3);

        $view = $data1;
        if ($data2 != "") $view .= " $data2";
        if ($data3 != "") $view .= " $data3";
        if ($data4 != "") $view .= " $data4";
        if ($data5 != "") $view .= " $data5";

        if (self::$ColorIx + 1 == count(self::$LsColor))
            self::$ColorIx = 0;
        else
            self::$ColorIx += 1;

        $color = self::$LsColor[self::$ColorIx];

        echo "<span style='color: $color'>$date</span> $view<br>";
    }

    public static function PrintR($array)
    {
        $date = date('His');
        $date .= "." . RandomUtil::RandInt(3);

        if (self::$ColorIx + 1 == count(self::$LsColor))
            self::$ColorIx = 0;
        else
            self::$ColorIx += 1;

        $color = self::$LsColor[self::$ColorIx];

        echo "<span style='color: $color'>$date</span><br>";
        echo "<pre>";
        print_r($array);
        echo "</pre><br>";
    }

    public static function PageRequest()
    {
        self::PrintR($_REQUEST);
    }
}
?>
