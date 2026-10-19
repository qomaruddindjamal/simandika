# Riset & Pengembangan — Tampilan Modern JIBAS, KPI, dan Skala Yayasan

Versi dokumen: 1.0 (19 Okt 2026) · Sasaran aplikasi: JIBAS v33.0 (`include/version.config.php:25`)
Lokasi: `C:\YIM\JIBAS\xampp\htdocs\jibas` — seluruh pekerjaan HANYA di dalam folder ini, memakai folder
existing `style/` dan `script/`. Tidak membuat folder aplikasi baru.

---

## 1. Ringkasan Eksekutif

JIBAS hari ini adalah aplikasi **pra-Bootstrap**: 7.444 file PHP, layout `<frameset>` + tabel slices gambar,
jQuery 1.9.1, tanpa dashboard. Sementara itu database yayasan (41 tabel di `yayasan`) sudah, dan `jbsyayasan` disiapkan sebagai schema baru dengan tabel sendiri
**multi-tenant by design** — 31 dari 41 tabel punya `id_lembaga`.

Kesimpulan riset: modernisasi tidak dilakukan dengan menambah Bootstrap di atas frameset (mustahil —
frameset memotong DOM per frame), melainkan **membangun shell baru berbasis Bootstrap 5.3.8 di dalam
`style/` + `script/`**, melayani modul lama lewat iframe *di dalam satu halaman* (bukan frameset), lalu
memigrasikan modul satu per satu. Tema built-in Bootstrap 5.3 (`data-bs-theme`) diperluas menjadi 4 fase
waktu (Terang/Senja/Gelap/Fajar) memakai CSS custom properties.

| Aspek | Hari ini | Target |
|---|---|---|
| CSS framework | tidak ada (BS 3.3.5 hanya di `cbe/`, `schooltube/`) | Bootstrap 5.3.8 lokal di `style/bootstrap/` |
| JS | jQuery 1.9.1 + Spry + jQuery-UI 1.8/1.10 | `bootstrap.bundle.min.js` (Popper menyatu), vanilla JS; jQuery hanya untuk modul legacy |
| Layout | `<frameset rows="87,*,41">` + 9-frame sub-layout | 1 halaman: navbar header + sidebar + area konten |
| Tema | integer 1–9 per user, folder gambar slice | 4 token tema + mode Auto berbasis jam server |
| Skala | 1 sekolah / 1 instance (`jbsakad`) | 1 yayasan → N lembaga, filter `id_lembaga` |
| KPI | tidak ada; grafik jpGraph per modul | Dashboard KPI yayasan + lembaga (agregasi `jbsyayasan.*`) |
| Webmail | tidak ada di jibas | RoundCube 1.7.4 engine terpasang di jibas |
| Bobot halaman | ±12 MB aset statistik (images 4,7 MB + `akademik/script` 7,0 MB) | < 300 KB transfer per halaman shell |

---

## 2. Temuan Kondisi Saat Ini (berbukti, bukan asumsi)

### 2.1 Frontend
- Portal `index.php:59` = XHTML 1.0 Transitional; menu berupa tabel `<img>` dengan `onMouseOver="changeImage(...)"`
  (`index.php:113-248`) yang menunjuk ke `akademik/index.php`, `keuangan/rinjani/index.php`, `kepegawaian/index.php`,
  `simtaka/index.php`, `anjungan/index.php`, `cbe/login.php`, `schooltube/index.php`, `infoguru/index.php`.
- Modul akademik = frameset asli: `akademik/index2.php:31-39` (`frametop/frameleft/content/frameright/framebottom`),
  navigasi `target="content"` dan `parent.framecenter.location` (`akademik/frametop.php:56`).
- Menu header lama (ikon 60×35 px, Dreamweaver MM_nbGroup) di `akademik/frametop.php:136-195`:
  REFERENSI, CALON SISWA, GURU & PELAJARAN, JADWAL, KESISWAAN, PRESENSI, PENILAIAN, EXIM, KELULUSAN, MUTASI,
  PELAPORAN, MANAJEMEN USER, LOGOUT. **Ini yang dipertahankan sebagai menu header.**
- Menu REFERENSI saat ini halaman gambar-slice 9-frame (`akademik/referensi.php:38-46`) berisi 12 entri nyata:
  `referensi/{pegawai,identitas,departemen,angkatan,tingkat,tahunajaran,semester,kelas,pilihandata,tambahandata,auditnilai,daftar_siswa}.php`.
- Bootstrap: hanya `cbe/script/bootstrap/` dan `schooltube/script/bootstrap/css/` (BS 3.3.5). Tidak ada satu pun
  tag CDN di seluruh tree (grep `bootstrapcdn|jsdelivr|cdnjs|unpkg|maxcdn` di jibas = 0; satu-satunya CDN adalah
  Font Awesome 6.4.0 di prototipe statis `htdocs/project`). Semua aset harus tetap lokal.
- Tidak ada Chart.js/Morris/Highcharts. Grafik lama: jpGraph (`akademik/include/chartfactory.php`) dan
  `CAsBarDiagram` (`as-diagrams.php`); ±90 halaman `statistik_*` tersebar, bukan dashboard.

### 2.2 Mesin tema lama
`akademik/include/theme.php:29` `GetThemeDir()` membaca `SELECT theme FROM jbsuser.hakakses WHERE login=… AND modul='SIMAKA'`
(default 3) lalu memetakan 1–9 ke folder `theme/green|pink|apple|vista|kopi|wood|gold|granite`. Sifatnya: **per user,
satu angka, berbasis gambar slice** — tidak bisa dipakai untuk terang/gelap. Harus diganti layer token.

### 2.3 Server & database
- PHP **8.2.12** (`php/php.exe -v`), handler Apache `LoadModule php_module ".../php8apache2_4.dll"`
  (`apache/conf/extra/httpd-xampp.conf:20`), `short_open_tag=On` (`php/php.ini:200`) — perlu dipertahankan karena
  7.444 file lama dibuka dengan `<?`. `date.timezone=Asia/Jakarta` (`php.ini:2010 area`) → mode Auto tema aman
  dari masalah zona waktu.
- MySQL/MariaDB **10.4.32** di port **3434**, user `root` / password ada di `include/database.config.php` (tidak ikut di-commit)
  (`include/database.config.php:26-35`, schema default `jbsakad`; skema lain: `jbsuser`, `jbsclient`, `jbsfina`, `jbssdm`, …).
