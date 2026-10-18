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
function getHeader2($db, $dep)
{
	$sql = "SELECT *, IF(foto IS NULL, 0, 1) AS fotoexist, 
                   IF(foto IS NULL, '', TO_BASE64(foto)) AS foto64 
              FROM jbsumum.identitas 
             WHERE departemen = '$dep'";
	$result = $db->QueryDb($sql);
	if (mysqli_num_rows($result) == 0)
	{
        echo "<div style='width: 80px; height: 80px; display: inline-block'>&nbsp;</div>";
        return;
    };

	$row = mysqli_fetch_array($result);
	$replid = $row['replid'];
	$nama = $row['nama'];
	$alamat1 = $row['alamat1'];
	$alamat2 = $row['alamat2'];
	$te1p1 = $row['telp1'];
	$telp2 = $row['telp2'];
	$te1p3 = $row['telp3'];
	$telp4 = $row['telp4'];
	$fax1 = $row['fax1'];
	$fax2 = $row['fax2'];
	$situs = $row['situs'];
	$email = $row['email'];
	$foto64 = $row['foto64'];
	$fotoexist = $row['fotoexist'];
	if ($fotoexist == 0)
        $foto64 = "/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAeAAD/4QMvaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjYtYzA2NyA3OS4xNTc3NDcsIDIwMTUvMDMvMzAtMjM6NDA6NDIgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2NDkwQjQ0N0E5NTAxMUYwOEUxQUJEQzU5NkY5MkMwOCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2NDkwQjQ0OEE5NTAxMUYwOEUxQUJEQzU5NkY5MkMwOCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjY0OTBCNDQ1QTk1MDExRjA4RTFBQkRDNTk2RjkyQzA4IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjY0OTBCNDQ2QTk1MDExRjA4RTFBQkRDNTk2RjkyQzA4Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQAEAsLCwwLEAwMEBcPDQ8XGxQQEBQbHxcXFxcXHx4XGhoaGhceHiMlJyUjHi8vMzMvL0BAQEBAQEBAQEBAQEBAQAERDw8RExEVEhIVFBEUERQaFBYWFBomGhocGhomMCMeHh4eIzArLicnJy4rNTUwMDU1QEA/QEBAQEBAQEBAQEBA/8AAEQgAQABAAwEiAAIRAQMRAf/EAEsAAQEAAAAAAAAAAAAAAAAAAAAHAQEAAAAAAAAAAAAAAAAAAAAAEAEAAAAAAAAAAAAAAAAAAAAAEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/9k=";

	echo "<table border='0' cellpadding='0' cellspacing='0' width='100%'>";
	echo "<tr>";
	echo "<td width='20%' align='center'>";
    echo "<img style='height: 70px;' src='data:image/jpg;base64,$foto64'>";
	echo "</td>";
	echo "<td valign='top' style='font-size: 12px;'>";
    echo "<span style='font-size: 18px;'><b>$nama</b></span>";
    if ($alamat1 != "")
    {
        echo "<br>Lokasi 1: $alamat1";
        if ($te1p1 != "")
            echo "&nbsp;&nbsp;Telp. $te1p1";
        if ($telp2 != "")
            echo ", $telp2";
        if ($fax1 != "" )
            echo "&nbsp;&nbsp;Fax. $fax1&nbsp;&nbsp;";
    }

    if ($alamat2 != "")
    {
        echo "<br>Lokasi 2: $alamat2";
        if ($te1p3 != "")
            echo "&nbsp;&nbsp;Telp. $te1p3";
        if ($telp4 != "")
            echo ", $telp4";
        if ($fax2 != "" )
            echo "&nbsp;&nbsp;Fax. $fax2&nbsp;&nbsp;";
    }

    if ($situs != "" )
	    echo "<br>Website: $situs&nbsp;&nbsp;";

    if ($email != "" )
	    echo "Email: $email";

	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='2'><hr width='100%' style='border: solid 1px #ccc;'></td>";
	echo "</tr>";
	echo "</table>";
	echo "<br>";
}

