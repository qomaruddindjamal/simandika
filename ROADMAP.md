# ROADMAP — JIBAS Tampilan Modern, KPI, Skala Yayasan

Dokumen pendamping: `RND-TAMPILAN-MODERN-YAYASAN.md` (temuan & keputusan).
Aturan main seluruh fase:

- Semua pekerjaan **di dalam `htdocs/jibas`**; aset frontend masuk folder existing `style/` dan `script/`
  (tidak membuat folder aplikasi baru, tidak menyentuh `htdocs/project` / `htdocs/nexgen` kecuali fase F-8).
- Kode baru: `<?php` (bukan `<?`), prepared statement, CSRF token, tanpa jQuery.
- Setiap fase punya *rollback*: file lama tidak dihapus, hanya diganti rujukan; backup `.legacy-<fase>` disimpan
  di `log/` sebelum perubahan in-place.
- Definisi Selesai (DoD) umum: halaman tampil di 4 fase tema, lolos `EXPLAIN` (pakai indeks), Lighthouse ≥ 90,
  modul lama masih berfungsi, dan ada bukti screenshot/`curl` yang dilaporkan ke user.

Status saat ini: **F-0 s/d F-8 belum dimulai** (dokumen ini = deliverable riset).

---

## Ringkasan Fase

| Fase | Nama | Hasil utama | Estimasi | Bergantung pada |
|---|---|---|---|---|
| F-0 | Baseline & aset | Bootstrap 5.3.8 lokal, ikon lokal, alat ukur | 1 hari | — |
| F-1 | Fondasi data yayasan | tabel sendiri di `jbsyayasan` (dimulai dari `yayasan`), indeks menyatu CREATE | 2–3 hari | F-0 |
| F-2 | Engine tema 4 fase | `style/jibas-theme.css`, `script/theme.js|php`, Terang/Senja/Gelap/Fajar/Auto | 2 hari | F-0 |
| F-3 | Shell layout | navbar header (13 menu lama) + sidebar + tab konten + shim frameset | 3–4 hari | F-2 |
| F-4 | Login & keamanan | sesi yayasan, `password_hash`, CSRF, scope lembaga | 2 hari | F-1, F-3 |
| F-5 | Dashboard KPI | 15 KPI + Chart.js lokal + `api/kpi.php` + cache | 3–4 hari | F-1, F-3 |
| F-6 | Sidebar Referensi [readonly] | 33 entri baca-saja + cetak | 4–5 hari | F-3, F-4, F-5 |
| F-7 | CRUD bertahap | gelombang A/B/C (lihat Matriks CRUD) | 8–12 hari | F-6 |
| F-8 | RoundCube 1.7.4 + SSO | webmail engine + `sso_clients` | 3–5 hari | F-3 (prasyarat user: imap, mail server) |
| F-9 | Pemangkasan bobot | hapus aset slice/jQuery per modul bermigrasi, sunset frameset | 3 hari | F-7 |

Total realistis: ±27–37 hari kerja (bukan kalender), bisa diiris per fase dan dihentikan di batas fase mana pun.

---

## F-0 — Baseline & aset (1 hari)

**Tujuan**: punya kerangka Bootstrap 5.3.8 lokal + angka pembanding sebelum perubahan.

Tugas:
1. Unduh Bootstrap **5.3.8** (`bootstrap.min.css`, `bootstrap.bundle.min.js`, `bootstrap-icons.css` + woff2) →
   `style/bootstrap/` dan `script/`. (Aksi unduh/instal dijalankan atas persetujuan user.)
2. Ukur baseline: `curl -o NUL -w %{size_download}` untuk `index.php`, `akademik/index.php`,
   `akademik/referensi.php`; simpan ke `log/baseline-f0.txt`.
3. Snapshot struktur: `mysqldump --no-data jbsyayasan` → `log/jbsyayasan-schema-f0.sql`; `SHOW INDEX` semua tabel →
   `log/jbsyayasan-index-f0.txt`.
