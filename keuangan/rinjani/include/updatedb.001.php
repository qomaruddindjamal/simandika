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
if (!IsTableExist($db, "jbsfina", "pgservicefee2"))
{
    $sql = "CREATE TABLE  `jbsfina`.`pgservicefee2` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `departemen` varchar(50) NOT NULL,
              `kode` varchar(10) NOT NULL,
              `nama` varchar(100) NOT NULL,
              `biaya` decimal(15,0) unsigned NOT NULL,
              `keterangan` varchar(255) NOT NULL,
              `aktif` tinyint(3) unsigned NOT NULL,
              `issync` tinyint(3) unsigned NOT NULL DEFAULT 0,
              `rekkas` varchar(15) NOT NULL,
              `rekpendapatan` varchar(15) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `FK_pgservicefee2_departemen` (`departemen`),
              KEY `FK_pgservicefee2_rekakun` (`rekkas`),
              KEY `FK_pgservicefee2_rekakun2` (`rekpendapatan`),
              CONSTRAINT `FK_pgservicefee2_departemen` FOREIGN KEY (`departemen`) REFERENCES `jbsakad`.`departemen` (`departemen`) ON UPDATE CASCADE,
              CONSTRAINT `FK_pgservicefee2_rekakun` FOREIGN KEY (`rekkas`) REFERENCES `rekakun` (`kode`) ON UPDATE CASCADE,
              CONSTRAINT `FK_pgservicefee2_rekakun2` FOREIGN KEY (`rekpendapatan`) REFERENCES `rekakun` (`kode`) ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";
    ExecIgnore($db, $sql);
}

