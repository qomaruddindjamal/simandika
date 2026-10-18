-- Schema  : jbsyayasan  (aplikasi baru, satu schema untuk satu yayasan)
-- Sumber  : struktur ditulis ulang sendiri; TIDAK ada referensi ke schema `yayasan`
--           (tidak ada FK lintas-schema, VIEW, RENAME, maupun CREATE TABLE ... LIKE).
-- Dipakai : mysql -u root -P 3434 -h 127.0.0.1 jbsyayasan < jbsyayasan.sql
-- Aturan  : InnoDB + utf8mb4_unicode_ci, uang = decimal, tanggal = date/datetime,
--           sandi = password_hash() (varchar 255), setiap tabel data punya id_lembaga.

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------------
-- yayasan : akar tenant seluruh sistem. Semua tabel lembaga menunjuk ke mari.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `yayasan` (
  `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `nama_yayasan`  VARCHAR(150)    NOT NULL,
  `npwp`          VARCHAR(20)     DEFAULT NULL COMMENT 'tanpa titik/hyphen, 15-16 digit',
  `alamat`        VARCHAR(200)    NOT NULL DEFAULT '',
  `kode_pos`      VARCHAR(10)     DEFAULT NULL,
  `telepon`       VARCHAR(30)     NOT NULL DEFAULT '',
  `email`         VARCHAR(120)    DEFAULT NULL,
  `website`       VARCHAR(120)    DEFAULT NULL,
  `logo`          VARCHAR(120)    NOT NULL DEFAULT '' COMMENT 'nama file di images/',
  `kop_surat`     TEXT            DEFAULT NULL,
  `status`        ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `dibuat_pada`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `diubah_pada`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Master yayasan - akar scope multi-tenant';

-- ---------------------------------------------------------------------------
-- departemen : unit akademik/organisasi di bawah sebuah yayasan.
--              Join wajib: d.id_yayasan = y.id (FK), jadi tidak ada departemen
--              tanpa yayasan induk. Struktur ditulis ulang sendiri: PK `id`,
--              nama tidak lagi menjadi primary key, tanpa kolom sinkronisasi
--              (token/issync/info1..3) seperti di jbsakad.departemen.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `departemen` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `id_yayasan`   INT UNSIGNED    NOT NULL COMMENT 'FK -> yayasan.id',
  `kode`         VARCHAR(20)     DEFAULT NULL,
  `nama`         VARCHAR(120)    NOT NULL,
  `id_kepala`    INT UNSIGNED    DEFAULT NULL COMMENT 'FK -> guru.id (tabel guru belum dibuat)',
  `urutan`       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `keterangan`   VARCHAR(255)    DEFAULT NULL,
  `status`       ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `dibuat_pada`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `diubah_pada`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_departemen_yayasan_nama` (`id_yayasan`, `nama`),
  KEY `ix_departemen_yayasan_status` (`id_yayasan`, `status`, `urutan`),
  CONSTRAINT `fk_departemen_yayasan` FOREIGN KEY (`id_yayasan`) REFERENCES `yayasan` (`id`)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Departemen milik yayasan (1 yayasan : N departemen)';

-- Contoh join yang dipakai aplikasi:
--   SELECT y.nama_yayasan, d.nama, d.urutan
--     FROM jbsyayasan.yayasan y
--     JOIN jbsyayasan.departemen d ON d.id_yayasan = y.id
--    WHERE d.status = 'aktif'
--    ORDER BY d.urutan;