- Ekstensi aktif: curl, gd, intl, json, mbstring, mysqli, openssl, PDO, pdo_mysql, zip. **`imap` dinonaktifkan**
  (`php.ini:933 ;extension=imap`) — prasyarat RoundCube.
- 17 schema `jbs*` berukuran kecil (terbesar `jbsfina` 4,9 MB, `jbsakad` 4,3 MB) → satu install = satu sekolah.

### 2.4 Database yayasan: `jbsyayasan` punya tabel sendiri (bukan salinan `yayasan`)
Status terverifikasi (19 Okt 2026, MySQL port 3434):

| Schema | Isi | Peran |
|---|---|---|
| `yayasan` | **41 tabel**, InnoDB `utf8mb4_general_ci`, 1,1 MB | peninggalan percobaan. **Bukan** sumber query aplikasi baru |
| `jbsyayasan` | **2 tabel** — `yayasan` (1 baris) + `departemen` (1 baris), 19 Okt 2026 | schema final, tabel dibuat bertahap lewat `jbsyayasan.sql` |

Isi `jbsyayasan` saat ini (dibuat sendiri, bukan turunan `yayasan`):

| Tabel | Kolom kunci | Catatan |
|---|---|---|
| `yayasan` | `id`, `nama_yayasan`, `npwp`, `alamat`, `kode_pos`, `telepon`, `email`, `website`, `logo`, `kop_surat`, `status`, `dibuat_pada`, `diubah_pada` | akar tenant; InnoDB `utf8mb4_unicode_ci`, `id INT UNSIGNED` |
| `departemen` | `id`, `id_yayasan`, `kode`, `nama`, `id_kepala`, `urutan`, `keterangan`, `status`, `dibuat_pada`, `diubah_pada` | JOIN `departemen.id_yayasan = yayasan.id` (FK `fk_departemen_yayasan`, ON DELETE RESTRICT); unik `(id_yayasan, nama)` |

Aturan keras (keputusan user, 19 Okt 2026):
1. Aplikasi baru **hanya** terhubung ke `jbsyayasan`. Tidak ada query lintas-schema, tidak ada `VIEW`/FK yang menunjuk
   `yayasan.*`, tidak ada `RENAME TABLE`, dan tidak ada `CREATE TABLE jbsyayasan.x LIKE yayasan.x`.
2. Dari `yayasan` yang dipakai hanyalah **daftar kebutuhan** (nama tabel/kolom di bawah). Struktur `jbsyayasan`
   ditulis ulang sendiri: uang `decimal(15,2)`, tanggal `date`/`datetime`, sandi `password_hash()`, indeks per
   `id_lembaga`/`id_tahun`, `utf8mb4_unicode_ci`, InnoDB, FK eksplisit.
3. Data lama tidak dimigrasikan sebagai skema acuan; bila nanti diperlukan isian awal, datanya di-impor sebagai
   baris bersih hasil anonimisasi — bukan sebagai rujukan struktur.

Temuan pada `yayasan` yang dijadikan acuan desain (bukan acuan struktur):
- **Multi-tenant siap**: 31 dari 41 tabel sudah punya kolom `id_lembaga` — antara lain data_siswa, data_guru,
  guru_lembaga, rombel, kelas, tingkat, semester, tahun_ajaran, angkatan, jurusan, spp, keuangan, kategori_keuangan,
  transaksi, pembayaran, penerimaan, jenis_penerimaan, kenaikan_kelas, alumni, kepala_sekolah, instansi, teman,
  dan seluruh tabel surat (surat_masuk, surat_keluar, no_surat, kategori_surat, jenis_surat, tujuan_surat,
  isi_surat_keluar).
- **10 tabel tanpa `id_lembaga`**: `data_yayasan`, `kabupaten`, `kecamatan`, `kelurahan`, `kepulauan`, `provinsi`,
  `sekolah_asal`, `sso_clients`, `suku`, `user`. → 6 di antaranya adalah referensi wilayah nasional (sudah benar global).
- **Tabel yang TIDAK dipakai lagi** (keputusan user, 19 Okt 2026):
  1. `departemen` — versi legacy (`jbsakad.departemen`, PK berupa nama + kolom sinkronisasi `token`/`issync`/`info1..3`)
     **tidak dipakai sebagai acuan**. Aturan direvisi (19 Okt 2026): departemen tetap ada, tetapi sebagai tabel sendiri
     `jbsyayasan.departemen` yang **ter-join** ke `jbsyayasan.yayasan` lewat FK `id_yayasan` (1 yayasan : N departemen).
     Struktur baru: PK `id`, nama tidak lagi jadi primary key, indeks `(id_yayasan, status, urutan)`, tanpa kolom sinkron.
  2. `teman` — 0 baris, kolom `(id, id_lembaga, id_siswa, id_teman, id_guru)`; tidak dipakai sebagai referensi
     kepegawaian dan dikeluarkan dari daftar menu Referensi.
  Konsekuensi: menu Referensi menjadi **33 entri** (32 setelah `teman` dicabut, lalu +1 karena `departemen` dibuat ulang di `jbsyayasan`), dan kelompok "Pegawai" hanya berisi
  `data_guru` + `guru_lembaga`.
- `data_lembaga` (11 baris) = master lembaga milik `id_yayasan` (1 yayasan: "Yayasan Islam Hasan Kafrawi").
  `user` baru 1 baris (`level='administrator'`).
- Hutang skema `yayasan` yang TIDAK boleh terulang di `jbsyayasan` (dikelola roadmap F-1):
  1. **Tidak ada satu pun indeks selain PRIMARY** (mis. `data_siswa`, `transaksi`, `pembayaran`, `keuangan` hanya punya PK `id`)
     — agregasi KPI per `id_lembaga`/`id_tahun` akan full-scan.
  2. Uang disimpan sebagai `varchar`: `transaksi.pembayaran`, `transaksi.diskon`, `pembayaran.*` sebagian `decimal`
     → KPI keuangan butuh `CAST` dan tidak bisa dipercaya untuk penjumlahan presisi.
  3. Tanggal sebagai `varchar`: `transaksi.tanggal`, `surat_masuk.tanggal` → tren bulanan tidak bisa di-`GROUP BY` langsung.
  4. `user.password` tersimpan **plaintext** (terkonfirmasi: baris `citramedia` = `K0323205`) dan `data_guru.password`
     `varchar` → wajib migrasi ke `password_hash()` sebelum CRUD dibuka.
  5. Data contoh masih sangat sedikit (`data_siswa` 12 baris, hanya id_lembaga 8 dan 11) → belum layak jadi dasar uji beban;
     roadmap memakai data latih hasil anonimisasi dari `jbsakad`.

