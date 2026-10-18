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
$nRowPerPage = 10;

function DefaultFormatNotif($db)
{
    global $departemen;

    $defFormat = "Kami informasikan {NAMA} masih memiliki tunggakan sebesar {TUNGGAKAN} untuk {PEMBAYARAN} - Bag. Keuangan";

    $sql = "SELECT COUNT(replid)
              FROM jbsfina.formatsms
             WHERE departemen = '$departemen'
               AND jenis = 'SISTUNG'";
    $ndata = $db->FetchSingle($sql, 0);
    if ($ndata == 0)
    {
        $sql = "INSERT INTO jbsfina.formatsms
                   SET jenis = 'SISTUNG', departemen = '$departemen', format = '$defFormat'";
        $db->QueryDb($sql);
    }

    $sql = "SELECT format
              FROM jbsfina.formatsms
             WHERE departemen = '$departemen'
               AND jenis = 'SISTUNG'";
    $format = $db->FetchSingle($sql, $defFormat);

    return $format;
}