if (!IsTableExist($db, "jbsfina", "sumberdana"))
{
    $sql = "CREATE TABLE `jbsfina`.`sumberdana` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `kode` varchar(20) NOT NULL,
              `nama` varchar(100) NOT NULL,
              `kelompok` varchar(50) NOT NULL,
              `keterangan` varchar(255) NOT NULL,
              `urutan` int(11) NOT NULL,
              `aktif` tinyint(4) DEFAULT 1,
              PRIMARY KEY (`id`),
              UNIQUE KEY `UX_sumberdana` (`kode`),
              KEY `IX_sumberdana` (`kelompok`,`aktif`,`urutan`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";
    ExecIgnore($db, $sql);

    $sql = "INSERT INTO `jbsfina`.`sumberdana` (`kode`, `nama`, `kelompok`, `keterangan`, `urutan`) 
            VALUES ('TUNAI', 'Pembayaran Tunai', 'TUNAI', ' ', '1')";
    ExecIgnore($db, $sql);
}

/*
 $sql = "";
 ExecIgnore($db, $sql);
 */
if (!IsColumnExist($db, "jbsfina", "penerimaanjtt", "sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaanjtt` 
              ADD COLUMN `sumberdana` VARCHAR(20) NULL AFTER `idjurnal`";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`penerimaanjtt` 
              ADD INDEX `IX_pembayaranjtt_sumberdana` (`sumberdana` ASC) VISIBLE";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`penerimaanjtt` 
              ADD CONSTRAINT `FK_pembayaranjtt_sumberdana`
                  FOREIGN KEY (`sumberdana`)
                  REFERENCES `jbsfina`.`sumberdana` (`kode`)
                  ON DELETE RESTRICT
                  ON UPDATE CASCADE";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "penerimaanjttcalon", "sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaanjttcalon`
              ADD COLUMN `sumberdana` VARCHAR(20) NULL AFTER `idjurnal`";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`penerimaanjttcalon`
              ADD INDEX `IX_pembayaranjttcalon_sumberdana` (`sumberdana` ASC) VISIBLE";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`penerimaanjttcalon`
              ADD CONSTRAINT `FK_pembayaranjttcalon_sumberdana`
                  FOREIGN KEY (`sumberdana`)
                  REFERENCES `jbsfina`.`sumberdana` (`kode`)
                  ON DELETE RESTRICT
                  ON UPDATE CASCADE";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "penerimaaniuran", "sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaaniuran` 
              ADD COLUMN `sumberdana` VARCHAR(20) NULL AFTER `idjurnal`";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`penerimaaniuran` 
              ADD INDEX `IX_pembayaraniuran_sumberdana` (`sumberdana` ASC) VISIBLE";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`penerimaaniuran` 
              ADD CONSTRAINT `FK_pembayaraniuran_sumberdana`
                  FOREIGN KEY (`sumberdana`)
                  REFERENCES `jbsfina`.`sumberdana` (`kode`)
                  ON DELETE RESTRICT
                  ON UPDATE CASCADE";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "penerimaaniurancalon", "sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaaniurancalon` 
              ADD COLUMN `sumberdana` VARCHAR(20) NULL AFTER `idjurnal`";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`penerimaaniurancalon` 
              ADD INDEX `IX_pembayaraniurancalon_sumberdana` (`sumberdana` ASC) VISIBLE";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`penerimaaniurancalon` 
              ADD CONSTRAINT `FK_pembayaraniurancalon_sumberdana`
                  FOREIGN KEY (`sumberdana`)
                  REFERENCES `jbsfina`.`sumberdana` (`kode`)
                  ON DELETE RESTRICT
                  ON UPDATE CASCADE";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "penerimaanlain", "sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaanlain` 
              ADD COLUMN `sumberdana` VARCHAR(20) NULL AFTER `idjurnal`";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`penerimaanlain` 
              ADD INDEX `IX_penerimaanlain_sumberdana` (`sumberdana` ASC) VISIBLE";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`penerimaanlain` 
              ADD CONSTRAINT `FK_penerimaanlain_sumberdana`
                  FOREIGN KEY (`sumberdana`)
                  REFERENCES `jbsfina`.`sumberdana` (`kode`)
                  ON DELETE RESTRICT
                  ON UPDATE CASCADE";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "jurnal", "jam"))
{
    $sql = "ALTER TABLE `jbsfina`.`jurnal` 
              ADD COLUMN `jam` VARCHAR(10) NOT NULL DEFAULT '00:00:00' AFTER `tanggal`";
    ExecIgnore($db, $sql);
}

if (!IsTriggerExist($db, "jbsfina", "jurnal", "updtime_jurnal_bins"))
{
    $sql = "CREATE TRIGGER jbsfina.updtime_jurnal_bins BEFORE INSERT ON jbsfina.jurnal
            FOR EACH ROW
            BEGIN
                SET NEW.jam = CURTIME();
            END";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "penerimaanjtt", "jam"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaanjtt` 
              ADD COLUMN `jam` VARCHAR(10) NOT NULL DEFAULT '00:00:00' AFTER `tanggal`";
    ExecIgnore($db, $sql);
}

if (!IsTriggerExist($db, "jbsfina", "penerimaanjtt", "updjam_penerimaanjtt_bins"))
{
    $sql = "CREATE TRIGGER jbsfina.updjam_penerimaanjtt_bins BEFORE INSERT ON jbsfina.penerimaanjtt
            FOR EACH ROW
            BEGIN
                SET NEW.jam = CURTIME();
            END";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "penerimaanjttcalon", "jam"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaanjttcalon` 
              ADD COLUMN `jam` VARCHAR(10) NOT NULL DEFAULT '00:00:00' AFTER `tanggal`";
    ExecIgnore($db, $sql);
}

if (!IsTriggerExist($db, "jbsfina", "penerimaanjttcalon", "updjam_penerimaanjttcalon_bins"))
{
    $sql = "CREATE TRIGGER jbsfina.updjam_penerimaanjttcalon_bins BEFORE INSERT ON jbsfina.penerimaanjttcalon
            FOR EACH ROW
            BEGIN
                SET NEW.jam = CURTIME();
            END";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "penerimaaniuran", "jam"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaaniuran` 
              ADD COLUMN `jam` VARCHAR(10) NOT NULL DEFAULT '00:00:00' AFTER `tanggal`";
    ExecIgnore($db, $sql);
}

if (!IsTriggerExist($db, "jbsfina", "penerimaaniuran", "updjam_penerimaaniuran_bins"))
{
    $sql = "CREATE TRIGGER jbsfina.updjam_penerimaaniuran_bins BEFORE INSERT ON jbsfina.penerimaaniuran
            FOR EACH ROW
            BEGIN
                SET NEW.jam = CURTIME();
            END";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "penerimaaniurancalon", "jam"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaaniurancalon` 
              ADD COLUMN `jam` VARCHAR(10) NOT NULL DEFAULT '00:00:00' AFTER `tanggal`";
    ExecIgnore($db, $sql);
}

if (!IsTriggerExist($db, "jbsfina", "penerimaaniurancalon", "updjam_penerimaaniurancalon_bins"))
{
    $sql = "CREATE TRIGGER jbsfina.updjam_penerimaaniurancalon_bins BEFORE INSERT ON jbsfina.penerimaaniurancalon
            FOR EACH ROW
            BEGIN
                SET NEW.jam = CURTIME();
            END";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "penerimaanlain", "jam"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaanlain` 
              ADD COLUMN `jam` VARCHAR(10) NOT NULL DEFAULT '00:00:00' AFTER `tanggal`";
    ExecIgnore($db, $sql);
}

if (!IsTriggerExist($db, "jbsfina", "penerimaanlain", "updjam_penerimaanlain_bins"))
{
    $sql = "CREATE TRIGGER jbsfina.updjam_penerimaanlain_bins BEFORE INSERT ON jbsfina.penerimaanlain
            FOR EACH ROW
            BEGIN
                SET NEW.jam = CURTIME();
            END";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "transaksilog", "jam"))
{
    $sql = "ALTER TABLE `jbsfina`.`transaksilog`
              ADD COLUMN `jam` VARCHAR(10) NOT NULL DEFAULT '00:00:00' AFTER `tanggal`";
    ExecIgnore($db, $sql);
}

if (!IsTriggerExist($db, "jbsfina", "transaksilog", "updjam_transaksilog_bins"))
{
    $sql = "CREATE TRIGGER jbsfina.updjam_transaksilog_bins BEFORE INSERT ON jbsfina.transaksilog
            FOR EACH ROW
            BEGIN
                SET NEW.jam = CURTIME();
            END";
    ExecIgnore($db, $sql);
}

if (!IsIndexExist($db, "jbsfina", "auditinfo", "IX_auditinfo"))
{
    $sql = "ALTER TABLE `jbsfina`.`auditinfo` 
              ADD INDEX `IX_auditinfo` (`tanggal` ASC, `sumber` ASC, `info1` ASC)";
    ExecIgnore($db, $sql);
}

if (!IsIndexExist($db, "jbsfina", "transaksilog", "IX_transaksilog"))
{
    $sql = "ALTER TABLE `jbsfina`.`transaksilog` 
              ADD INDEX `IX_transaksilog` (`petugas` ASC, `idtahunbuku` ASC, `tanggal` ASC, `nokas` ASC, `debet` ASC, `kredit` ASC)";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "barang", "aktif"))
{
    $sql = "ALTER TABLE `jbsfina`.`barang` 
              ADD COLUMN `aktif` TINYINT NOT NULL DEFAULT 1 AFTER `satuan`";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`groupbarang` 
            CHANGE COLUMN `keterangan` `keterangan` VARCHAR(255) NULL DEFAULT NULL ,
            CHANGE COLUMN `namagroup` `namagroup` VARCHAR(100) NOT NULL";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`kelompokbarang` 
            CHANGE COLUMN `kelompok` `kelompok` VARCHAR(100) NOT NULL ,
            CHANGE COLUMN `keterangan` `keterangan` VARCHAR(255) NULL DEFAULT NULL";
    ExecIgnore($db, $sql);
}
?>