### 2.5 Nexgen — acuan fungsional, BUKAN acuan visual
`C:\YIM\JIBAS\xampp\htdocs\nexgen` = "Citramedia Apollo Portal & Webmail" (fork Roundcube). Fakta:
- Skin `apollo` memuat **Bootstrap 4.6.2** lokal (`skins/apollo/templates/includes/layout.html:49`), FontAwesome 5,
  `apollo-*.css` 12 file, `bootstrap.bundle.min.js` (`includes/footer-apollo.html:87`); tanpa Chart.js/DataTables.
- Menu sidebar **hardcoded HTML** per panel di `skins/apollo/templates/includes/sidebar/sidebar-*.html`
  (contoh `sidebar-master-data.html:27-66`), dibuat oleh `bin/create_sidebar.php:83-131`. Tidak ada tabel menu di DB.
- Router modul otomatis: `public_html/admin/index.php:107` memetakan `?_task=X` → `modules/X/X.php`;
  gerbang peran lewat class `Panel` (`admin/controller/controller.php` `is_admin():88`, `is_staff():141`) berdasarkan
  kolom `users.level`.
- Tidak ada menu bernama "Referensi" (terdekat: `masterdata`), dan **tidak ada SSO** dengan jibas
  (hanya OAuth login ke Google/Microsoft di `config/config.inc.php:221-292`).
- Kesimpunan untuk R&D: referensi sidebar "Referensi [readonly]" di nexgen cukup 1 file sidebar + 1 modul PHP
  (4 langkah, lihat §6.3). Secara visual **tidak ditiru**: nexgen = BS4.6.2 + FA5 + skin Roundcube; shell baru jibas =
  BS5.3.8 + Bootstrap Icons + token warna sendiri, jadi DNA-nya berbeda sejak layer variabel.

---

## 3. Keputusan Arsitektur (beserta alasan)

**D-01 Bootstrap 5.3.8, lokal, tanpa CDN.**
v5.3.8 adalah rilis minor terakhir seri 5 (seri 6 baru alpha) — sesuai permintaan "Bootstrap 5 terbaru" sambil tetap
aman untuk produksi. CSS/JS sinkron satu sumber (`style/bootstrap/bootstrap.min.css` + `script/bootstrap.bundle.min.js`,
versi sama) sehingga tidak ada selisih perilaku komponen. Popper sudah menyatu di bundle → tidak perlu file terpisah.

**D-02 Shell baru, modul lama masuk lewat `<iframe>` bertabulasi dalam satu DOM.**
Frameset tidak mungkin di-Bootstrap-kan. Shell (navbar + sidebar + tab area) adalah DOM tunggal; halaman lama
di-embed dan akan dilepas bertahap per modul. `target="content"` / `parent.frame*` pada modul yang belum migrasi
dijembatani dengan shim JS kecil di `script/legacy-shim.js` (menyediakan objek `parent.frame*` palsu). Ini jalur
terendah risiko: tidak menyentuh 7.444 file lama di fase awal.

**D-03 Tema = token CSS, bukan folder gambar.**
Satu file `style/jibas-theme.css` mendefinisikan `:root[data-jibas-phase="terang|senja|gelap|fajar"]` dan memetakan
variabel Bootstrap (`--bs-body-bg`, `--bs-body-color`, `--bs-primary`, `--bs-border-color`, `--bs-component-*`).
Tema = atribut, bukan stylesheet lain → 1 request CSS, pergantian tanpa reload, dan ikut warna komponen Bootstrap
secara otomatis.

**D-04 Mode Auto dievaluasi di server (PHP), bukan hanya JS.**
`script/theme.php` menghitung fase dari `Asia/Jakarta` dan menulis `data-jibas-phase` ke `<html>` sebelum byte pertama
keluar → tidak ada *flash of wrong theme*. JS hanya melakukan re-evaluasi saat melewati batas jam saat halaman terbuka
(`setTimeout` ke batas berikutnya) dan menyimpan pilihan manual di `localStorage`.

**D-05 Multi-yayasan lewat scope `id_lembaga`, bukan banyak install.**
Satu instance = satu yayasan; tabel `jbsyayasan.yayasan` (baru) + `lembaga` adalah akar. Setiap user punya daftar lembaga yang
boleh diakses (tabel baru `user_lembaga`), dan semua query wajib menyaring `id_lembaga`. Skema `jbs*` lama (per sekolah)
tetap berjalan dan dihubungkan lewat kolom penghubung `id_lembaga` (tabel `lembaga_client_map`). Alternative
"install per sekolah" ditolak: tidak memberi KPI yayasan.

**D-06 RoundCube 1.7.4 sebagai engine webmail di dalam jibas.**
Seri 1.7 adalah stable terbaru (1.7.4, security release 6 Sep 2026); 1.6 adalah LTS. PHP 8.2.12 memenuhi requirement
1.7 (≥ 8.1) — namun **versi final dan daftar ekstensi wajib diverifikasi dari file `INSTALL` paket unduhan sebelum
instalasi** (dokumen ini tidak mengasumsikannya). Konsekuensi: `extension=imap` harus di-nyalakan di `php.ini`
(keputusan ada di user — perubahan php.ini tidak saya lakukan tanpa persetujuan), dan skema DB RoundCube dibuat terpisah
(`jbsmail`) agar tidak mencemari `jbsyayasan`.

**D-07 Keamanan sebagai syarat, bukan tambahan.**
Semua query baru memakai prepared statement (`mysqli_stmt`) + whitelist kolom. Password → `password_hash()` (bcrypt).
CSRF token untuk semua form CRUD. Session cookie `SameSite=Lax`. Pemetaan 12 halaman lama `pilihandata`/`tambahandata`
(diduga raw-query) masuk daftar audit.

---

## 4. Spesifikasi Tema

Atribut: `<html data-bs-theme="light|dark">` + `<html data-jibas-phase="terang|senja|gelap|fajar">`.
Pilihan user: `Terang`, `Gelap`, `Auto` (default **Auto**).

