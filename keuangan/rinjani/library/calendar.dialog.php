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
require_once('../include/sessioninfo.php');
require_once('../include/sessionchecker.php');
require_once('../include/rupiah.php');
require_once('../include/config.php');
require_once('../library/msg.php');
require_once('../library/common.func.php');
require_once('../util/peek.php');
require_once('../include/db.onpage.php');

$tahun = isset($_REQUEST["tahun"]) ? $_REQUEST["tahun"] : date("Y");
$bulan = isset($_REQUEST["bulan"]) ? $_REQUEST["bulan"] : date("m");
$tanggal = isset($_REQUEST["tanggal"]) ? $_REQUEST["tanggal"] : date("d");
$pilih = isset($_REQUEST["pilih"]) ? $_REQUEST["pilih"] : date("Y-m-d");

$ls = explode("-", $pilih);
$tglpilih = $ls[2];
$blnpilih = $ls[1];
$thnpilih = $ls[0];

OpenDb();

$tmp = "$tahun-$bulan-1";
$sql = "SELECT DAYOFWEEK('$tmp')";
$result = QueryDb($sql);
$row = mysqli_fetch_row($result);
$first_weekday_this_month = $row[0];

if ($bulan == 12)
{
    $next_month = 1;
    $next_year = $tahun + 1;
}
else
{
    $next_month = $bulan + 1;
    $next_year = $tahun;
}

if ($bulan == 1)
{
    $last_month = 12;
    $last_year = $tahun - 1;

    $tmp = ($tahun - 1) . "-12-1";
}
else
{
    $last_month = $bulan - 1;
    $last_year = $tahun;

    $tmp = $tahun . "-" . ($bulan - 1) . "-1";
}

$sql = "SELECT DAY(LAST_DAY('$tmp'))";
$result = QueryDb($sql);
$row = mysqli_fetch_row($result);
$last_day_last_month = $row[0];

$now = "$tahun-$bulan-1";
$sql = "SELECT DAY(LAST_DAY('$now'))";
$result = QueryDb($sql);
$row = mysqli_fetch_row($result);
$last_day_this_month = $row[0];

CloseDb();

$nweek = 0;
$nday = 0;
for ($i = 0; $i < ($first_weekday_this_month - 1); $i++)
{
    $cal[$nweek][$nday][0] = $last_day_last_month - ($first_weekday_this_month - 1) + ($i + 1);
    $cal[$nweek][$nday][1] = $last_month;
    $cal[$nweek][$nday][2] = $last_year;

    $nday++;
}

for ($i = 1; $i <= $last_day_this_month; $i++)
{
    $cal[$nweek][$nday][0] = $i;
    $cal[$nweek][$nday][1] = $bulan;
    $cal[$nweek][$nday][2] = $tahun;

    if ($nday == 6)
    {
        $nday = 0;
        $nweek++;
    }
    else
    {
        $nday++;
    }
}

if (($nday > 0) && ($nday < 7))
{
    $start = 1;
    for ($i = $nday; $i < 7; $i++)
    {
        $cal[$nweek][$i][0] = $start++;
        $cal[$nweek][$i][1] = $next_month;
        $cal[$nweek][$i][2] = $next_year;
    }
}

//Peek::PrintR($cal);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Kalender</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../style/style.css?<?= filemtime('../style/style.css')?>">
    <link rel="stylesheet" type="text/css" href="../style/toast.css">
    <link rel="stylesheet" type="text/css" href="../script/jquery-ui-1.14.1/jquery-ui.min.css">
    <style>
        .thismonth {
            font-family: Georgia, "Times New Roman", Times, serif;
            font-size: 20px;
            font-weight: bold;
        }

        .othermonth {
            font-family: Georgia, "Times New Roman", Times, serif;
            font-size: 14px;
            font-weight: bold;
            color:#999999;
        }
        .style1 {
            color: #006633;
            font-weight: bold;
        }
    </style>
    <script language="javascript" src="../script/jquery-3.7.1.min.js"></script>
    <script language="javascript" src="../script/jquery-ui-1.14.1/jquery-ui.min.js"></script>
    <script language="javascript" src="../script/qsbuilder.js?<?=filemtime('../script/qsbuilder.js')?>" ></script>
    <script language="JavaScript">
        function GoToLastMonth()
        {
            var qsb = new QsBuilder();
            qsb.add("bulan", $("#last_month").val());
            qsb.add("tahun", $("#last_year").val());
            qsb.add("pilih", $("#pilih").val());

            document.location.href = "calendar.dialog.php?" + qsb.createQs();
        }

        function GoToNextMonth()
        {
            var qsb = new QsBuilder();
            qsb.add("bulan", $("#next_month").val());
            qsb.add("tahun", $("#next_year").val());
            qsb.add("pilih", $("#pilih").val());

            document.location.href = "calendar.dialog.php?" + qsb.createQs();
        }

        function ChangeCal()
        {
            var qsb = new QsBuilder();
            qsb.add("bulan", $("#bulan").val());
            qsb.add("tahun", $("#tahun").val());
            qsb.add("pilih", $("#pilih").val());

            document.location.href = "calendar.dialog.php?" + qsb.createQs();
        }

        function PilihTanggal(i, j)
        {
            var pos = "#data-" + i + "-" + j;
            var data = $(pos).val();

            opener.acceptCalendar(data);
            window.close();
        }
    </script>
