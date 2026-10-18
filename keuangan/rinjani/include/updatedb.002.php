<?php
if (!IsTableExist($db, "jbsfina", "lokasidana"))
{
    $sql = "CREATE TABLE `jbsfina`.`lokasidana` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `kode` varchar(20) NOT NULL,
              `nama` varchar(100) NOT NULL,
              `kelompok` varchar(50) NOT NULL,
              `keterangan` varchar(255) NOT NULL,
              `urutan` int(11) NOT NULL,
              `aktif` tinyint(4) DEFAULT 1,
              PRIMARY KEY (`id`),
              UNIQUE KEY `UX_lokasidana` (`kode`),
              KEY `IX_lokasidana` (`kelompok`,`aktif`,`urutan`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";
    ExecIgnore($db, $sql);

    $sql = "INSERT INTO `jbsfina`.`lokasidana` (`kode`, `nama`, `kelompok`, `keterangan`, `urutan`) 
            VALUES ('KAS', 'Kas Sekolah', 'TUNAI', ' ', '1')";
    ExecIgnore($db, $sql);
}

/*
 $sql = "";
 ExecIgnore($db, $sql);
 */
if (!IsColumnExist($db, "jbsfina", "tabungan", "lokasidana"))
{
    $sql = "ALTER TABLE `jbsfina`.`tabungan` 
              ADD COLUMN `lokasidana` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL AFTER `idjurnal`";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`tabungan` 
              ADD INDEX `IX_tabungan_lokasidana` (`lokasidana` ASC)";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`tabungan` 
              ADD CONSTRAINT `FK_tabungan_lokasidana`
                  FOREIGN KEY (`lokasidana`)
                  REFERENCES `jbsfina`.`lokasidana` (`kode`)
                  ON DELETE RESTRICT
                  ON UPDATE CASCADE";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "tabungan", "sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`tabungan` 
              ADD COLUMN `sumberdana` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL AFTER `idjurnal`";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`tabungan` 
              ADD INDEX `IX_tabungan_sumberdana` (`sumberdana` ASC)";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`tabungan` 
              ADD CONSTRAINT `FK_tabungan_sumberdana`
                  FOREIGN KEY (`sumberdana`)
                  REFERENCES `jbsfina`.`sumberdana` (`kode`)
                  ON DELETE RESTRICT
                  ON UPDATE CASCADE";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "tabunganp", "lokasidana"))
{
    $sql = "ALTER TABLE `jbsfina`.`tabunganp` 
              ADD COLUMN `lokasidana` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL AFTER `idjurnal`";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`tabunganp` 
              ADD INDEX `IX_tabunganp_lokasidana` (`lokasidana` ASC)";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`tabunganp` 
              ADD CONSTRAINT `FK_tabunganp_lokasidana`
                  FOREIGN KEY (`lokasidana`)
                  REFERENCES `jbsfina`.`lokasidana` (`kode`)
                  ON DELETE RESTRICT
                  ON UPDATE CASCADE";
    ExecIgnore($db, $sql);
}

if (!IsColumnExist($db, "jbsfina", "tabunganp", "sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`tabunganp` 
              ADD COLUMN `sumberdana` VARCHAR(20) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL AFTER `idjurnal`";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`tabunganp` 
              ADD INDEX `IX_tabunganp_sumberdana` (`sumberdana` ASC)";
    ExecIgnore($db, $sql);

    $sql = "ALTER TABLE `jbsfina`.`tabunganp` 
              ADD CONSTRAINT `FK_tabunganp_sumberdana`
                  FOREIGN KEY (`sumberdana`)
                  REFERENCES `jbsfina`.`sumberdana` (`kode`)
                  ON DELETE RESTRICT
                  ON UPDATE CASCADE";
    ExecIgnore($db, $sql);
}

if (!IsIndexExist($db, "jbsfina", "penerimaanjtt", "IX_pembayaranjtt_sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaanjtt` 
              ADD INDEX `IX_pembayaranjtt_sumberdana` (`sumberdana` ASC)";
    ExecIgnore($db, $sql);
}

if (!IsIndexExist($db, "jbsfina", "penerimaanjttcalon", "IX_penerimaanjttcalon_sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaanjttcalon` 
              ADD INDEX `IX_penerimaanjttcalon_sumberdana` (`sumberdana` ASC)";
    ExecIgnore($db, $sql);
}

if (!IsIndexExist($db, "jbsfina", "penerimaaniuran", "IX_penerimaaniuran_sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaaniuran` 
              ADD INDEX `IX_penerimaaniuran_sumberdana` (`sumberdana` ASC)";
    ExecIgnore($db, $sql);
}

if (!IsIndexExist($db, "jbsfina", "penerimaaniurancalon", "IX_penerimaaniurancalon_sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaaniurancalon` 
              ADD INDEX `IX_penerimaaniurancalon_sumberdana` (`sumberdana` ASC)";
    ExecIgnore($db, $sql);
}

if (!IsIndexExist($db, "jbsfina", "penerimaanlain", "IX_penerimaanlain_sumberdana"))
{
    $sql = "ALTER TABLE `jbsfina`.`penerimaanlain` 
              ADD INDEX `IX_penerimaanlain_sumberdana` (`sumberdana` ASC)";
    ExecIgnore($db, $sql);
}

?>