| Fase | Jam (Asia/Jakarta) | `data-jibas-phase` | `data-bs-theme` | Palet (token utama) |
|---|---|---|---|---|
| Terang | 06:00 – 16:59 | `terang` | `light` | bg `#f7f9fc`, surface `#ffffff`, teks `#152238`, primary `#0f6fb8`, aksen `#12a37f` |
| Senja | 17:00 – 17:59 | `senja` | `dark` | bg `#2b1c33`, surface `#3a2440`, teks `#ffe8d6`, primary `#ff8a5b`, aksen `#c76b9a` (nuansa jingga-ungu) |
| Gelap | 18:00 – 03:59 | `gelap` | `dark` | bg `#0e1319`, surface `#161d26`, teks `#dfe7ef`, primary `#4aa8e8`, aksen `#2fbf8f` |
| Fajar | 04:00 – 05:59 | `fajar` | `light` | bg `#fdf5f0`, surface `#fffaf6`, teks `#3a2c40`, primary `#7c5cad`, aksen `#f0a868` (krem-lavender) |

Aturan implementasi:
1. Batas jam inklusif-eksklusif persis seperti tabel (06:00 masuk Terang; 17:00 masuk Senja; 17:59:59 masih Senja).
2. `gelap` mencakup potongan malam 18:00–23:59 **dan** 00:00–03:59 → logika jam, bukan rentang tunggal.
3. Mode manual (Terang/Gelap) disimpan di `localStorage.jibasTheme` dan `hakakses`/`user` (kolom baru `theme_mode`)
   supaya bertahan lintas device; pilihan "Auto" menghapus override.
4. Tidak ada gambar slice tema; ikon memakai **Bootstrap Icons** (local, 1 file woff2 + css) — sengaja berbeda dari
   Font Awesome milik nexgen/prototipe.
5. `prefers-reduced-motion` dan kontras WCAG AA (≥ 4,5:1 untuk teks normal) diverifikasi untuk keempat fase.
6. Cetak (`@media print`) dipaksa fase `terang`, latar putih.

---

## 5. Spesifikasi KPI

Dua tingkatan: **KPI Yayasan** (agregat semua lembaga dalam scope) dan **KPI Lembaga**. Semua sumber = schema `jbsyayasan`.
Karena `tanggal` dan nominal sebagian `varchar` (§2.4), KPI fase 1 dibaca dari tabel yang bersih (`pembayaran` decimal)
dan agregasi jumlah siswa; KPI berbasis waktu/finance baru aktif setelah migrasi tipe data (Roadmap F-3).

| # | KPI | Definisi | Sumber | Bentuk visual |
|---|---|---|---|---|
| 1 | Total peserta didik aktif | count siswa `status` aktif per periode | `data_siswa` (`id_lembaga`,`status`,`id_tahun`) | number card + sparkline |
| 2 | Total GTK | count guru `status`/`induk` | `data_guru` + `guru_lembaga` | number card |
| 3 | Rombel & kuota | jumlah rombel, isi vs `kuota_rombel` | `rombel` (`id_tahun`,`id_lembaga`,`kuota_rombel`) | bar per lembaga |
| 4 | Sebaran jenjang | RA/TK/SD/SMP/SMA per lembaga | `data_lembaga.jenjang` × siswa | stacked bar |
| 5 | Rasio guru:siswa | GTK / siswa per lembaga | 2 + 1/2 | tabel ranking |
| 6 | Kepatuhan data | % siswa dengan NISN, NIK, alamat, foto lengkap | `data_siswa` kolom kunci | progress ring |
| 7 | Tunggakan / piutang | `pembayaran.jumlah_pembayaran` − terbayar | `pembayaran`,`transaksi`,`keuangan` | number card + bar |
| 8 | Realisasi penerimaan | % tagihan tertagih per tahun buku | `pembayaran` (decimal) × `transaksi` | gauge |
| 9 | Trend pembayaran bulanan | jumlah transaksi & nominal per bulan | `transaksi.tanggal` (butuh migrasi) | line chart |
| 10 | Surat masuk vs keluar | volume + rasio, per lembaga, per bulan | `surat_masuk`, `surat_keluar` | dual bar |
| 11 | Surat belum didisposisi | `otorisasi` kosong/`N` | `surat_masuk.otorisasi` | alert badge |
| 12 | Alumni per angkatan | count alumni per `angkatan` | `alumni` × `angkatan` | bar |
| 13 | PSB konversi | pendaftar → terdaftar (per `penerimaan`) | `penerimaan`,`jenis_penerimaan`,`data_siswa` | funnel |
| 14 | Kesehatan akses | user aktif 30 hari, user terblokir, tanpa lembaga | `user`(+ kolom baru `lastlogin`), `user_lembaga` | table + badge |
| 15 | Peringkat lembaga | skor gabungan 1,7,8,6 (bobot dapat dikonfigurasi) | hasil KPI 1–8 | ranking table |

Spesifikasi teknis: satu endpoint `script/api/kpi.php?scope=yayasan|lembaga&id_lembaga=&id_tahun=` mengembalikan JSON;
dibungkus view cache 5 menit (tabel `kpi_cache` atau file cache di `log/`) supaya 15 KPI tidak jadi 15 query berat
setiap render. Grafik memakai Chart.js UMD lokal (bukan bawaan nexgen) dengan palet diambil dari token tema —
konvensi "jangan meniru nexgen" tetap terpenuhi karena Chart.js di nexgen tidak dipakai (di nexgen hanya ada html2canvas).

---

## 6. Ruang Lingkup Baru

### 6.1 Layout shell (menu header dipertahankan)
- Navbar atas (Bootstrap 5) berisi 13 menu header lama (§2.1) sebagai `.nav-item`, logo + nama yayasan
  (`jbsyayasan.yayasan.nama_yayasan`, `logo`), pemilih yayasan→lembaga→tahun ajaran, ikon tema, ikon webmail, user dropdown.
- Sidebar kiri dapat ditutup (offcanvas di ≤ lg) dengan grup: Dashboard/KPI, Referensi (readonly), Transaksional,
  Surat, Laporan, Webmail, Pengaturan.
- Konten = area tunggal; modul lama lewat iframe bertabulasi; `page-header` konsisten (judul, breadcrumb, aksi).

### 6.2 Sidebar "Referensi" [readonly] — 33 entri
Sesuai permintaan, menu Referensi bersifat **read-only** (list + detail + cetak, tanpa tombol tambah/edit/hapus).
Peta entri (sumber: tabel `jbsyayasan`, padanan modul lama di kurung):

