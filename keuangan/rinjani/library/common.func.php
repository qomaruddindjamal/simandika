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
$tag_mandatory = "&nbsp;<span style='color: red; font-weight: bold'>*</span>";
$bullet_grey = "<span style='color: #999;'>&bull;</span>&nbsp;";
$bullet_red = "<span style='color: #8d0000;'>&bull;</span>&nbsp;";
$bullet_blue = "<span style='color: #1a73e8;'>&bull;</span>&nbsp;";

function StringIsSelected($value, $comparer)
{
	if ($value == $comparer) 
		return "selected";
	else
		return "";
}

function IntIsSelected($value, $comparer)
{
	$a = (int)$value;
	$b = (int)$comparer;
	
	if ($a == $b) 
		return "selected";
	else
		return "";
}

function StringIsChecked($value, $comparer)
{
	if ($value == $comparer) 
		return "checked";
	else
		return "";
}

function IntIsChecked($value, $comparer)
{
	if ($value == $comparer) 
		return "checked";
	else
		return "";
}

function RandStr($length)
{
	$charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$s = "";
	while(strlen($s) < $length) 
		$s .= substr($charset, rand(0, 61), 1);
	return $s;		
}

function NamaBulan($bln)
{
	if ($bln == 1)
		return "Januari";
	elseif ($bln == 2)
		return "Februari";		
	elseif ($bln == 3)
		return "Maret";		
	elseif ($bln == 4)
		return "April";		
	elseif ($bln == 5)
		return "Mei";
	elseif ($bln == 6)
		return "Juni";		
	elseif ($bln == 7)
		return "Juli";
	elseif ($bln == 8)
		return "Agustus";		
	elseif ($bln == 9)
		return "September";
	elseif ($bln == 10)
		return "Oktober";		
	elseif ($bln == 11)
		return "November";
	elseif ($bln == 12)
		return "Desember";		
}

function rpad($string, $padchar, $length)
{
	$result = trim($string);
	if (strlen($result) < $length) {
		$nzero = $length - strlen($result);
		$zero = "";
		for($i = 0; $i < $nzero; $i++)
			$zero .= "0";
		$result = $zero . $result;
	}
	return $result;
}

function MySqlDateFormat($date)
{
    $ls = explode("-", $date);
    $d = $ls[0];
    $m = $ls[1];
    $y = $ls[2];
    return "$y-$m-$d";
}

function RegularDateFormat($mysqldate)
{
    $ls = explode("-", $mysqldate);
    $d = $ls[2];
    $m = $ls[1];
    $y = $ls[0];
    return "$d-$m-$y";
}

function LongDateFormat($mysqldate)
{
	//list($y, $m, $d) = split('[/.-]', $mysqldate);
	//return "$d ". NamaBulan($m) ." $y";
    $ls = explode("-", $mysqldate);
    $d = $ls[2];
    $m = $ls[1];
    $y = $ls[0];
    return "$d ". NamaBulan($m) ." $y";
}

function JmlHari($bln,$th)
{
	if ($bln == 4 || $bln == 6|| $bln == 9 || $bln == 11) 
		$n = 30;
	else if ($bln == 2 && $th % 4 <> 0)
		$n = 28;
	else if ($bln == 2 && $th % 4 == 0)
		$n = 29;
	else 
		$n = 31;
	return $n;
}

function SafeInput($text)
{
    $text = trim($text);
    $text = str_replace("'", "`", $text);
    $text = str_replace("<", "&lt;", $text);
    return str_replace(">", "&gt;", $text);
}

function RequestData($name, $defaultValue)
{
    return isset($_REQUEST[$name]) ? SafeInput($_REQUEST[$name]) : $defaultValue;
}
?>