4. Konfirmasi `short_open_tag=On` tetap dan tidak ada regresi PHP warning di log (`xampp/apache/logs/error.log`).

Acceptance: file aset ada & bisa diakses via `http://localhost/jibas/style/bootstrap/bootstrap.min.css` (HTTP 200),
baseline tercatat, schema snapshot tersimpan.
Rollback: hapus file aset (belum ada yang dirujuk).

---

## F-1 — Fondasi data yayasan: bangun tabel sendiri di `jbsyayasan` (2–3 hari)

**Tujuan**: `jbsyayasan` berdiri sendiri sebagai schema aplikasi baru. **Tidak ada** ketergantungan ke schema
`yayasan` lama (tanpa rename, tanpa `CREATE TABLE ... LIKE`, tanpa FK/VIEW lintas-schema, tanpa query baca ke sana).
UI hanya bicara ke `jbsyayasan`, sehingga KPI multi-lembaga langsung berjalan di atas tipe data yang benar.

Konvensi DDL (sudah berlaku, sumber: `jbsyayasan.sql` di root project):
1. `CREATE TABLE IF NOT EXISTS` → file bisa dijalankan berulang (idempotent).
2. InnoDB, `utf8mb4_unicode_ci`; PK `id INT UNSIGNED AUTO_INCREMENT`.
3. Setiap tabel data membawa `id_lembaga` (dan `id_tahun` bila berlaku) + indeks penyerta — indeks ditulis
   **bersamaan CREATE**, bukan sebagai migrasi susulan.
4. Uang `decimal(15,2)`, tanggal `date`/`datetime`, sandi `varchar(255)` hasil `password_hash()` (tidak ada plaintext).
5. `status enum('aktif','nonaktif')` + `dibuat_pada` / `diubah_pada` TIMESTAMP.

Urutan tabel yang dibuat (✅ = sudah ada):
1. ✅ `yayasan` — akar tenant (19 Okt 2026, terisi 1 baris "Yayasan Islam Hasan Kafrawi").
2. `lembaga` (pengganti `data_lembaga`; FK `id_yayasan` → `yayasan.id`).
2b. ✅ `departemen` — dibuat sendiri di `jbsyayasan`, FK `id_yayasan` → `yayasan.id`, unik `(id_yayasan, nama)` (19 Okt 2026).
3. `user`, `user_lembaga` (hak akses per lembaga), `lembaga_client_map` (`id_lembaga` ↔ skema `jbs*` lama).
4. Referensi akademik: `tahun_ajaran`, `semester`, `tingkat`, `jurusan`, `angkatan`, `kelas`, `rombel`, `alumni`.
5. PEGAWAI/SISWA: `guru`, `guru_lembaga`, `siswa`.
6. Keuangan: `kategori_keuangan`, `keuangan`, `transaksi`, `pembayaran`, `penerimaan`, `tahun_buku`.
7. Persuratan: `kategori_surat`, `jenis_surat`, `tujuan_surat`, `no_surat`, `surat_masuk`, `surat_keluar`.
8. Penunjang: `kpi_cache(kpi_code, scope, payload, generated_at)`, `audit_log(id, id_user, aksi, tabel, record_id, sebelum, sesudah, ip, waktu)`, `sso_clients`.

Acceptance: (a) `mysql < jbsyayasan.sql` dari keadaan kosong berhasil tanpa error; (b) tiap tabel data punya
`SHOW INDEX` ≥ 2 baris; (c) `grep -rn "yayasan\." script include` tidak menemukan satu pun query ke schema lama.
Rollback: `DROP TABLE` per tabel (data produksi belum masuk); file DDL disimpan di `jbsyayasan.sql` dengan penanda fase.

---

## F-2 — Engine tema 4 fase (2 hari)

**Tujuan**: satu layer token, 4 fase, mode Auto berbasis jam server, tanpa reload-flash.