Yayasan & Lembaga: `yayasan` (tabel baru, lihat §2.4), `lembaga`, `departemen` (tabel baru, FK `id_yayasan` → `yayasan.id`), `kepala_sekolah`, `instansi` (modul lama: identitas)
Akademik: `tahun_ajaran` (tahunajaran), `semester`, `tingkat`, `kelas`, `rombel`, `jurusan`, `angkatan`,
`kenaikan_kelas`, `alumni`, `sekolah_asal`
Referensi wilayah: `provinsi`, `kabupaten`, `kecamatan`, `kelurahan`, `kepulauan`, `suku`
Keuangan & pembukuan: `kategori_keuangan`, `keuangan`, `spp`, `penerimaan`, `jenis_penerimaan`, `tahun_buku`
Surat: `kategori_surat`, `jenis_surat`, `tujuan_surat`, `no_surat`
SDM: `data_guru`, `guru_lembaga` (`teman` tidak dipakai lagi — lihat §2.4)

Perilaku: pencarian client-side, pagination server-side, sort whitelist, tombol Cetak/PDF, tombol Edit **tidak dirender**
(bukan sekadar `disabled`) — API menolak `POST` non-readonly dengan 403. CRUD untuk entri-entri ini dibuka bertahap di
Roadmap F-5/F-6.

### 6.3 Nexgen: sidebar Referensi readonly (pekerjaan terpisah, 4 titik ubah)
1. Generate `nexgen/skins/apollo/templates/includes/sidebar/sidebar-referensi.html`
   (pola: `bin/create_sidebar.php:83-131`).
2. Modul baca-saja `nexgen/public_html/admin/modules/referensi/referensi.php` +
   `templates/referensi.html` (guard `Panel::is_admin()`), mengikuti struktur `modules/home-portal/templates/home-portal.html`.
3. Tambahkan `<a href="?_task=referensi">` di `sidebar-home-portal.html` dan `sidebar-master-data.html:66`
   (+ salinan di panel operator/staff bila perlu). Router `admin/index.php:107` tidak perlu diubah.
4. Data referensi dibaca dari schema `jbsyayasan` (bukan `citramedia`) lewat koneksi kedua, read-only user MySQL
   `jibas_ro` — hak tulis di level DB, bukan di aplikasi.

### 6.4 RoundCube engine di jibas
- Unduh RoundCube **1.7.4** (verifikasi `INSTALL`), pasang sebagai komponen jibas dengan konfigurasi sendiri
  (`config` berisi DSN ke schema `jbsmail`, `des_key`, `smtp` milik yayasan).
- Skin custom minimal yang mewarisi **token tema jibas** (§4) supaya 4 fase ikut berlaku di webmail;
  bukan skin `apollo` milik nexgen.
- Menu header/sidebar "Webmail" membuka `<?=_task=login>` dalam tab shell; sesi dibagi lewat cookie domain yang sama
  + tabel `sso_clients` yang **sudah ada di `jbsyayasan`** (kolom `client_id`,`client_secret`,`redirect_uri`,`home_url`,`active`)
  — jadi fondasi SSO-nya tersedia, tinggal diimplementasikan (OAUTH/`auth` flow) di F-7.
- Prasyarat yang harus dipenuhi user lebih dulu: aktifkan `extension=imap` di `php.ini`, sediakan IMAP/SMTP server,
  buat DB `jbsmail`. Tanpa itu webmail tidak bisa diuji — dicatat sebagai blocker, bukan asumsi "sudah siap".

### 6.5 Anjungan = halaman utama, URL root, tampilan modern (menu di atas)
Permintaan: `https://data.yaishk.sch.id/` menampilkan anjungan, nama folder `/anjungan` **tidak boleh muncul
di address bar**, dan tampilannya lebih modern dengan menu di atas.

Yang dipakai sekarang (semua di dalam `htdocs/jibas`, tanpa folder baru, tanpa mengubah file warisan):

| Bagian | Peran |
|---|---|
| `beranda.php` | front controller: rute root + 8 seksi, `chdir(anjungan)`, muat `include/mainconfig.php`, lalu render |
| `include/anjungan-front.php` | peta seksi + pencetak kerangka halaman modern (navbar atas, hero, kartu per seksi, footer) |
| `style/jibas-anjungan.css` | layer tampilan anjungan (shell + restyle potongan/iframe warisan) di atas `jibas-theme.css` |
| `script/anjungan.js` | menu atas: muat potongan seksi, sekuens `<script>`, panggil init warisan, pushState, tema iframe |
| `.htaccess` | `/` dan `/(beranda\|infosekolah\|mading\|infosiswa\|jadkal\|pustaka\|pegawai\|psb)/` → `beranda.php`; `/anjungan/` → 302 `/` |

Cara nama folder tetap tidak muncul: halaman selalu di-render pada URL bersih dan `<base href="/anjungan/">`
hanya berfungsi agar aset serta titik masuk AJAX warisan (`beranda/beranda.php`, `mading/notes.php`, dst.)
tetap resolve. Jadi nama folder terlihat di network tab, bukan di address bar — dan `/anjungan/` langsung
dirapatkan ke `/`. Aset statis root (`/style/`, `/script/`) sengaja **tidak** dilewatkan ke PHP, sehingga
rule lama berbasis `-e` diganti daftar rute eksplisit.

Pemetaan 8 seksi (id pane lama dipertahankan agar script warisan jalan apa adanya):
`beranda`→a, `infosekolah`→b, `mading`→j, `infosiswa`→c, `jadkal`→i, `pustaka`→k, `pegawai`→g, `psb`→h.
Menu bergambar lama (jadwal/pustaka/psb) diganti tombol Bootstrap yang memanggil fungsi warisan yang sama
(`jadkal_ShowKalender()`, `pustaka_browse()`, `psb_InputPsbClicked()`, …); untuk mading & info sekolah
include menunya tetap dipakai karena berisi pilihan departemen dari database, bagian tab gambarnya disembunyikan CSS.

Kepala halaman dua baris: baris identitas (logo+nama, jam, tombol **Masuk**, dropdown tema) dan baris menu.
Menu **Info Siswa sengaja dihilangkan dari baris menu** (`nav_hidden`) — kartanya tetap ada sebagai sasaran
setelah login modal, jadi seksi yang dibuka hanya lewat tombol Masuk.
Tombol tema hanya ikon — nama fase dan rentang jam pindah ke tooltip (`data-jb-phase="title"`) dan header isi dropdown,
isinya Auto / Terang / Gelap dengan default Auto (Senja & Fajar tetap otomatis dari jam).
Label menu dipendekkan (Sekolah, Siswa, Jadwal, Perpust., Pegawai) pada 768–1199 px supaya baris menu tidak pernah
bergulir horizontal di atas 768 px.

