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
if (!IsTableExist($db, "jbsfina", "lokasidanamutasi"))
{
    $sql = "CREATE TABLE  `jbsfina`.`lokasidanamutasi` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `tanggal` datetime NOT NULL,
                `petugas` varchar(100) NOT NULL,
                `saldo` decimal(15,0) NOT NULL,
                `idtabungan` int(11) NOT NULL,
                `kelompok` varchar(10) NOT NULL,
                `lokasiasal` varchar(20) DEFAULT NULL,
                `lokasitujuan` varchar(20) NOT NULL,
                `stidlist` text NOT NULL,
                `keterangan` varchar(255) DEFAULT NULL,
                `alasan` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `FK_lokasidanamutasi_lokasi_asal_idx` (`lokasiasal`),
                KEY `FK_lokasidanamutasi_lokasi_tujuan_idx` (`lokasitujuan`),
                CONSTRAINT `FK_lokasidanamutasi_lokasi_asal` FOREIGN KEY (`lokasiasal`) REFERENCES `lokasidana` (`kode`) ON UPDATE CASCADE,
                CONSTRAINT `FK_lokasidanamutasi_lokasi_tujuan` FOREIGN KEY (`lokasitujuan`) REFERENCES `lokasidana` (`kode`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
    ExecIgnore($db, $sql);
}
?>