Tugas:
1. `style/jibas-theme.css` — token per `html[data-jibas-phase=...]` + pemetaan variabel `--bs-*`
   (palet lengkap ada di §4 dok R&D).
2. `script/theme.php` — fungsi `jibasPhase(?int $hour=null): string` dengan aturan:
   06:00–16:59 `terang`, 17:00–17:59 `senja`, 18:00–03:59 `gelap`, 04:00–05:59 `fajar`
   (perhatikan potongan malam yang melewati tengah malam).
3. `script/theme.js` — re-evaluasi pada batas jam berikutnya, tombol pilihan Terang/Gelap/Auto,
   simpan ke `localStorage.jibasTheme`.
4. Integrasi: `<html lang="id" data-bs-theme="..." data-jibas-phase="...">` ditulis server-side sebelum output.
5. Halaman uji `style/theme-preview.html` menampilkan 4 fase berdampingan + komponen Bootstrap
   (table, modal, dropdown, navbar, form, card, badge) untuk verifikasi visual.

Acceptance: pada jam 04:30 simulasi (`jibasPhase(4)`) = fajar, 17:30 = senja, 18:00 = gelap, 03:59 = gelap;
teks kontras ≥ 4,5:1 di keempat fase; `@media print` = terang; **tidak** menyalin CSS `apollo` nexgen.
Rollback: hapus atribut + file, layout lama tidak terpengaruh.

---

## F-3 — Shell layout (3–4 hari)

**Tujuan**: tampilan baru lebih ringan dengan **menu header tetap ada** + sidebar, menggantikan frameset untuk jalur baru.

Tugas:
1. `style/jibas-shell.css` (≤ 20 KB) untuk navbar/sidebar/tab; `script/shell.js` untuk toggle, offcanvas, tab,
   pemilih yayasan→lembaga→tahun ajaran.
2. Navbar header: 13 entri dari `akademik/frametop.php:136-195` (REFERENSI, CALON SISWA, GURU & PELAJARAN, JADWAL,
   KESISWAAN, PRESENSI, PENILAIAN, EXIM, KELULUSAN, MUTASI, PELAPORAN, MANAJEMEN USER, LOGOUT) — versi Bootstrap,
   bukan gambar slice.
3. Sidebar grup: Dashboard/KPI · Referensi (readonly) · Transaksional · Surat · Laporan · Webmail · Pengaturan.
4. Area konten: tab-panel; modul lama dimuat dalam `<iframe>`; `script/legacy-shim.js` menyediakan
   `parent.framecenter/frameleft/...` agar JS lama (`akademik/frametop.php:56`, `akademik/referensi.php:38-46`)
   tetap jalan tanpa edit per-file.
5. Ganti portal `index.php` (halaman gambar tombol, `index.php:113-248`) menjadi shell dengan kartu modul;
   `include/school.config.php` (`$G_LOGO_*`, `$G_JUDUL_*`) dibaca dari `jbsyayasan.yayasan` + `jbsyayasan.lembaga` sebagai override.
6. Ukur payload & bandingkan baseline F-0 (target < 300 KB transfer).

Acceptance: satu DOM (tidak ada `<frameset>` di jalur baru), 13 menu header tampil & berfungsi, sidebar bisa
collapse, modul lama terbuka di tab (minimal `akademik/referensi.php`, `keuangan/rinjani/index.php`),
payload ≤ 300 KB, tema berubah saat tombol ditekan tanpa reload.
Rollback: `index.php` lama ada di `log/index.legacy-f3.php`.

---

## F-4 — Login & keamanan (2 hari)

**Tujuan**: autentikasi berbasis `yayasan.user` + scope lembaga, menutup temuan plaintext password.

Tugas:
1. `script/auth.php`: login (prepared statement), `password_verify()` dengan fallback migrasi
   (login pertama dengan password lama → langsung di-`password_hash()` & simpan), blokir bila `user.blokir='Y'`.