Tombol **Masuk** tidak lagi mengarah ke `/akademik/`: ia membuka modal Bootstrap (`data-bs-backdrop="static"`, lihat
aturan modal di bawah) yang formnya meniru login Informasi Siswa warisan, tetapi kolomnya **NISN + PIN**.
Kiriman POST ke `login-siswa.php` (titik masuk baru di root, JSON) yang memvalidasi `jbsakad.siswa` lewat prepared
statement, lalu membuka sesi `__JIBAS_ANJUNGAN_IS__` — sesi yang sama dengan `anjungan/infosiswa/infosiswa.session.php` —
sehingga konten warisan tetap dipakai apa adanya. CATATAN: kolom `pinsiswa` di skema warisan tersimpan apa adanya,
jadi PIN tetap berjalan dalam teks polos (di kueri maupun di jalur HTTPS); pemindahan ke `password_hash()` baru bisa
dilakukan bersamaan dengan migrasi data siswa dan belum terjadi sampai catatan ini ditulis.
Setelah login berhasil, `data.nis` dipakai untuk memuat
`infosiswa/infosiswa.content.php?nis=…&reporttype=PROFIL` (bukan `infosiswa.php`, karena include `infosiswa.main.php`
milik warisan tidak ada di disk) dan isinya dibungkus `<div id="is_main">` (`data-wadah`) karena seluruh navigasi
nilai/presensi/keuangan warisan menulis ke elemen itu.
Kolom login selalu dikosongkan setiap modal dibuka dan memakai `autocomplete=off` / `new-password` supaya kredensial
yang pernah tersimpan browser tidak menempel di form.

Aset per seksi dimuat saat kartunya dibuka: `data-lib` untuk JavaScript warisan dan `data-css` (mekanisme baru yang
mengikutinya) untuk stylesheet yang hanya dipakai satu seksi. Contoh nyatanya laporan **Nilai/Rapor**: halaman induk
lama memuat `script/SpryAssets/SpryTabbedPanels.js` + `.css` di kepala halaman, sedangkan shell modern tidak, sehingga
`showInfoInitTabbedPanel()` di `anjungan/infosiswa/infosiswa.js` memanggil `Spry` yang belum ada. Keduanya sekarang
terdaftar di seksi `infosiswa` dan baru di-request bila Info Siswa benar-benar dibuka.

Hasil uji setelah login (akun uji, 2026-10-19): seluruh option combo laporan dipanggil lewat `GetReportContent()` dan
membalas 200 — PROFIL, KEUANGAN, PRESENSIHARIAN, PRESENSIPELAJARAN, PRESENSIKEGIATAN, NILAI, RAPOR, PERPUSTAKAAN, CBE.
Dua di antaranya sempat fatal karena kode warisan belum siap untuk PHP 8: `count($this->arrdept)` di
`infosiswa.keuangan.class.php:70` dan `sizeof($ajaran)`/`sizeof($kls)` di `infosiswa.presensiharian.php:110,115,125`
— keduanya bernilai `null` bila siswa belum punya baris `jbsakad.riwayatdeptsiswa` / `jbsakad.riwayatkelassiswa`.

**Kedua langkah diambil sekaligus (2026-10-19).** (a) Lima file laporan warisan ditambal: `$dep`/`$ajaran`/`$kls`
(plus `$this->arrdept`, `$aspekarr`, `$pelarr`) selalu berupa array, dan pembacaan `$dep[$departemen]`,
`$ajaran[0][0]`, `$this->arrdept[$this->departemen]` dilindungi `isset(... ? … : $nis_awal/'')`. Salinan semula ada di
`C:\YIM\JIBAS\legacy-backup\2026-10-19-infosiswa\` (di luar docroot, md5 dicocokkan sebelum menyunting) dan
`php -l` bersih untuk semuanya. (b) `anjungan/include/config.php` — konfigurasi yang dimuat setiap fragmen anjungan —
menyetel `display_errors`/`display_startup_errors` = `0`, jadi jejak error tidak lagi muncul ke pengunjung; fatal
tetap tercatat di `apache/logs/data.yaishk.sch.id-ssl-error.log` dan `php.ini` di sini masih `error_reporting`
tanpa `E_WARNING`, sehingga warning tidak tampil dan tidak ditulis. Bukti: file prova sementara yang sengaja
memancing warning + fatal hanya membalas kosong lewat HTTP (200) sementara fatalnya muncul di log.

Uji ulang tanpa satu pun baris riwayat (`DELETE` kedua baris uji, sweep laporan, lalu `INSERT` kembali):
sembilan laporan membalas 200 tanpa teks error — sebelumnya KEUANGAN dan PRESENSIHARIAN fatal.

**Aturan modal untuk seluruh pengembangan baru:** selalu pasang `data-bs-backdrop="static"` — modal hanya ditutup lewat
tombolnya sendiri (X / Batal / aksi eksplisit), tidak boleh hilang karena klik di luar area modal.

Setelah login, **Info Siswa tampil sebagai dasbor bersidebar** (permintaan user 2026-10-19: menu samping, bukan dropdown;
kepala halaman anjungan tetap seperti adanya). Kartunya ditandai `data-dasbor="siswa"`; `script/anjungan.js` membaca option
`<select id="reporttype">` warisan sebagai satu-satunya daftar laporan (tidak ada duplikasi daftar di PHP), lalu membangun
`.anj-sb` di **rel luar kartu** (`<aside class="anj-rail">` di dalam `main.anj-main`, `include/anjungan-front.php`) —
permintaan user 2026-10-19: "sidebar menu taruh di luar card dan saat di scroll halaman ke atas jangan ikut ke atas".
`main.anj-main` menjadi grid dua kolom (`13.5rem` + `minmax(0,1fr)`) hanya saat kelas `anj-ber-rail` terpasang, jadi
seksi lain tetap lebar penuh; `.anj-notice` melintang `1 / -1`. Rel-nya `position: sticky` dengan
`top: calc(var(--anj-topbar-tinggi) + 1rem)` dan `max-height` sisanya (`overflow:auto`), dan variabel tinggi itu diisi
`ukurKepala()` dari `#anjTopbar.offsetHeight` (118 px terukur) saat mulai, saat `load`, dan saat `resize` — angka hardcoded
tidak dipakai karena tinggi kepala berubah menurut lebar layar. `#is_main` warisan tidak pernah menelan sidebar: seluruh
navigasi warisan hanya menulis ke `#is_main`, dan sidebar sekarang bahkan bukan turunannya.
Dropdown "Laporan :", teks labelnya, dan tombol LOGOUT/REFRESH warisan hanya **disembunyikan**
(`display:none`, nilai combo tetap dipakai fungsi warisan), bukan dihapus. Klik item sidebar = `combo.value = tipe` +
`dispatchEvent('change')` sehingga `GetReportContent()` warisan yang bekerja; sebuah `MutationObserver` pada `#is_main`
(dipasang sekali per kartu lewat `data-anj-pengamat`) menyembunyikan ulang kontrol lama dan menyelaraskan item aktif
setiap kali konten berganti. Item khusus: **Muat ulang** (mengulang laporan aktif) dan **Keluar**.
Node sidebar dipertahankan di rel dan hanya kolomnya yang di-toggle: pindah ke seksi lain memanggil `tampilRel(false)`
(rel `display:none`, kartu kembali selebar halaman) dan kembali lewat tombol nama memanggil `tampilRel(true)` tanpa
membangun ulang; `keluarSiswa()` membersihkan rel lewat `kosongkanRel()` karena isi kartu bukan lagi tempat sidebar.

