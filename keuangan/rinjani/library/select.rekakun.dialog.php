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
$kategori = isset($_REQUEST["kategori"]) ? $_REQUEST["kategori"] : "ALL";
$subKategori = isset($_REQUEST["subkategori"]) ? $_REQUEST["subkategori"] : "ALL";
?>
<script language="JavaScript">
    function acceptRekAkunDialog(kategori, subKategori, kodeRek, namaRek)
    {
        opener.acceptRekAkunDialog(kategori, subKategori, kodeRek, namaRek);
        window.close();
    }
</script>
<frameset rows="50,*" border="0">
    <frame name="header" src="select.rekakun.dialog.header.php?kategori=<?=$kategori?>&subkategori=<?=$subKategori?>" scrolling="no" noresize="noresize"  style="border:1px; border-bottom-color:#000000; border-bottom-style:solid"/>
    <frame name="content" src="blank.php" scrolling="yes" />
</frameset><noframes></noframes>