2. Gate per halaman: `require_lembaga($id)` cek `user_lembaga`; `level` → `administrator`/`kepala_sekolah`/`operator`/`siswa`.
3. CSRF token untuk semua form; cookie `HttpOnly`+`SameSite=Lax`; `session_regenerate_id()` saat login.
4. `audit_log` untuk login/logout dan (F-7) semua aksi CRUD.
5. Audit 12 file referensi lama (`akademik/referensi/pilihandata.*`, `tambahandata.*`) untuk query raw — daftar temuan
   dilaporkan, perbaikan masuk F-7.

Acceptance: user tanpa hak `id_lembaga` mendapat 403; password baru tersimpan hash (bukti: `SELECT LEFT(password,7)`);
CSRF menolak POST tanpa token.
Rollback: fitur login lama (`jbsuser.hakakses`) tetap aktif untuk jalur legacy.

---

## F-5 — Dashboard KPI (3–4 hari)

**Tujuan**: KPI yayasan (agregat) + KPI lembaga, ringan, cache.

Tugas:
1. `script/api/kpi.php` — 15 KPI §5 dok R&D, parameter `scope`, `id_lembaga`, `id_tahun`; respons JSON.
2. `script/kpi_queries.php` — satu fungsi per KPI, semua pakai indeks F-1; `EXPLAIN` dicatat ke `log/kpi-explain.txt`.
3. Cache 5 menit (`kpi_cache`) + endpoint batch 1 request untuk 15 kartu (hindari 15 XHR).
4. `script/charts/` Chart.js UMD lokal + adapter warna dari token tema (kartu angka, bar per lembaga, gauge realisasi,
   dual bar surat, funnel PSB, ring kepatuhan data, ranking lembaga).
5. Halaman `Beranda` shell: filter yayasan/lembaga/tahun + grid kartu responsif + "lihat laporan".

Acceptance: 15 KPI tampil dengan data `jbsyayasan` riil (1 yayasan, 11 lembaga), waktu render < 1 s pada data saat ini,
< 300 ms setelah cache; ganti lembaga → angka berubah; semua KPI nol-aman (tidak error saat data kosong).
Rollback: halaman lama `akademik/home.php` tidak dihapus.

---

## F-6 — Sidebar Referensi [readonly] (4–5 hari)

**Tujuan**: menu **Referensi** di sidebar berisi 33 entri master data, **hanya-baca** (sesuai permintaan),
menggantikan halaman 9-frame `akademik/referensi.php`.

Tugas:
1. Generator daftar tunggal `script/referensi/registry.php` — peta 33 entri (tabel, label, kolom tampil, kolom cari,
   relasi `id_lembaga`) sesuai §6.2 dok R&D. Satu konfigurasi → semua halaman (tidak menulis 26 file mirip).
2. `script/referensi/list.php` (server-side pagination, sort whitelist, cari) + `detail.php` + `cetak.php` (PDF/print).
3. Mode readonly: tombol Tambah/Edit/Hapus **tidak dirender**; `api/referensi.php` hanya menerima `GET`;
   `POST/PUT/DELETE` → 403 + `audit_log`.
4. Entri wilayah (`provinsi`,`kabupaten`,`kecamatan`,`kelurahan`,`kepulauan`,`suku`) = global, tanpa filter `id_lembaga`.
5. Entri ber-`id_lembaga` = otomatis ter-filter oleh scope F-4.
6. Uji data: RA/TK sampai SMA (11 lembaga) — tampilkan jumlah baris per entri untuk memastikan tidak ada tabel kosong
   yang membingungkan (bila kosong → tampilkan panduan, bukan "error").

Acceptance: 33 tautan sidebar berfungsi, tidak ada satu pun jalur tulis ke DB, pencarian & pagination benar,
cetak sesuai 4 fase (print = terang), dan **tampilan tidak meniru** nexgen/Apollo (Bootstrap 5.3.8 + Bootstrap Icons).
Rollback: `akademik/referensi/*` tetap bisa diakses lewat tab legacy.

---

## F-7 — CRUD bertahap (8–12 hari)