**Keluar tidak lagi ke form login warisan** (permintaan user 2026-10-19: siswa logout diarahkan ke area login lama di
dalam folder `anjungan/infosiswa`). Item Keluar memanggil `keluarSiswa()` di `script/anjungan.js`: konfirmasi, POST
`login-siswa.php?aksi=keluar` (cabang baru, hanya menerima POST — sesi `_JIBAS_ANJUNGAN_IS__` dikosongkan, cookienya
dihapus, `session_destroy()`), lalu kartu `infosiswa` dibersihkan (`data-nis` dilepas, `data-anj-pulih` diisi nilai
tinggi supaya sesi yang baru ditutup tidak otomatis dipulihkan), tombol kepala kembali menjadi **Masuk**, halaman pindah
ke **Beranda**, dan kabar `.anj-kabari` ("Anda telah keluar dari Informasi Siswa.") muncul di kanan atas lalu hilang
sendiri dalam ±4 detik. `is_Logout()` warisan tidak dipakai lagi; fungsi itu menulis form login lama ke `#is_main`.
Fungsionalitasnya tetap warisan dan masih bisa dipakai lewat `/anjungan/` lama karena `infosiswa.logout.php` tidak
diubah — ia hanya meninggalkan `infosiswa.nis` di sesi, dan `?aksi=status` sudah memeriksa `islogin` lebih dulu
sehingga nilai sisa itu tidak bisa dipakai. Teruji di browser 2026-10-19: sesi tertutup (`?aksi=status` → `login:false`),
laporan tidak lagi menampilkan data, kartu aktif kembali ke `anj-kartu-beranda`, toast terlihat (`opacity:1`) lalu
terhapus, dan modal Masuk terbuka kembali dengan fokus di NISN.
Untuk siswa yang punya nilai, `showInfoInitTabbedPanel()` memerlukan Spry TabbedPanels — dulu dimuat di kepala
`anjungan/index.php`, sekarang ikut `data-lib`/`data-css` seksi `infosiswa`.

Sesi dipulihkan lewat `login-siswa.php?aksi=status` (cabang GET, hanya membaca `_JIBAS_ANJUNGAN_IS__` milik sendiri):
saat `/infosiswa/` dibuka ulang atau dituju langsung lewat URL, `infosiswa.php` warisan selalu menampilkan form loginnya
(karena file itu tidak membuka sesi siswa), jadi isinya dibersihkan dan diganti — dasbor bila sesi masih ada, jika tidak
placeholder "Masuk dulu…" plus modal. Percobaan pemulihan dibatasi dua kali per kartu (`data-anj-pulih`) agar konten
warisan yang rusak tidak memicu muat ulang tanpa henti. Pada ≤991,98 px kartu laporan terlalu sempit bila berdampingan
dengan rel, jadi rel pindah ke atas kartu (`position: static`, satu kolom grid) dan daftar laporan menjadi baris chip
yang mengemas diri (`flex-wrap`), tanpa scrollbar horizontal.

**Kepala anjungan tembus pandang 70% saat digulir** (permintaan user 2026-10-19). `pantauGulir()` memasang listener
`scroll` (passive) yang men-toggle kelas `anj-gulung` pada `#anjTopbar` di atas `scrollY` 12 px; CSS-nya mengganti
latar baris identitas dan baris menu dengan `color-mix(in srgb, <warna fase> 30%, transparent)` — jadi 70% tembus pandang,
alpha 0,3 — plus `backdrop-filter: blur(14px) saturate(1.2)` dan bayangan dilepas. Yang diturunkan hanya alfa warna fase
yang sedang aktif (Terang/Senja/Fajar/Gelap ikut), bukan warna baru, sehingga teks kepala tetap sesuai token
`--jb-navbar-text`. Verifikasi browser 2026-10-19: dengan transisi dimatikan, `backgroundColor` terhitung
`color(srgb 1 1 1 / 0.3)` (baris identitas) dan `color(srgb .933 .949 .973 / 0.3)` (baris menu); kelas dan
`backdrop-filter` hanya muncul sesudah gulir. Catatan: di tab otomatis yang tidak melukis frame, event `scroll` asli
tidak terkirim (sama seperti `requestAnimationFrame`) — kelas diuji dengan mengirim `new Event('scroll')`, dan nilai
alpha harus dibaca setelah `transition` dimatikan karena nilai terhitung saat transisi berjalan adalah nilai antara.