function getSmallHeader2($db, $dep)
{
	$sql = "SELECT replid, nama, 
                   IF(foto IS NULL, 0, 1) AS fotoexist, 
                   IF(foto IS NULL, '', TO_BASE64(foto)) AS foto64  
              FROM jbsumum.identitas 
             WHERE departemen='$dep'";
	$result = $db->QueryDb($sql);
	$row = @mysqli_fetch_array($result);
	$replid = $row['replid'];
	$nama = $row['nama'];
    $foto64 = $row['foto64'];
    $fotoexist = $row['fotoexist'];
    if ($fotoexist == 0)
        $foto64 = "/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAeAAD/4QMvaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjYtYzA2NyA3OS4xNTc3NDcsIDIwMTUvMDMvMzAtMjM6NDA6NDIgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2NDkwQjQ0N0E5NTAxMUYwOEUxQUJEQzU5NkY5MkMwOCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2NDkwQjQ0OEE5NTAxMUYwOEUxQUJEQzU5NkY5MkMwOCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjY0OTBCNDQ1QTk1MDExRjA4RTFBQkRDNTk2RjkyQzA4IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjY0OTBCNDQ2QTk1MDExRjA4RTFBQkRDNTk2RjkyQzA4Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQAEAsLCwwLEAwMEBcPDQ8XGxQQEBQbHxcXFxcXHx4XGhoaGhceHiMlJyUjHi8vMzMvL0BAQEBAQEBAQEBAQEBAQAERDw8RExEVEhIVFBEUERQaFBYWFBomGhocGhomMCMeHh4eIzArLicnJy4rNTUwMDU1QEA/QEBAQEBAQEBAQEBA/8AAEQgAQABAAwEiAAIRAQMRAf/EAEsAAQEAAAAAAAAAAAAAAAAAAAAHAQEAAAAAAAAAAAAAAAAAAAAAEAEAAAAAAAAAAAAAAAAAAAAAEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/9k=";
		
	echo "<table border='0' cellpadding='0' cellspacing='0' width='100%'>";
	echo "<tr>";
	echo "<td width='20%' align='center'>";
    echo "<img style='height: 30px;' src='data:image/jpg;base64,$foto64'>";
	echo "</td>";
	echo "<td valign='middle' align='left'>";
    echo "<span style='font-size: 15px;'><b>$nama</b></span>";
	echo "</td>";
	echo "</tr>";
    echo "<tr>";
    echo "<td colspan='2'><hr width='100%' style='border: solid 1px #ccc;'></td>";
    echo "</tr>";
    echo "</table>";
}

function getSmallHeaderLogo2($db, $dep)
{
    $sql = "SELECT IF(foto IS NULL, 0, 1) AS fotoexist, 
                   IF(foto IS NULL, '', TO_BASE64(foto)) AS foto64  
              FROM jbsumum.identitas 
             WHERE departemen='$dep'";
    $result = $db->QueryDb($sql);
    $row = @mysqli_fetch_array($result);

    $foto64 = $row['foto64'];
    $fotoexist = $row['fotoexist'];
    if ($fotoexist == 0)
        $foto64 = "/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAeAAD/4QMvaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjYtYzA2NyA3OS4xNTc3NDcsIDIwMTUvMDMvMzAtMjM6NDA6NDIgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2NDkwQjQ0N0E5NTAxMUYwOEUxQUJEQzU5NkY5MkMwOCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2NDkwQjQ0OEE5NTAxMUYwOEUxQUJEQzU5NkY5MkMwOCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjY0OTBCNDQ1QTk1MDExRjA4RTFBQkRDNTk2RjkyQzA4IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjY0OTBCNDQ2QTk1MDExRjA4RTFBQkRDNTk2RjkyQzA4Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+/+4ADkFkb2JlAGTAAAAAAf/bAIQAEAsLCwwLEAwMEBcPDQ8XGxQQEBQbHxcXFxcXHx4XGhoaGhceHiMlJyUjHi8vMzMvL0BAQEBAQEBAQEBAQEBAQAERDw8RExEVEhIVFBEUERQaFBYWFBomGhocGhomMCMeHh4eIzArLicnJy4rNTUwMDU1QEA/QEBAQEBAQEBAQEBA/8AAEQgAQABAAwEiAAIRAQMRAf/EAEsAAQEAAAAAAAAAAAAAAAAAAAAHAQEAAAAAAAAAAAAAAAAAAAAAEAEAAAAAAAAAAAAAAAAAAAAAEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/9k=";

    echo "<img style='height: 30px;' src='data:image/jpg;base64,$foto64'>";
}
?>