**Tujuan**: membuka tulis (Create/Update/Delete) per kelompok data, dari paling rendah risiko.
Setiap item: form Bootstrap 5 + validasi server, `audit_log` sebelum/sesudah, soft-delete (`status`) bila ada,
uji peran (admin vs operator), dan **bukan** sekadar melepas mode readonly F-6 — perlu form & aturan bisnis sendiri.

Matriks gelombang (kolom R/W = baca/tulis; "RO" dari F-6):

| Gelombang | Entri | Alasan urutan | Perkiraan |
|---|---|---|---|
| A — Master kecil, 4–8 kolom | `tingkat`, `semester`, `tahun_ajaran`, `angkatan`, `kategori_surat`, `jenis_surat`, `kategori_keuangan`, `tujuan_surat` | kolom sedikit, tanpa efek keuangan | 2–3 hari |
| B — Master berelasi | `data_lembaga`, `kepala_sekolah`, `instansi`, `jurusan`, `kelas`, `rombel`, `keuangan`, `spp`, `penerimaan`, `jenis_penerimaan`, `no_surat` | ada FK & validasi unik (NPSN, kuota) | 3–4 hari |
| C — Data induk & transaksi | `data_siswa` (65 kolom → form bertahap/tab), `data_guru`, `guru_lembaga`, `kenaikan_kelas`, `alumni`, `sekolah_asal` | paling banyak kolom, perlu upload foto | 3–4 hari |
| D — Transaksional sensitif | `transaksi`, `pembayaran`, `surat_masuk`, `surat_keluar`, `isi_surat_keluar` | uang & surat → wajib audit + nomor urut + approval | 2 hari |
| E — Referensi wilayah | `provinsi`, `kabupaten`, `kecamatan`, `kelurahan`, `kepulauan`, `suku` | jarang berubah, tulis hanya oleh admin yayasan | 1 hari |

Catatan wajib: `data_siswa` 65 kolom tidak digabung jadi satu form — dipecah 5 tab (identitas, alamat, sekolah,
orang tua/wali, status) memakai struktur existing yang sama dengan F-6.

Acceptance per gelombang: CRUD + audit + role-gate + validasi unik + uji dengan 11 lembaga; tidak ada regresi pada
RO view; setiap DELETE memakai soft-delete bila tabel punya `status`.

---

## F-8 — RoundCube engine + SSO (3–5 hari, ada prasyarat user)

**Tujuan**: webmail berbasis RoundCube **1.7.4** di dalam jibas, selaras tema 4 fase, terhubung SSO yayasan.

Prasyarat (harus dipenuhi/disetujui user lebih dulu — bukan sesuatu yang saya asumsikan sudah ada):
1. `extension=imap` di-nyalakan di `php.ini` (kini `;extension=imap`, baris ~933) dan Apache direstart.
2. Server IMAP/SMTP yayasan tersedia (host, port, keamanan) + akun uji.
3. Schema DB baru `jbsmail` untuk tabel RoundCube.
4. Unduhan RoundCube 1.7.4 resmi; **verifikasi `INSTALL`** (kebutuhan PHP/ekstensi) sebelum pasang — PHP terpasang 8.2.12.

Tugas:
1. Pasang RoundCube sebagai komponen jibas, konfigurasi `des_key`, DSN `jbsmail`, `smtp`/`imap` dari poin prasyarat.
2. Skin minimal `style/jibas-roundcube.css` yang meng-import token tema F-2 (bukan skin `apollo`).
3. Proksi masuk lewat shell: tab "Webmail" → `_task=login`, auto-logon terenkripsi dari sesi jibas
   (jangan menaruh kredensial di URL).
4. SSO memakai tabel `yayasan.sso_clients` yang sudah ada (`client_id`, `client_secret`, `redirect_uri`, `home_url`, `active`):
   endpoint authorize/token sisi jibas + registrasi client RoundCube; sesi dibagi lewat cookie domain.
