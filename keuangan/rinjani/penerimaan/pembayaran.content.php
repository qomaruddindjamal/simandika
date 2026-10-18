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
$idtahunbuku = $_REQUEST['idtahunbuku'];
$idkategori = $_REQUEST['idkategori'];
$idpenerimaan = $_REQUEST['idpenerimaan'];
$departemen = $_REQUEST['departemen'];
?>
<frameset border="0" cols="25%,*" frameborder="1">
    <frame name="siswa" src="pembayaran.userlist.php?idtahunbuku=<?=$idtahunbuku?>&idkategori=<?=$idkategori?>&idpenerimaan=<?=$idpenerimaan?>&departemen=<?=$departemen?>" scrolling="auto" style="border-right: 1px solid #999;"/>
    <frame name="content" src="blank.php" scrolling="auto" />
</frameset><noframes></noframes>