</head>
<body style="margin: 0">
<input type="hidden" id="last_month" value="<?=$last_month?>">
<input type="hidden" id="last_year" value="<?=$last_year?>">
<input type="hidden" id="next_month" value="<?=$next_month?>">
<input type="hidden" id="next_year" value="<?=$next_year?>">
<input type="hidden" id="pilih" value="<?=$pilih?>">
<table border="0" cellpadding="2" cellspacing="0" width="490" align="center">
<tr>
    <td width="100%" align="left">

    <span class="dialogTitle">Kalender</span><br><br>

    <strong>Bulan :</strong>
    <input type="button" class="dialogButtonPositive" onclick="GoToLastMonth()" value="  <  ">
<?php
    echo "<select id='bulan' name='bulan' class='inputbox' onchange='ChangeCal()'>";
    for ($i = 1; $i <= 12; $i++)
    {
        $sel = $i == $bulan ? "selected" : "";
        echo "<option value='$i' $sel>" . NamaBulan($i) . "</option>";
    }
    echo "</select>";

    echo "<select id='tahun' name='tahun' class='inputbox' onchange='ChangeCal()'>";
    $YNOW = date('Y');
    for ($i = 2007; $i <= $YNOW + 10; $i++)
    {
        $sel = $i == $tahun ? "selected" : "";
        echo "<option value='$i' $sel>$i</option>";
    }
    echo "</select>";
?>
    <input type="button" class="dialogButtonPositive" onclick="GoToNextMonth()" value="  >  ">
    </td>
</tr>
</table>

<table border="1" class="tab" cellpadding="5" cellspacing="0" width="490" style="border-color:#999999" align="center">
<tr height="30" bgcolor="#DFFFDF">
    <td width="70" class="redheader" align="center" style="background-color:#990000; color:#FFFFFF"><b>Minggu</b></td>
    <td width="70" class="header" align="center" style="background-color:#3366CC; color:#FFFFFF"><b>Senin</b></td>
    <td width="70" class="header" align="center" style="background-color:#3366CC; color:#FFFFFF"><b>Selasa</b></td>
    <td width="70" class="header" align="center" style="background-color:#3366CC; color:#FFFFFF"><b>Rabu</b></td>
    <td width="70" class="header" align="center" style="background-color:#3366CC; color:#FFFFFF"><b>Kamis</b></td>
    <td width="70" class="header" align="center" style="background-color:#339900; color:#FFFFFF"><b>Jum'at</b></td>
    <td width="70" class="header" align="center" style="background-color:#3366CC; color:#FFFFFF"><b>Sabtu</b></td>
</tr>
<?php
    for ($i = 0; $i < count($cal); $i++)
    {
        echo "<tr height='45'>";

        for ($j = 0; $j < count($cal[$i]); $j++)
        {
            $tgl = $cal[$i][$j][0];
            $bln = $cal[$i][$j][1];
            $thn = $cal[$i][$j][2];

            if ($j == 0)
                $color = "#FFCCCC";
            else
                $color = "#DFEFFF";

            if ($tgl == $tglpilih && $bln == $blnpilih && $thn == $thnpilih)
                $color = "#FFCC00";

            if (($bln == $bulan) && ($thn == $tahun))
                $style = "thismonth";
            else
                $style = "othermonth";

            $data = "$thn-$bln-$tgl";

            echo "<td align='center' valign='middle' style='background-color: $color'>";
            echo "<input type='hidden' id='data-$i-$j' value='$data'>";
            echo "<span class='$style' style='cursor: pointer' onclick='PilihTanggal($i, $j)'>$tgl</span><br>";
            echo "</td>";
        }
        echo "</tr>";
    }
    ?>
</table>

</body>
</html>