5. Menu header/sidebar Webmail muncul hanya untuk user dengan hak.

Acceptance: kirim & terima 1 surat uji lewat webmail, 4 fase tema berlaku di webmail, logout jibas memutus sesi webmail,
`jbsmail` terpisah dari `jbsyayasan`, dan tidak ada perubahan pada nexgen.
Rollback: folder RoundCube + `jbsmail` bisa dinonaktifkan tanpa mempengaruhi modul jibas lain.

---

## F-9 — Pemangkasan bobot & sunset frameset (3 hari, berjalan paralel setelah F-7)

**Tujuan**: realisasi "tampilan baru dan lebih ringan".

Tugas:
1. Modul yang sudah migrasi → hapus rujukan aset lama (jangan hapus file dulu, pindahkan ke `log/legacy-unused/`).
2. Hentikan `bgstretcher`, `lytebox`, Spry, jQuery-UI 1.8/1.10, datepicker i18n (40+ file), tinymce lama →
   ganti dengan komponen Bootstrap 5 + 1 editor pilihan.
3. Hapus gambar slice tema (`theme/green|pink|...`) dan `images/ico/Icon Header/*.png` milik menu header lama.
4. Ukur ulang & laporkan delta vs `log/baseline-f0.txt` (target ±12 MB aset statis → < 2 MB terpakai).
5. Ganti `akademik/index2.php` frameset jadi redirect ke shell setelah tidak ada modul yang bergantung.

Acceptance: delta bobot terdokumentasi, tidak ada 404 aset di console browser, semua modul masih bisa diakses.

---

## F-10 — Nexgen (pekerjaan terpisah, opsional, di luar `jibas`)

Permintaan "tambahkan sidebar menu Referensi di nexgen [readonly]". Empat titik ubah (rinci di §6.3 dok R&D):
1. `nexgen/skins/apollo/templates/includes/sidebar/sidebar-referensi.html` (dipola dari `sidebar-master-data.html:27-66`).
2. Modul `nexgen/public_html/admin/modules/referensi/{referensi.php,templates/referensi.html}` (guard `Panel::is_admin()`,
   router `admin/index.php:107` tidak perlu diubah).
3. Tautan `<a href="?_task=referensi">` di `sidebar-home-portal.html` + `sidebar-master-data.html:66` (+ panel lain bila perlu).
4. Koneksi read-only ke schema `jbsyayasan` lewat user MySQL `jibas_ro` (hak tulis ditegakkan di level DB).

Status: **belum dikerjakan** — menunggu keputusan apakah nexgen boleh disentuh (ini aplikasi webmail produksi).
Sifatnya hanya menampilkan data referensi, tidak menyalakan CRUD, dan tidak mengubah core `program/` RoundCube.

---

## Urutan eksekusi yang disarankan

```
F-0 → F-2 → F-3            (tampilan terasa, belum menyentuh DB)
      ↓
F-1 → F-4                  (fondasi data & keamanan)
      ↓
F-5 → F-6                  (KPI + Referensi readonly)  ← titik "aman" untuk demo
      ↓
F-7 A→B→C→D→E              (CRUD dibuka bertahap)
      ↓
F-8 (RoundCube, prasyarat) · F-9 (pemangkasan) · F-10 (nexgen, terpisah)
```

Pintu keputusan dari user yang saya butuhkan sebelum lanjut:
1. Setujui F-0 (unduh Bootstrap 5.3.8 + Bootstrap Icons ke `style/bootstrap/`, `script/`).
2. Setujui jalur F-1: bangun tabel sendiri di `jbsyayasan` (`yayasan` sudah dibuat), tanpa referensi ke schema `yayasan` —
   ada `*_down.sql` untuk rollback.
3. Konfirmasi batas readonly F-6: 33 entri Referensi, tanpa tombol tulis — CRUD baru dibuka di F-7.
4. Prasyarat F-8 (imap, mail server, DB `jbsmail`) dan apakah nexgen boleh disentuh (F-10).