Tombol **Masuk** di baris identitas ikut menyesuaikan, dan **dipindah ke paling kanan, sesudah dropdown tema**
(permintaan user 2026-10-19: "taruh di belakang theme saja, taruh samping kanan"). Selama sesi siswa aktif ia menjadi
pintu dasbor berlabel **nama lengkap siswa** (menggantikan tulisan "Info Siswa") plus **avatar**: bila
`jbsakad.siswa.foto` (mediumblob) terisi, avatar menampilkan fotonya lewat `login-siswa.php?aksi=foto`; bila kosong,
avatar menampilkan **inisial nama depan + nama kedua** (mis. "Abdul Rahman Setiawan" → `AR`, satu kata → satu huruf).
Inisial selalu ada di DOM sebagai lapisan bawah dan `<img>` foto menimbunnya, jadi foto yang hilang di tengah jalan
(`img.onerror` melepas `<img>`) kembali ke inisial, bukan kotak rusak. `?aksi=foto` hanya melayani nis milik sesi yang
berjalan — nis tidak diterima dari parameter, supaya foto siswa lain tidak bisa diminta lewat URL — dan membalas 404
bila blob kosong; respons login/status membawa `berfoto` sehingga avatar ikut terisi tanpa permintaan tambahan.
Nama diambil dari hasil login/`?aksi=status`, disimpan di `data-nama`/`data-foto` kartu `infosiswa` supaya tetap terbaca
saat halaman dimuat ulang atau saat `MutationObserver` dasbor memanggil ulang penataan tombol; bila sesi dibuat alur
warisan sehingga nama tidak diketahui, label jatuh kembali ke "Info Siswa". Nama panjang dipotong `ellipsis`
(maks 13rem, 7,5rem di ≤575,98 px) dan utuhnya ada di `title`; proporsinya diukur di browser 2026-10-19 — avatar
1,25rem (20 px) dalam chip setinggi 32 px, sama persis dengan tinggi tombol tema di sebelahnya dan sepusat secara
vertikal (permintaan user: avatar pertama kali terlalu besar sehingga chip lebih gemuk dari kontrol tema);
di layar sempit label "Masuk" disembunyikan dan avatar tetap tampil. Status sesi dibaca sekali saat halaman dibuka (`login-siswa.php?aksi=status`, dilewati bila seksi yang
aktif memang Info Siswa) dan setiap kali sesi berubah (login, Keluar — setelah Keluar wujud kembali "Masuk" berikon
panah, tanpa avatar dan tanpa `title`). Tombol ini tetap diperlukan karena seksi Info Siswa tidak ada di baris menu,
jadi tanpa tombolnya tidak ada jalan kembali ke dasbor.

Pengecualian jujur atas anggaran §7: jalur anjungan masih memakai jQuery + library warisan (syarat potongan
`anjungan/*`), tetapi hanya **1 seksi per muat awal** (dulu 8 permintaan AJAX serentak) dan tanpa TinyMCE,
bgStretcher, serta jVertTabs, sehingga jumlah request awal turun dan halaman tetap bisa di-cache service worker.

Catatan keamanan ikut tertutup di `.htaccess`: `include/`, `log/`, path ber-titik, `.sql/.md/.ini/.conf/.bak/…`,
`database.config.php`/`.example.php` → 403; vhost 443 (mkcert) memaksa HTTP → HTTPS.

---

## 7. Ringan & Performa

Anggaran keras (hard budget) per halaman shell:
- CSS ≤ 120 KB gzip (bootstrap.min.css + 1 theme.css + 1 icons.css), JS ≤ 90 KB gzip (bundle + 1 app.js + chart.js hanya
  di halaman KPI), **0** gambar slice, **0** jQuery di jalur baru.
- Font Awesome 6 CDN di prototipe diganti Bootstrap Icons lokal (meniadakan request eksternal — penting karena
  jaringan sekolah sering offline/terbatas).
- Query KPI: wajib berjalan di indeks; karena §2.4 (tidak ada indeks sekunder sama sekali), F-1 menambah indeks
  `id_lembaga`, `id_tahun`, kombinasi `(id_lembaga,id_tahun)`, dan indeks kolom tanggal setelah migrasi tipe.
- Target: TTFB < 250 ms, DOMContentLoaded < 1 s pada localhost, Lighthouse Performance ≥ 90.
- Yang boleh dihapus setelah modul bermigrasi (hemat ±9 MB statis): `akademik/script` 7,0 MB (Spry, jQuery-UI 1.8/1.10,
  datepicker i18n 40+ file, tinymce lama), `images` 1,9 MB + `akademik/images` 2,8 MB slice tema.

---

## 8. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Frameset lama dipaksa Bootstrap | layout rusak massal | shell baru + iframe Shim (D-02), jangan konversi in-place |
| `short_open_tag` dibutuhkan 7.444 file lama | fatal error bila diubah | pertahankan `php.ini:200 On`; kode baru selalu `<?php` |
| Tema senja/fajar dianggap "sekadar gelap/terang" | fitur tak terasa | token distinct + pratinjau 4 fase di halaman Pengaturan; uji visual tiap fase |
| Tidak ada indeks di `jbsyayasan` | KPI lambat saat data riil masuk | F-1 migrasi indeks + cache KPI; ukur `EXPLAIN` sebelum merge |
| `pembayaran`/`tanggal` varchar | angka KPI salah | F-3 migrasi tipe + validasi; sementara hanya KPI non-presisi |
| Password plaintext | risiko keamanan besar | F-4 `password_hash()` + reset paksa, sebelum CRUD dibuka |
| Nexgen (Roundcube fork) ikut berubah | webmail produksi error | 6.3 adalah pekerjaan terisolasi; jangan menyentuh `program/` core, dan **jangan** mengubah `app.asar.unpacked` di aplikasi desktop lain |
| RoundCube butuh imap + PHP 8.1+ | install gagal | verifikasi `INSTALL` + syarat di §6.4 sebagai gerbang F-7; fallback: link ke nexgen saja |
| 1 yayasan = 1 instance membatasi grup besar | skalabilitas | arsitektur siap `id_lembaga`; bila nanti multi-yayasan, `jbsyayasan.yayasan` sudah menjadi akar FK |

---

## 9. Cara Verifikasi (bukti, bukan asumsi)

```bash
# koneksi & isi schema
"C:/YIM/JIBAS/xampp/mysql/bin/mysql.exe" -u root -p'<password-root>' -P 3434 -h 127.0.0.1 yayasan -e "SHOW TABLES"

# cek indeks sebuah tabel (harus > PRIMARY setelah F-1)
... yayasan -e "SHOW INDEX FROM data_siswa"

# cek PHP runtime + ekstensi (imap harus muncul setelah prasyarat F-7)
"C:/YIM/JIBAS/xampp/php/php.exe" -m | grep -i imap

# payload halaman shell (harus <= 300 KB transfer, 0 gambar slice)
curl -s -o NUL -w "%{size_download} bytes\n" http://localhost/jibas/index.php

# fase tema pada jam tertentu
"C:/YIM/JIBAS/xampp/php/php.exe" -r "echo date('H:i');"; # bandingkan dengan atribut data-jibas-phase di HTML
```

Roadmap pengerjaan ada di `ROADMAP.md` (folder yang sama, hanya dokumen — implementasi CRUD menyusul per fase).
