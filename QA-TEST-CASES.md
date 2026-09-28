# Dokumen Analisis dan Test Case QA — Peminjaman Alat

## Ruang lingkup dan cara menggunakan dokumen

Dokumen ini merupakan **rancangan test case berbasis analisis source code**, bukan laporan hasil pengujian aktual. Aplikasi tidak dijalankan selama analisis; kolom `ACTUAL RESULT` sengaja dikosongkan dan seluruh `STATUS` diisi `NOT TESTED`.

Proyek memiliki dua permukaan aplikasi yang perlu dibedakan saat menguji:

- **Web**: halaman Blade dan route pada `routes/web.php`. Gunakan browser, sesi login, tombol/form yang disebut di langkah, dan akun dengan role yang disebutkan.
- **API**: route pada `routes/api.php`, autentikasi Sanctum Bearer token, dan respons JSON. Langkah yang menyebut API harus dilakukan menggunakan klien API; jangan menyamakan hasil API dengan tampilan web.

Gunakan database dan file uji terisolasi. Buat data baru untuk setiap skenario yang mengubah atau menghapus data. Jangan menjalankan kasus hapus kategori yang memiliki alat di database berisi data penting.

## BAGIAN 1: HASIL ANALISIS PROYEK

### Teknologi dan struktur

- Laravel 12, PHP `^8.2`, Blade untuk antarmuka web, Sanctum untuk API token, Eloquent ORM, dan SQLite tersedia sebagai database proyek. Dompdf tercantum sebagai dependensi, tetapi fitur cetak web Petugas yang ditemukan menggunakan `window.print()`; tidak ditemukan pemanggilan Dompdf dari alur cetak tersebut.
- Role database dibatasi pada `admin`, `petugas`, dan `peminjam`. Login web menggunakan **email dan password** (bukan username).
- Relasi data utama: `users` memiliki peminjaman; peminjaman memiliki detail alat dan satu pengembalian; kategori memiliki banyak alat; pengembalian menyimpan peminjaman serta petugas pemroses. Foreign key tertentu menggunakan cascade delete.
- Web mengelola sesi login; API login/register mengeluarkan token Sanctum dan API terlindungi memakai `auth:sanctum`.

### Fitur ditemukan per role

| Role | Fitur web yang ditemukan | Fitur API yang ditemukan |
|---|---|---|
| Admin | Dashboard/log terbaru; CRUD User, Alat, Kategori; buat/lihat/hapus peminjaman dan ubah status; lihat antrean pengembalian dan proses pengembalian. | CRUD Kategori, Alat, User, Peminjaman (termasuk approve/update/delete), baca pengembalian (index/show), koreksi/hapus pengembalian, baca log dan laporan peminjaman. |
| Petugas | Lihat pengajuan; setujui atau tolak pengajuan; pantau transaksi berstatus dipinjam; proses pengembalian; lihat dan cetak laporan melalui dialog browser. | Setujui peminjaman; buat pengembalian. Route API petugas tidak memberi akses ke update/delete pengembalian atau laporan. |
| Peminjam | Lihat katalog alat tersedia; ajukan peminjaman; lihat riwayat sendiri. | Lihat katalog, ajukan dan lihat riwayat peminjaman sendiri; API memiliki pemeriksaan kepemilikan saat membaca/mengubah/menghapus peminjaman. |

### Alur dan aturan yang didukung implementasi

- Login web memvalidasi email berformat email dan password wajib, meregenerasi sesi saat berhasil, lalu mengarahkan Admin ke dashboard, Petugas ke daftar peminjaman, dan Peminjam ke katalog. Logout web menghapus sesi dan token CSRF lalu kembali ke halaman login.
- Pengajuan web Peminjam mewajibkan tanggal rencana kembali setelah hari ini, array `alat_id` dan `jumlah`; status awal `diajukan`. Controller web tidak memvalidasi setiap ID/jumlah atau membandingkan jumlah dengan stok. Form katalog hanya menampilkan alat dengan stok positif, tetapi batas kuantitas di HTML bukan pengganti validasi server.
- Pengajuan Admin memvalidasi user, tanggal, alat, dan jumlah; tanggal rencana kembali harus sama atau setelah tanggal pinjam. Controller memeriksa stok pada saat pengajuan. Persetujuan Petugas mengubah status ke `dipinjam` dan mengurangi stok.
- Pengembalian Admin membatasi kondisi ke `baik`, `rusak ringan`, `rusak berat`, atau `tidak lengkap`; menghitung denda keterlambatan Rp5.000/hari ditambah denda kerusakan; memulihkan stok dan mengubah status menjadi `dikembalikan`. Pengembalian web Petugas memakai alur berbeda: status menjadi `selesai`, kondisi opsional (default `Baik`) dan stok dipulihkan.
- API pengajuan membatasi tanggal format `YYYY-MM-DD` mulai hari ini, minimal satu item, ID alat ada, jumlah integer minimal 1, serta memeriksa stok; status awal `diajukan`. Persetujuan API mengurangi stok. API pengembalian hanya menerima peminjaman berstatus `dipinjam`; mencatat status peminjaman menjadi `telat` atau `dikembalikan` menurut tanggal, membuat catatan pengembalian, memulihkan stok, dan mencatat aktivitas secara eksplisit.
- Admin web menolak penghapusan kategori yang masih memiliki alat. API kategori menggunakan `apiResource`; penghapusan langsung tidak memeriksa relasi dan migration alat memiliki cascade delete pada kategori, sehingga hapus kategori terpakai berpotensi menghapus alat terkait.
- Daftar Admin web umumnya dipaginasi 10 data; kategori 5; pencarian tersedia untuk User, Alat, Kategori, Peminjaman dan halaman Petugas yang relevan. Katalog web hanya menampilkan stok lebih dari nol dan tidak memiliki pencarian.

### Batasan / hal yang belum dapat diverifikasi

1. **Peminjam mengembalikan alat**: tidak ditemukan tombol/form atau route web untuk Peminjam mengirim pengembalian. API `POST /api/pengembalian` hanya berada di middleware Petugas; Peminjam tidak diberi route pembuatan pengembalian. Fitur yang diminta tersebut **BELUM DAPAT DIVERIFIKASI sebagai alur Peminjam** dan tidak dibuatkan test case fungsional yang mengada-ada. Pengembalian dicatat oleh Petugas atau Admin web.
2. **Edit detail peminjaman web Admin**: web menyediakan pembuatan, daftar, ubah status, dan hapus, tetapi tidak menyediakan edit tanggal/user/item. API mempunyai update untuk permohonan berstatus `diajukan`. Karena itu tidak ada test edit detail pada web; operasi API dicakup dalam bagian API.
3. **CRUD Pengembalian Admin**: web Admin hanya menyediakan baca antrean dan proses/buat pengembalian. API Admin menyediakan index/show/update/delete, tetapi route `POST /api/pengembalian` untuk membuat pengembalian hanya diberikan kepada Petugas.
4. **Log otomatis melalui Observer**: file `AlatObserver`, `PeminjamanObserver`, dan `PengembalianObserver` ada, tetapi tidak ditemukan pendaftaran observer pada provider/bootstrap. Dashboard Admin membaca maksimal 10 log; API log menampilkan semua. API proses pengembalian menulis log secara eksplisit. Catatan otomatis dari observer untuk perubahan lain **BELUM DAPAT DIJAMIN** sebelum pendaftaran observer dikonfirmasi.
5. **Perbedaan nama relasi**: model `Peminjaman` mendefinisikan `detailPinjams()`, sedangkan beberapa controller/view memanggil `detailPinjam`. Sebagian alur tampilan/persetujuan/pengembalian dapat gagal saat runtime akibat relasi yang tidak ditemukan. Test di bawah tetap memuat skenario sesuai route/tujuan bisnis, namun jika gagal karena relasi ini catat sebagai `FAIL` dan lampirkan respons/exception, bukan menutupi kegagalan.
6. **Perbedaan status**: kolom status migration adalah `diajukan`, `dipinjam`, `dikembalikan`, `telat`, sedangkan alur Petugas menetapkan `selesai` dan beberapa view menampilkan status itu. Pengujian transisi status akhir perlu mencatat perbedaan implementasi ini.
7. Tidak ditemukan halaman detail User/Alat/Kategori terpisah; informasi alat terlihat di tabel/katalog. Tidak ditemukan dialog pembatalan transaksi selain konfirmasi JavaScript pada tombol hapus/setujui/tolak/terima kembali.
8. Form web login maupun test case berikut memakai akun yang sudah tersedia. Registrasi API membuat role `peminjam`; login API memvalidasi kredensial tetapi tidak membatasi role. Middleware API Admin/Petugas/Peminjam mengembalikan 403 bagi role yang tidak sesuai.

## BAGIAN 2: TEST CASE ROLE ADMIN

### A. Login

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-LOGIN-001 | Login Admin dengan kredensial valid | Akun Admin aktif tersedia; browser belum login. | 1. Buka `/login`.<br>2. Isi Email dan Password.<br>3. Klik **Masuk**. | Email: `admin.qa@example.test`<br>Password: `AdminQa123` | Login berhasil dan browser diarahkan ke dashboard Admin. |  | NOT TESTED |
| ADMIN-LOGIN-002 | Membuka halaman Admin setelah login | ADMIN-LOGIN-001 berhasil. | 1. Akses `/admin/dashboard`.<br>2. Periksa halaman dan nama/role yang ditampilkan. | Akun Admin yang sama. | Halaman dashboard Admin tampil dan menunjukkan pengguna ber-role Admin. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-LOGIN-003 | Menolak password Admin yang salah | Akun Admin tersedia; belum login. | 1. Buka `/login`.<br>2. Isi email Admin dan password salah.<br>3. Klik **Masuk**. | Email: `admin.qa@example.test`<br>Password: `Salah123` | Login ditolak; tetap di form login dan validasi menampilkan bahwa email atau password salah. |  | NOT TESTED |
| ADMIN-LOGIN-004 | Menolak email yang tidak terdaftar | Belum login. | 1. Buka `/login`.<br>2. Isi email yang tidak terdaftar dan password apa pun.<br>3. Klik **Masuk**. | Email: `tidak-ada.qa@example.test`<br>Password: `AdminQa123` | Login ditolak dan pesan kredensial salah ditampilkan. |  | NOT TESTED |
| ADMIN-LOGIN-005 | Menolak kolom login kosong | Belum login. | 1. Buka `/login`.<br>2. Kosongkan Email dan Password.<br>3. Klik **Masuk**. | Email: kosong<br>Password: kosong | Form menolak pengiriman karena kedua kolom wajib. |  | NOT TESTED |
| ADMIN-LOGIN-006 | Menolak format email tidak valid | Belum login. | 1. Buka `/login`.<br>2. Isi email tanpa format email dan password.<br>3. Klik **Masuk**. | Email: `admin-bukan-email`<br>Password: `AdminQa123` | Validasi email menolak input dan pengguna tetap di halaman login. |  | NOT TESTED |
| ADMIN-LOGIN-007 | Menolak Admin mengakses halaman role lain | Login sebagai Admin. | 1. Akses route web khusus Peminjam `/peminjam/katalog`.<br>2. Catat respons. | Sesi Admin aktif. | Middleware role Peminjam menolak akses (403). |  | NOT TESTED |

### B. Logout

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-LOGOUT-001 | Logout Admin dan kembali ke login | Admin telah login. | 1. Klik tombol **Logout** pada navigasi.<br>2. Konfirmasi logout bila dialog tampil.<br>3. Periksa halaman tujuan. | Sesi Admin aktif. | Sesi diakhiri dan browser diarahkan ke `/login`. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-LOGOUT-002 | Menolak akses dashboard sesudah logout | Admin baru saja logout. | 1. Ketik `/admin/dashboard` pada address bar.<br>2. Muat halaman. | Sesi sudah diinvalidasi. | Dashboard terlindungi tidak menampilkan data Admin; pengguna diarahkan ke autentikasi atau ditolak. |  | NOT TESTED |

### C. CRUD User

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-USER-001 | Menambahkan User Petugas | Admin login; email belum dipakai. | 1. Buka **Manajemen Pengguna**.<br>2. Klik **+ Tambah User**.<br>3. Isi form dan klik **Simpan**. | Nama: Naya QA<br>Email: `naya.qa@example.test`<br>Password: `Naya123`<br>Role: Petugas<br>No. HP: `081234567890` | User dibuat, password disimpan sebagai hash, dan user baru terlihat di daftar. |  | NOT TESTED |
| ADMIN-USER-002 | Melihat dan mencari daftar User | Admin login; tersedia beberapa User. | 1. Buka daftar User.<br>2. Cari nama, email, atau role yang ada.<br>3. Periksa hasil. | Search: `petugas` | Tabel menampilkan kolom Nama, Email, Role, No. HP; hasil pencarian yang cocok tampil. |  | NOT TESTED |
| ADMIN-USER-003 | Memperbarui data User tanpa mengganti password | User target tersedia. | 1. Klik **Edit** pada User.<br>2. Ubah Nama dan No. HP, pertahankan email.<br>3. Kosongkan password jika field tersedia.<br>4. Simpan. | Nama baru: Naya QA Updated<br>Email: `naya.qa@example.test`<br>Password: kosong<br>No. HP: `081234567891` | Perubahan nama/no. HP tersimpan; password lama tidak diganti. |  | NOT TESTED |
| ADMIN-USER-004 | Menghapus User setelah konfirmasi | User uji tersedia dan tidak dibutuhkan data lain. | 1. Klik **Hapus** pada User uji.<br>2. Pilih **OK** pada konfirmasi browser. | User: `naya.qa@example.test` | User dihapus dari daftar. Periksa dampak cascade pada transaksi terkait hanya dengan data uji. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-USER-005 | Menolak User baru dengan data wajib kosong | Admin login dan membuka form tambah User. | 1. Kosongkan Nama, Email, Password, atau Role.<br>2. Klik **Simpan**. | Nama: kosong<br>Email: kosong<br>Password: kosong<br>Role: kosong | Validasi menolak data wajib yang kosong dan tidak membuat User. |  | NOT TESTED |
| ADMIN-USER-006 | Menolak email User yang sudah digunakan | Email sudah terdaftar. | 1. Buka form tambah User.<br>2. Isi data lain valid dengan email yang telah dipakai.<br>3. Klik **Simpan**. | Email: `admin.qa@example.test` | Validasi unique menolak email duplikat; data lama tidak berubah. |  | NOT TESTED |
| ADMIN-USER-007 | Menolak role atau password tidak sesuai validasi API | Admin memiliki token API; endpoint `POST /api/user`. | 1. Kirim JSON dengan role di luar tiga role yang diizinkan, lalu ulangi dengan password yang kurang dari 8 karakter/tidak berisi huruf dan angka. | `role: supervisor` atau `password: abc123` | API menolak request dengan validasi (422); User tidak dibuat. |  | NOT TESTED |
| ADMIN-USER-008 | Membatalkan penghapusan User | User uji tersedia. | 1. Klik **Hapus** pada User uji.<br>2. Pilih **Cancel** pada konfirmasi browser.<br>3. Muat ulang daftar. | User: `naya.qa@example.test` | Penghapusan dibatalkan oleh browser; User tetap ada. |  | NOT TESTED |
| ADMIN-USER-009 | Menolak update User dengan email milik User lain | Dua User dengan email berbeda tersedia; token Admin untuk API. | 1. Kirim `PUT /api/user/{id}` untuk User pertama.<br>2. Isi email yang sudah dimiliki User kedua.<br>3. Kirim request. | Email target: email User kedua | API mengembalikan validasi 422 dan data User pertama tidak berubah. |  | NOT TESTED |

### D. CRUD Alat

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-ALAT-001 | Menambahkan Alat dengan data valid | Admin login; kategori tersedia. | 1. Buka **Manajemen Data Alat**.<br>2. Klik **+ Tambah Alat**.<br>3. Isi form dan klik **Simpan**. | Nama: Multimeter QA<br>Kategori: kategori uji<br>Stok: 3<br>Status Kondisi: Baik<br>Deskripsi: alat uji<br>Gambar: kosong | Alat tersimpan dengan kategori, stok, kondisi, dan deskripsi yang diisi. |  | NOT TESTED |
| ADMIN-ALAT-002 | Melihat, mencari, dan memeriksa daftar Alat | Admin login; tersedia lebih dari satu alat. | 1. Buka daftar Alat.<br>2. Cari nama alat atau nama kategori.<br>3. Periksa baris hasil dan pagination. | Search: `Multimeter QA` | Baris menampilkan gambar/tanda tidak ada, nama, kategori, stok, kondisi; pencarian cocok dan halaman dapat dipaginasi. |  | NOT TESTED |
| ADMIN-ALAT-003 | Memperbarui data Alat | Alat uji dan kategori lain tersedia. | 1. Klik **Edit** pada Alat.<br>2. Ubah nama, kategori, dan stok.<br>3. Simpan. | Nama baru: Multimeter QA 2<br>Stok: 4<br>Kondisi: Baik | Data Alat diperbarui dan tampil di daftar. |  | NOT TESTED |
| ADMIN-ALAT-004 | Menghapus Alat setelah konfirmasi | Alat uji tanpa transaksi penting tersedia. | 1. Klik **Hapus**.<br>2. Pilih **OK**.<br>3. Periksa daftar. | Nama: Multimeter QA 2 | Alat dihapus; bila memiliki gambar, file gambar terkait juga dihapus oleh controller web. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-ALAT-005 | Menolak Alat dengan field wajib kosong | Admin membuka form tambah Alat. | 1. Kosongkan nama, kategori, stok, atau kondisi.<br>2. Klik **Simpan**. | Nama: kosong; Kategori: kosong; Stok: kosong; Kondisi: kosong | Validasi menolak field wajib dan Alat tidak dibuat. |  | NOT TESTED |
| ADMIN-ALAT-006 | Menolak kategori tidak ada dan stok negatif | Admin menggunakan API `POST /api/alat` dengan token Admin. | 1. Kirim request dengan `kategori_id` yang tidak ada atau stok `-1`.<br>2. Periksa respons dan daftar Alat. | `kategori_id: 999999`, `stok: -1` | API menolak input dengan validasi 422; data tidak dibuat. |  | NOT TESTED |
| ADMIN-ALAT-007 | Menolak gambar bukan gambar atau melebihi batas | Admin login dan membuka form tambah/edit Alat. | 1. Unggah file PDF sebagai gambar, lalu ulangi dengan gambar lebih dari 2 MB.<br>2. Kirim form. | PDF uji; gambar > 2048 KB | Validasi gambar menolak format/ukuran; alat tidak tersimpan dengan file tidak valid. |  | NOT TESTED |
| ADMIN-ALAT-008 | Membatalkan penghapusan Alat | Alat uji tersedia. | 1. Klik **Hapus**.<br>2. Pilih **Cancel**.<br>3. Periksa daftar. | Alat uji. | Alat tetap tampil dan belum dihapus. |  | NOT TESTED |

### E. CRUD Kategori

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-KAT-001 | Menambahkan Kategori baru | Admin login. | 1. Buka **Manajemen Kategori Alat**.<br>2. Klik **+ Tambah Kategori**.<br>3. Isi nama dan klik **Simpan**. | Nama Kategori: QA Periferal | Kategori tersimpan dan muncul di daftar. |  | NOT TESTED |
| ADMIN-KAT-002 | Melihat dan mencari daftar Kategori | Admin login; beberapa kategori tersedia. | 1. Buka daftar Kategori.<br>2. Isi nama kategori pada kolom pencarian.<br>3. Klik **Cari**. | Search: `QA Periferal` | Daftar menampilkan kategori cocok; pagination tersedia, 5 data per halaman. |  | NOT TESTED |
| ADMIN-KAT-003 | Memperbarui Kategori | Kategori uji tersedia. | 1. Klik **Edit**.<br>2. Ubah nama menjadi nama baru yang belum dipakai.<br>3. Klik **Simpan**. | `QA Periferal Updated` | Nama kategori diperbarui. |  | NOT TESTED |
| ADMIN-KAT-004 | Menghapus Kategori yang tidak digunakan | Kategori uji tidak memiliki alat terkait. | 1. Klik **Hapus**.<br>2. Pilih **OK**.<br>3. Periksa daftar kategori. | Kategori: `QA Periferal Updated` | Kategori terhapus dan pesan sukses ditampilkan. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-KAT-005 | Menolak nama Kategori kosong | Admin membuka form tambah Kategori. | 1. Biarkan nama kosong.<br>2. Klik **Simpan**. | Nama Kategori: kosong | Validasi menolak penyimpanan. |  | NOT TESTED |
| ADMIN-KAT-006 | Menolak nama Kategori duplikat | Kategori dengan nama tersebut sudah ada. | 1. Buka form tambah Kategori.<br>2. Masukkan nama yang sudah digunakan.<br>3. Klik **Simpan**. | Nama Kategori: `Jaringan` (yang sudah tersedia) | Validasi unique menolak kategori duplikat. |  | NOT TESTED |
| ADMIN-KAT-007 | Menolak penghapusan Kategori yang masih digunakan pada web | Kategori memiliki minimal satu Alat. | 1. Di web Admin, klik **Hapus** kategori tersebut.<br>2. Pilih **OK**.<br>3. Periksa pesan dan Alat terkait. | Kategori terpakai. | Web menampilkan pesan kategori tidak dapat dihapus; kategori dan alat tetap ada. |  | NOT TESTED |
| ADMIN-KAT-008 | Menguji hapus Kategori terpakai pada API | Token Admin API tersedia; gunakan database uji dan kategori dengan alat uji. | 1. Kirim `DELETE /api/kategori/{id}`.<br>2. Periksa status respons, kategori, dan alat terkait. | Kategori dan alat uji | Catat perilaku aktual. Source API tidak memiliki pemeriksaan relasi; migration memberi cascade pada alat. Jangan jalankan terhadap data penting. |  | NOT TESTED |
| ADMIN-KAT-009 | Membatalkan penghapusan Kategori | Kategori uji tersedia. | 1. Klik **Hapus**.<br>2. Pilih **Cancel**.<br>3. Periksa daftar. | Kategori uji. | Kategori tetap tersimpan. |  | NOT TESTED |

### F. CRUD Data Peminjaman (operasi yang tersedia)

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-PINJAM-001 | Membuat Peminjaman dari panel Admin | Admin login; tersedia User role peminjam dan alat dengan stok cukup. | 1. Buka daftar Peminjaman.<br>2. Klik **+ Tambah Peminjaman**.<br>3. Pilih peminjam dan alat, isi tanggal/jumlah.<br>4. Klik **Simpan Peminjaman**. | Peminjam: user QA<br>Tgl pinjam: hari ini<br>Rencana kembali: hari ini + 3 hari<br>Alat: alat stok 3<br>Jumlah: 1 | Data tersimpan dengan status `diajukan`; Admin diarahkan ke daftar peminjaman. |  | NOT TESTED |
| ADMIN-PINJAM-002 | Melihat dan mencari Data Peminjaman | Admin login; ada beberapa transaksi. | 1. Buka daftar Peminjaman.<br>2. Cari nama peminjam atau status.<br>3. Periksa tanggal, alat/jumlah dan status. | Search: nama peminjam uji atau `diajukan` | Tabel menampilkan data transaksi yang sesuai; pencarian dan pagination berfungsi. |  | NOT TESTED |
| ADMIN-PINJAM-003 | Mengubah status menjadi Dipinjam dan mengurangi stok | Peminjaman berstatus `diajukan`; stok cukup. | 1. Pada tabel Peminjaman, pilih **Dipinjam** pada dropdown status.<br>2. Tunggu form terkirim.<br>3. Periksa status dan stok alat. | Jumlah pinjam 1; stok sebelum 3 | Status berubah menjadi `dipinjam` dan stok alat berkurang sejumlah detail. |  | NOT TESTED |
| ADMIN-PINJAM-004 | Menghapus transaksi Dipinjam dan memulihkan stok | Peminjaman uji berstatus `dipinjam`; catat stok. | 1. Klik **Hapus** pada transaksi.<br>2. Konfirmasi **OK**.<br>3. Periksa transaksi dan stok alat. | Peminjaman alat uji sebanyak 1 | Transaksi terhapus; controller mengembalikan jumlah ke stok untuk status `dipinjam`. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-PINJAM-005 | Menolak data Peminjaman dengan field wajib atau tanggal tidak valid | Admin membuka form tambah Peminjaman. | 1. Kirim tanpa peminjam atau tanpa item.<br>2. Ulangi dengan tanggal kembali sebelum tanggal pinjam.<br>3. Periksa hasil tiap pengiriman. | `user_id` kosong; `alat_id` kosong; `tgl_kembali_plan` sebelum `tgl_pinjam` | Validasi menolak request; transaksi tidak dibuat. |  | NOT TESTED |
| ADMIN-PINJAM-006 | Menolak jumlah Peminjaman melebihi stok | Alat uji memiliki stok 1. | 1. Ajukan dari form Admin jumlah 2.<br>2. Klik **Simpan Peminjaman**.<br>3. Periksa pesan, transaksi, dan stok. | Stok 1; jumlah 2 | Controller mengembalikan error stok tidak mencukupi dan rollback mencegah transaksi/detail tersimpan. |  | NOT TESTED |
| ADMIN-PINJAM-007 | Menolak nilai status di luar daftar | Token Admin API tersedia; ada peminjaman uji. | 1. Kirim `PUT /api/peminjaman/{id}` dengan payload update valid.<br>2. Kirim `PUT /admin/peminjaman/{id}/status` memakai status di luar daftar. | Status: `ditolak` | API validasi status menolak status tak terdaftar; endpoint web status juga menolak nilai di luar `diajukan`, `dipinjam`, `dikembalikan`, `telat`. |  | NOT TESTED |
| ADMIN-PINJAM-008 | Menolak update API setelah pengajuan diproses | Token Admin API tersedia; transaksi berstatus `dipinjam`. | 1. Kirim `PUT /api/peminjaman/{id}` dengan tanggal dan item valid.<br>2. Periksa status respons dan transaksi. | Status awal: `dipinjam` | API menolak perubahan dengan status 400 karena update hanya untuk status `diajukan`; data tetap. |  | NOT TESTED |
| ADMIN-PINJAM-009 | Membatalkan penghapusan Peminjaman | Transaksi uji tersedia. | 1. Klik **Hapus**.<br>2. Pilih **Cancel** pada dialog.<br>3. Periksa daftar dan stok. | Transaksi uji. | Transaksi tetap ada dan stok tidak berubah. |  | NOT TESTED |

### G. CRUD / Proses Pengembalian

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-KEMBALI-001 | Melihat antrean pengembalian Admin | Admin login; tersedia transaksi berstatus `dipinjam`/`telat`. | 1. Buka menu Pengembalian Admin.<br>2. Periksa data peminjam, alat, rencana kembali, kondisi/status. | Transaksi berstatus dipinjam. | Antrean hanya berisi transaksi berstatus `dipinjam` atau `telat`; data dipaginasi. |  | NOT TESTED |
| ADMIN-KEMBALI-002 | Memproses pengembalian tepat waktu di web Admin | Transaksi berstatus `dipinjam`; Admin login; relasi alat tersedia. | 1. Klik **Proses Pengembalian**.<br>2. Isi kondisi, denda kerusakan, dan denda wajib.<br>3. Kirim form. | Kondisi: baik<br>Denda kerusakan: 0<br>Denda: 0<br>Tanggal rencana: hari ini/masa depan | Catatan pengembalian dibuat, status peminjaman menjadi `dikembalikan`, stok pulih; total denda memasukkan denda kerusakan dan keterlambatan aktual. |  | NOT TESTED |
| ADMIN-KEMBALI-003 | Melihat detail dan riwayat Pengembalian melalui API | Token Admin API tersedia; pengembalian uji telah ada. | 1. Kirim `GET /api/pengembalian`.<br>2. Kirim `GET /api/pengembalian/{id}`.<br>3. Periksa relasi peminjaman, alat, dan petugas. | ID pengembalian uji | API mengembalikan daftar dan detail pengembalian terkait. |  | NOT TESTED |
| ADMIN-KEMBALI-004 | Mengoreksi Pengembalian melalui API | Token Admin API tersedia; pengembalian uji ada. | 1. Kirim `PUT /api/pengembalian/{id}` dengan kondisi valid dan denda baru.<br>2. Baca ulang detail. | Kondisi: `Rusak ringan`<br>Denda: 5000 | Kondisi dan denda diperbarui; field lain tidak diubah oleh controller update. |  | NOT TESTED |
| ADMIN-KEMBALI-005 | Menghapus Pengembalian melalui API | Token Admin API tersedia; data pengembalian uji; stok mencukupi untuk ditarik kembali. | 1. Catat status dan stok.<br>2. Kirim `DELETE /api/pengembalian/{id}`.<br>3. Periksa pengembalian, status peminjaman, dan stok. | Pengembalian untuk pinjaman 1 alat | Pengembalian dihapus, status peminjaman menjadi `dipinjam`, stok dikurangi sejumlah barang, dan respons sukses dikembalikan. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-KEMBALI-006 | Menolak data pengembalian tidak valid | Transaksi `dipinjam`; Admin membuka form proses web. | 1. Kosongkan kondisi atau denda wajib.<br>2. Kirim kondisi di luar pilihan atau denda negatif.<br>3. Periksa transaksi, catatan, dan stok. | Kondisi: `pecah total`<br>Denda: `-1` | Validasi menolak input; tidak ada catatan pengembalian dan stok/status tidak berubah. |  | NOT TESTED |
| ADMIN-KEMBALI-007 | Menolak pengembalian transaksi yang sudah selesai | Transaksi tidak berstatus `dipinjam` atau `telat`. | 1. Akses form atau kirim request proses pengembalian untuk ID tersebut.<br>2. Periksa respons dan data. | Status awal: `dikembalikan` | Web menampilkan pesan transaksi sudah dikembalikan; tidak membuat pengembalian tambahan. |  | NOT TESTED |
| ADMIN-KEMBALI-008 | Menolak koreksi API dengan nilai denda negatif | Token Admin API tersedia; pengembalian uji ada. | 1. Kirim `PUT /api/pengembalian/{id}` dengan kondisi dan denda `-100`.<br>2. Periksa respons/data. | `denda: -100` | Validasi API mengembalikan 422; catatan pengembalian tidak berubah. |  | NOT TESTED |
| ADMIN-KEMBALI-009 | Menolak penghapusan pengembalian bila stok tidak cukup | Token Admin API tersedia; data terisolasi; stok alat dibuat lebih kecil daripada jumlah pinjaman. | 1. Kirim `DELETE /api/pengembalian/{id}`.<br>2. Periksa status pengembalian, peminjaman, dan stok. | Jumlah pinjam 2; stok saat ini 1 | API mengembalikan 422 dan transaksi rollback agar pengembalian/status/stok tidak setengah berubah. |  | NOT TESTED |

### H. Log Aktivitas

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-LOG-001 | Melihat log terbaru pada Dashboard | Admin login; tersedia log aktivitas. | 1. Buka `/admin/dashboard`.<br>2. Periksa waktu, User, dan Aktivitas pada tabel. | Minimal satu log uji | Dashboard menampilkan paling banyak 10 log terbaru beserta user dan deskripsi. |  | NOT TESTED |
| ADMIN-LOG-002 | Melihat seluruh log melalui API Admin | Token Admin API tersedia; log uji tersedia. | 1. Kirim `GET /api/log-aktivitas`.<br>2. Periksa `total_data` dan daftar `data`. | Log uji tersimpan | Respons berisi semua log yang dikembalikan query, jumlah data, user dan aktivitas. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-LOG-003 | Menampilkan keadaan saat belum ada log | Database uji tidak memiliki log; Admin login. | 1. Buka dashboard Admin.<br>2. Panggil `GET /api/log-aktivitas` dengan token Admin. | Tabel `log_aktivitas` kosong | Dashboard menampilkan teks “Belum ada log aktivitas”; API mengembalikan jumlah 0 dan daftar kosong. |  | NOT TESTED |
| ADMIN-LOG-004 | Memverifikasi apakah pembuatan Alat tercatat otomatis | Admin login; gunakan Alat uji baru; Observer tidak ditemukan terdaftar. | 1. Catat jumlah log.<br>2. Buat Alat baru lewat web/API.<br>3. Periksa dashboard dan API log. | Nama alat unik: `Log QA` | **BELUM DAPAT DIPASTIKAN**: file observer ada tetapi pendaftarannya tidak ditemukan. Catat hasil aktual; jangan menetapkan PASS untuk pencatatan otomatis sebelum observer aktif. |  | NOT TESTED |

### I. Otorisasi Admin

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-AUTHZ-001 | Mengakses endpoint Admin menggunakan token Admin | Token Sanctum dari akun role Admin aktif. | 1. Kirim `GET /api/user` dengan header `Authorization: Bearer {token}`.<br>2. Periksa respons. | Token Admin valid | Endpoint Admin mengembalikan daftar pengguna. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| ADMIN-AUTHZ-002 | Menolak Petugas mengakses endpoint Admin | Token API Petugas aktif. | 1. Kirim `GET /api/user` memakai token Petugas.<br>2. Ulangi route web `/admin/users` dalam sesi Petugas. | Token/sesi Petugas | API menolak dengan 403; route web tidak menampilkan halaman Admin. |  | NOT TESTED |
| ADMIN-AUTHZ-003 | Menolak Peminjam mengakses endpoint Admin | Token/sesi Peminjam aktif. | 1. Kirim `GET /api/user` menggunakan token Peminjam.<br>2. Akses `/admin/users` melalui browser. | Token/sesi Peminjam | API ditolak 403 dan akses web Admin ditolak middleware. |  | NOT TESTED |
| ADMIN-AUTHZ-004 | Menolak pengguna tanpa autentikasi mengakses Admin | Tidak ada sesi/token. | 1. Buka `/admin/dashboard`.<br>2. Panggil `GET /api/user` tanpa Bearer token. | Tidak ada kredensial | Web meminta login; API menolak request tidak terautentikasi. |  | NOT TESTED |

## BAGIAN 3: TEST CASE ROLE PETUGAS

### A. Login

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-LOGIN-001 | Login Petugas dengan kredensial valid | Akun Petugas tersedia; belum login. | 1. Buka `/login`.<br>2. Isi Email dan Password.<br>3. Klik **Masuk**. | Email: `petugas.qa@example.test`<br>Password: `PetugasQa123` | Login berhasil dan diarahkan ke `/petugas/peminjaman`. |  | NOT TESTED |
| PETUGAS-LOGIN-002 | Mengakses daftar peminjaman Petugas | Petugas berhasil login. | 1. Buka `/petugas/peminjaman`.<br>2. Periksa data yang tampil. | Sesi Petugas aktif | Halaman pengajuan peminjaman Petugas tampil. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-LOGIN-003 | Menolak password Petugas yang salah | Akun Petugas tersedia. | 1. Buka `/login`.<br>2. Isi email Petugas dan password salah.<br>3. Klik **Masuk**. | Email: `petugas.qa@example.test`<br>Password: `Salah123` | Login ditolak dengan validasi kredensial salah. |  | NOT TESTED |
| PETUGAS-LOGIN-004 | Menolak email Petugas tidak terdaftar | Belum login. | 1. Kirim form login dengan email yang tidak terdaftar.<br>2. Periksa respons. | Email: `unknown.qa@example.test`<br>Password: `PetugasQa123` | Login ditolak; pengguna tetap di form login. |  | NOT TESTED |
| PETUGAS-LOGIN-005 | Menolak Petugas mengakses katalog Peminjam | Login sebagai Petugas. | 1. Akses `/peminjam/katalog`.<br>2. Periksa respons. | Sesi Petugas | Middleware Peminjam menolak akses. |  | NOT TESTED |

### B. Logout

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-LOGOUT-001 | Logout Petugas | Petugas telah login. | 1. Klik **Logout** pada navigasi.<br>2. Konfirmasi bila diminta.<br>3. Periksa halaman. | Sesi Petugas aktif | Sesi berakhir dan halaman login ditampilkan. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-LOGOUT-002 | Menolak akses halaman Petugas setelah logout | Petugas sudah logout. | 1. Akses `/petugas/peminjaman` langsung dari address bar.<br>2. Muat halaman. | Tidak ada sesi | Halaman terlindungi tidak dapat dibuka tanpa autentikasi. |  | NOT TESTED |

### C. Menyetujui Peminjaman

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-SETUJU-001 | Menyetujui pengajuan dan mengurangi stok | Petugas login; pengajuan berstatus `diajukan`; stok alat cukup. | 1. Buka daftar pengajuan.<br>2. Klik **Setujui** pada transaksi.<br>3. Konfirmasi **OK**.<br>4. Periksa status dan stok. | Jumlah 1; stok awal 3 | Peminjaman berubah menjadi `dipinjam`; stok berkurang 1; pesan sukses ditampilkan. |  | NOT TESTED |
| PETUGAS-SETUJU-002 | Menolak pengajuan yang belum diproses | Petugas login; tersedia pengajuan uji. | 1. Klik **Tolak** pada pengajuan.<br>2. Konfirmasi **OK**.<br>3. Muat ulang daftar dan periksa data. | Peminjaman status `diajukan` | Controller menghapus pengajuan dan menampilkan pesan berhasil ditolak; stok tidak dikurangi. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-SETUJU-003 | Menangani persetujuan ulang pada transaksi yang sudah diproses | Peminjaman uji sudah berstatus selain `diajukan`. | 1. Kirim ulang POST setujui untuk ID transaksi.<br>2. Periksa status dan stok. | Status awal: `dipinjam` | Alur bisnis seharusnya tidak memproses transaksi dua kali. **Source web tidak memeriksa status sebelum mengurangi stok**; catat jika stok terpotong ulang sebagai kegagalan. |  | NOT TESTED |
| PETUGAS-SETUJU-004 | Menangani stok yang tidak cukup saat disetujui | Pengajuan tersedia; stok telah berkurang hingga kurang dari jumlah yang diajukan. | 1. Klik **Setujui**.<br>2. Periksa flash message, status, dan stok. | Jumlah 2; stok tersisa 1 | Source web controller tidak memeriksa kecukupan stok dan mengurangi stok langsung. Uji ini mengungkap kemungkinan stok negatif; catat hasil tanpa menganggap penolakan otomatis terjadi. |  | NOT TESTED |
| PETUGAS-SETUJU-005 | Membatalkan persetujuan melalui dialog konfirmasi | Pengajuan masih `diajukan`. | 1. Klik **Setujui**.<br>2. Pilih **Cancel**.<br>3. Periksa status/stok. | Pengajuan uji | Pengiriman dibatalkan; status dan stok tetap. |  | NOT TESTED |
| PETUGAS-SETUJU-006 | Menolak Petugas API mengakses route Admin saja | Token Petugas API aktif. | 1. Panggil `GET /api/user` memakai token Petugas. | Token Petugas | Middleware menolak dengan HTTP 403. |  | NOT TESTED |

### D. Memantau dan Memproses Pengembalian

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-KEMBALI-001 | Melihat daftar alat yang sedang dipinjam | Petugas login; tersedia transaksi `dipinjam`. | 1. Buka `/petugas/pengembalian`.<br>2. Periksa peminjam, tanggal, alat dan status. | Transaksi berstatus `dipinjam` | Daftar hanya menampilkan transaksi berstatus `dipinjam` dengan detail peminjaman. |  | NOT TESTED |
| PETUGAS-KEMBALI-002 | Mencatat pengembalian tepat waktu melalui tombol Terima Kembali | Petugas login; peminjaman `dipinjam`, tanggal rencana hari ini/masa depan. | 1. Buka daftar Peminjaman.<br>2. Klik **Terima Kembali**.<br>3. Konfirmasi bila browser meminta.<br>4. Periksa transaksi, stok, dan riwayat. | Kondisi form: `Baik`; denda tersembunyi dikirim `0`; jumlah 1 | Catatan pengembalian dibuat, status menjadi `selesai`, stok bertambah sesuai jumlah, dan pesan sukses ditampilkan. |  | NOT TESTED |
| PETUGAS-KEMBALI-003 | Memastikan denda keterlambatan pada proses pengembalian | Petugas login; transaksi dipinjam dengan tanggal rencana lampau. | 1. Klik **Terima Kembali**.<br>2. Periksa nilai denda di catatan Pengembalian. | Terlambat 2 hari; denda kerusakan default 0 | Controller menghitung denda keterlambatan Rp5.000 per hari; total yang diharapkan Rp10.000, meskipun form mengirim field `denda=0` yang tidak digunakan controller. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-KEMBALI-004 | Menolak pengembalian ulang transaksi yang telah diproses | Transaksi sudah bukan `dipinjam`. | 1. Kirim ulang POST proses pengembalian untuk ID tersebut.<br>2. Periksa catatan, status dan stok. | Status awal: `selesai` atau status lain | Controller mengembalikan pesan status tidak valid; tidak boleh membuat catatan kedua atau menambah stok kembali. |  | NOT TESTED |
| PETUGAS-KEMBALI-005 | Membatalkan proses pengembalian dari konfirmasi browser | Transaksi masih `dipinjam`. | 1. Klik **Terima Kembali**.<br>2. Pilih **Cancel** pada konfirmasi.<br>3. Periksa status dan stok. | Peminjaman uji | Pengembalian tidak diproses; status dan stok tetap. |  | NOT TESTED |

### E. Mencetak Laporan

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-LAPORAN-001 | Melihat rekap peminjaman | Petugas login; transaksi tersedia. | 1. Buka `/petugas/laporan`.<br>2. Periksa kolom peminjam, tanggal pinjam, rencana kembali, alat, status. | Minimal satu transaksi uji | Rekap menampilkan data peminjaman beserta item alat dan status; daftar dipaginasi. |  | NOT TESTED |
| PETUGAS-LAPORAN-002 | Membuka dialog cetak browser | Halaman laporan tampil. | 1. Klik **Cetak Laporan**.<br>2. Periksa dialog cetak browser. | Browser dengan dukungan print | Pemanggilan `window.print()` membuka dialog cetak; format PDF/Excel tidak dijanjikan oleh implementasi. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-LAPORAN-003 | Menampilkan laporan saat tidak ada transaksi | Database uji tidak memiliki peminjaman; Petugas login. | 1. Buka `/petugas/laporan`.<br>2. Periksa tabel. | Tidak ada data peminjaman | Tabel menampilkan “Belum ada data untuk laporan.” dan halaman tidak error. |  | NOT TESTED |
| PETUGAS-LAPORAN-004 | Menolak Peminjam mengakses laporan Petugas | Login sebagai Peminjam. | 1. Akses `/petugas/laporan`.<br>2. Periksa respons. | Sesi Peminjam | Akses ditolak oleh middleware role. |  | NOT TESTED |

### F. Otorisasi Petugas

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-AUTHZ-001 | Mengakses endpoint persetujuan API sebagai Petugas | Token API role Petugas; pengajuan tersedia. | 1. Kirim `POST /api/peminjaman/{id}/approve` memakai token Petugas.<br>2. Periksa respons. | Peminjaman `diajukan`; stok cukup | API menerima request Petugas, mengubah status dan mengurangi stok. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PETUGAS-AUTHZ-002 | Menolak Peminjam menyetujui Peminjaman | Token/sesi Peminjam aktif. | 1. Akses route web setujui atau panggil API approve dengan token Peminjam. | Peminjam tidak berwenang | Web menolak route Petugas; API mengembalikan 403. |  | NOT TESTED |
| PETUGAS-AUTHZ-003 | Menolak akses Petugas tanpa login | Tidak ada sesi/token. | 1. Akses `/petugas/peminjaman`.<br>2. Panggil `POST /api/peminjaman/{id}/approve` tanpa token. | Tanpa autentikasi | Web meminta login; API menolak request. |  | NOT TESTED |
| PETUGAS-AUTHZ-004 | Menolak Admin pada endpoint API khusus Petugas | Token API Admin aktif. | 1. Panggil `POST /api/peminjaman/{id}/approve` memakai token Admin. | Token Admin | API Petugas menggunakan middleware role Petugas saja dan menolak Admin dengan 403. Catatan: route web Petugas menerima Admin, sehingga hasil web dan API berbeda. |  | NOT TESTED |

## BAGIAN 4: TEST CASE ROLE PEMINJAM

### A. Login

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PEMINJAM-LOGIN-001 | Login Peminjam dengan kredensial valid | Akun Peminjam tersedia; belum login. | 1. Buka `/login`.<br>2. Masukkan Email dan Password.<br>3. Klik **Masuk**. | Email: `peminjam.qa@example.test`<br>Password: `PeminjamQa123` | Login berhasil dan diarahkan ke `/peminjam/katalog`. |  | NOT TESTED |
| PEMINJAM-LOGIN-002 | Membuka katalog setelah login | Peminjam berhasil login. | 1. Periksa halaman tujuan.<br>2. Buka **Riwayat Pinjam** dan kembali ke katalog. | Sesi Peminjam aktif | Katalog dan riwayat Peminjam dapat diakses. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PEMINJAM-LOGIN-003 | Menolak password Peminjam salah | Akun Peminjam tersedia. | 1. Isi email terdaftar dengan password salah.<br>2. Klik **Masuk**. | Email: `peminjam.qa@example.test`<br>Password: `Salah123` | Login ditolak dengan pesan kredensial salah. |  | NOT TESTED |
| PEMINJAM-LOGIN-004 | Menolak email yang tidak terdaftar | Belum login. | 1. Isi email yang tidak terdaftar dan password.<br>2. Klik **Masuk**. | Email: `tidak-ada.qa@example.test` | Login ditolak; halaman login tetap ditampilkan. |  | NOT TESTED |
| PEMINJAM-LOGIN-005 | Menolak format email atau input kosong | Belum login. | 1. Kirim form dengan kedua kolom kosong.<br>2. Ulangi dengan email tidak valid. | Email kosong / `salah-format`<br>Password kosong / `PeminjamQa123` | Validasi menolak kolom wajib atau format email tidak sesuai. |  | NOT TESTED |

### B. Logout

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PEMINJAM-LOGOUT-001 | Logout Peminjam | Peminjam login pada katalog. | 1. Klik **Logout**.<br>2. Konfirmasi bila diminta.<br>3. Periksa halaman tujuan. | Sesi Peminjam aktif | Sesi diinvalidasi dan browser kembali ke halaman login. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PEMINJAM-LOGOUT-002 | Menolak akses katalog setelah logout | Peminjam sudah logout. | 1. Akses `/peminjam/katalog` langsung.<br>2. Muat halaman. | Sesi telah diinvalidasi | Katalog terlindungi tidak dapat dibuka sebelum login. |  | NOT TESTED |

### C. Melihat Daftar Alat

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PEMINJAM-ALAT-001 | Melihat alat yang stoknya tersedia | Peminjam login; ada alat stok lebih dari nol. | 1. Buka katalog alat.<br>2. Periksa nama alat, kategori, dan stok. | Alat stok 2, kondisi apa pun | Katalog menampilkan alat yang stoknya lebih dari nol dan relasi kategori. |  | NOT TESTED |
| PEMINJAM-ALAT-002 | Melihat katalog API yang tersedia | Token API Peminjam aktif. | 1. Kirim `GET /api/katalog` memakai token Peminjam.<br>2. Periksa daftar. | Ada alat stok positif | API katalog mengembalikan data alat yang tersedia menurut scope `tersedia` dan kategori. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PEMINJAM-ALAT-003 | Menyembunyikan alat dengan stok nol pada web | Peminjam login; alat uji memiliki stok 0. | 1. Muat ulang katalog.<br>2. Cari nama alat stok nol pada tabel. | Alat stok 0 | Alat stok nol tidak ditampilkan di katalog web. |  | NOT TESTED |
| PEMINJAM-ALAT-004 | Menampilkan kondisi katalog kosong | Peminjam login; tidak ada alat dengan stok lebih dari nol. | 1. Buka katalog web.<br>2. Periksa tabel. | Semua stok alat 0 pada database uji | Tabel menampilkan “Tidak ada alat yang tersedia saat ini.” tanpa error. |  | NOT TESTED |

### D. Mengajukan Peminjaman

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PEMINJAM-PINJAM-001 | Mengajukan peminjaman melalui katalog web | Peminjam login; alat tersedia dan stok cukup. | 1. Pilih tanggal rencana kembali setelah hari ini.<br>2. Centang satu alat.<br>3. Isi jumlah yang tidak melebihi stok.<br>4. Klik **Ajukan Peminjaman**.<br>5. Periksa riwayat. | Rencana kembali: besok<br>Alat: alat stok 3<br>Jumlah: 1 | Header dan detail tersimpan dengan status `diajukan`; diarahkan ke riwayat dan pesan sukses ditampilkan. |  | NOT TESTED |
| PEMINJAM-PINJAM-002 | Mengajukan peminjaman melalui API | Token API Peminjam aktif; alat stok cukup. | 1. Kirim `POST /api/peminjaman` dengan tanggal format ISO dan satu item.<br>2. Periksa respons serta riwayat. | `tgl_kembali_plan`: besok `YYYY-MM-DD`<br>`items`: `[{alat_id: ID, jumlah: 1}]` | API mengembalikan 201, status `diajukan`, detail tersimpan dan pesan menunggu persetujuan. |  | NOT TESTED |
| PEMINJAM-PINJAM-003 | Melihat riwayat peminjaman milik sendiri | Peminjam telah memiliki transaksi uji. | 1. Buka **Riwayat Pinjam** pada web.<br>2. Panggil `GET /api/riwayat-pinjam` memakai token Peminjam.<br>3. Periksa nama alat, jumlah dan status. | Transaksi milik akun yang login | Web/API menampilkan riwayat akun yang sedang login beserta detail dan status. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PEMINJAM-PINJAM-004 | Menolak form web tanpa tanggal kembali | Peminjam login dan katalog memuat alat. | 1. Pilih alat dan jumlah.<br>2. Kosongkan tanggal kembali.<br>3. Klik **Ajukan Peminjaman**. | `tgl_kembali_plan`: kosong | Browser/controller menolak tanggal wajib; pengajuan tidak tersimpan. |  | NOT TESTED |
| PEMINJAM-PINJAM-005 | Menolak tanggal kembali hari ini atau masa lalu | Peminjam login. | 1. Isi tanggal kembali hari ini, kirim.<br>2. Ulangi dengan tanggal kemarin. | Tanggal hari ini dan kemarin | Validasi web `after:today` menolak kedua tanggal; data tidak dibuat. |  | NOT TESTED |
| PEMINJAM-PINJAM-006 | Menolak daftar item kosong atau format item tidak valid di API | Token Peminjam API aktif. | 1. Kirim `POST /api/peminjaman` tanpa items.<br>2. Ulangi dengan `items` bukan array atau jumlah 0. | `items: []`; lalu `jumlah: 0` | Validasi API menolak request; tidak ada header/detail tersimpan. |  | NOT TESTED |
| PEMINJAM-PINJAM-007 | Menolak ID alat yang tidak tersedia di API | Token API Peminjam aktif. | 1. Kirim peminjaman dengan ID alat yang tidak ada.<br>2. Periksa status dan database. | `alat_id: 999999`, `jumlah: 1` | Validasi keberadaan alat menolak request; peminjaman tidak dibuat. |  | NOT TESTED |
| PEMINJAM-PINJAM-008 | Menguji jumlah melebihi stok pada API | Token Peminjam API; alat memiliki stok 1. | 1. Kirim pengajuan dengan jumlah 2.<br>2. Periksa kode respons, data dan stok. | Stok: 1; jumlah: 2 | API memeriksa stok dan mengembalikan 422; transaksi rollback dan stok tidak berubah. |  | NOT TESTED |
| PEMINJAM-PINJAM-009 | Menguji validasi stok pada form web | Peminjam login; alat ada di katalog dengan stok 1. | 1. Ubah nilai form jumlah menjadi 2 menggunakan alat browser/request.<br>2. Kirim form web.<br>3. Periksa riwayat, detail dan stok. | ID alat valid; jumlah 2; stok 1 | **Source web tidak membandingkan jumlah dengan stok**. Catat hasil aktual; jika tersimpan, tandai sebagai temuan validasi web, jangan laporkan sebagai perilaku penolakan yang sudah ada. |  | NOT TESTED |
| PEMINJAM-PINJAM-010 | Menolak peminjam melihat atau mengubah transaksi milik orang lain melalui API | Dua akun Peminjam dan transaksi milik akun kedua tersedia. | 1. Dengan token akun pertama, panggil `GET /api/peminjaman/{id}` milik akun kedua.<br>2. Uji `PUT` dan `DELETE` pada transaksi tersebut. | Token user A; ID pinjaman user B | API mengembalikan 403 untuk show/update/delete; data milik user B tidak berubah. |  | NOT TESTED |
| PEMINJAM-PINJAM-011 | Menolak pembatalan API setelah transaksi diproses | Token Peminjam pemilik transaksi; status transaksi bukan `diajukan`. | 1. Kirim `DELETE /api/peminjaman/{id}`.<br>2. Periksa status respons dan data. | Status: `dipinjam` | API mengembalikan 400 “tidak dapat dibatalkan”; transaksi tidak dihapus. |  | NOT TESTED |

### E. Mengembalikan Alat — BELUM DAPAT DIVERIFIKASI

Source code tidak menyediakan aksi/route pengembalian oleh role Peminjam pada web maupun API. Endpoint API pembuatan pengembalian berada di middleware Petugas; halaman web pengembalian dan tombol proses yang ditemukan berada pada panel Petugas/Admin. Dengan demikian tidak ada test case positif yang dapat dijalankan sebagai Peminjam tanpa menambahkan atau mengasumsikan fitur yang belum ada.

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PEMINJAM-KEMBALI-001 | Memverifikasi ketersediaan fitur pengembalian untuk Peminjam | Peminjam login; tersedia pinjaman berstatus `dipinjam`. | 1. Buka katalog dan riwayat Peminjam.<br>2. Periksa tombol/form pengembalian.<br>3. Periksa route web yang tersedia dan route API pengembalian dengan token Peminjam. | Pinjaman milik Peminjam berstatus `dipinjam` | Tandai **BELUM DAPAT DIVERIFIKASI / fitur tidak ditemukan** bila tidak ada aksi dan route. Pengembalian harus diproses Petugas/Admin sesuai implementasi saat ini. |  | NOT TESTED |

### F. Otorisasi Peminjam

#### TEST CASE POSITIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PEMINJAM-AUTHZ-001 | Membaca katalog dan riwayat dengan token Peminjam | Token Sanctum role Peminjam aktif. | 1. Panggil `GET /api/katalog`.<br>2. Panggil `GET /api/riwayat-pinjam`. | Bearer token Peminjam | Kedua route mengizinkan Peminjam dan riwayat hanya berisi data akun tersebut. |  | NOT TESTED |

#### TEST CASE NEGATIF

| ID | TITLE | PRECONDITION | STEPS | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS |
|---|---|---|---|---|---|---|---|
| PEMINJAM-AUTHZ-002 | Menolak Peminjam membuka halaman Admin | Sesi Peminjam aktif. | 1. Akses `/admin/dashboard` dan `/admin/alat`.<br>2. Periksa respons. | Sesi Peminjam | Middleware Admin menolak akses. |  | NOT TESTED |
| PEMINJAM-AUTHZ-003 | Menolak Peminjam membuka halaman Petugas | Sesi Peminjam aktif. | 1. Akses `/petugas/peminjaman` dan `/petugas/laporan`. | Sesi Peminjam | Middleware Petugas menolak akses. |  | NOT TESTED |
| PEMINJAM-AUTHZ-004 | Menolak token tidak valid pada API privat | Request tanpa token atau token acak. | 1. Panggil `GET /api/me` dan `GET /api/katalog` tanpa token.<br>2. Ulangi memakai token acak. | Token kosong / `invalid-token` | Sanctum menolak request tanpa autentikasi yang sah. |  | NOT TESTED |

## BAGIAN 5: REKAPITULASI TEST CASE

Angka berikut menghitung seluruh baris test case di atas, termasuk skenario verifikasi fitur yang belum tersedia. Pengembalian Peminjam dihitung sebagai skenario negatif/verifikasi ketersediaan, bukan uji keberhasilan proses pengembalian.

| Role | Nama fitur | Jumlah test case positif | Jumlah test case negatif | Total test case |
|---|---|---:|---:|---:|
| Admin | Login | 2 | 5 | 7 |
| Admin | Logout | 1 | 1 | 2 |
| Admin | CRUD User | 4 | 5 | 9 |
| Admin | CRUD Alat | 4 | 4 | 8 |
| Admin | CRUD Kategori | 4 | 5 | 9 |
| Admin | CRUD Data Peminjaman (operasi tersedia) | 4 | 5 | 9 |
| Admin | CRUD / Proses Pengembalian (Web & API) | 5 | 4 | 9 |
| Admin | Log Aktivitas | 2 | 2 | 4 |
| Admin | Otorisasi | 1 | 3 | 4 |
| **Subtotal Admin** |  | **27** | **34** | **61** |
| Petugas | Login | 2 | 3 | 5 |
| Petugas | Logout | 1 | 1 | 2 |
| Petugas | Menyetujui / Menolak Peminjaman | 2 | 4 | 6 |
| Petugas | Memantau / Memproses Pengembalian | 3 | 2 | 5 |
| Petugas | Mencetak Laporan | 2 | 2 | 4 |
| Petugas | Otorisasi | 1 | 3 | 4 |
| **Subtotal Petugas** |  | **11** | **15** | **26** |
| Peminjam | Login | 2 | 3 | 5 |
| Peminjam | Logout | 1 | 1 | 2 |
| Peminjam | Melihat Daftar Alat | 2 | 2 | 4 |
| Peminjam | Mengajukan Peminjaman | 3 | 8 | 11 |
| Peminjam | Mengembalikan Alat (BELUM DAPAT DIVERIFIKASI) | 0 | 1 | 1 |
| Peminjam | Otorisasi | 1 | 3 | 4 |
| **Subtotal Peminjam** |  | **9** | **18** | **27** |
| **TOTAL** |  | **47** | **67** | **114** |

### Catatan akhir QA

- Semua `ACTUAL RESULT` belum diisi karena pengujian aktual belum dijalankan; ubah `STATUS` menjadi `PASS` atau `FAIL` hanya setelah menjalankan langkah pada lingkungan uji.
- Jalankan terlebih dahulu test case yang tidak menghapus data; gunakan database uji untuk skenario delete, cascade, stok, dan perubahan status.
- Jika test gagal pada pemanggilan relasi detail, simpan bukti respons/exception dan cocokkan dengan temuan nama relasi `detailPinjam` / `detailPinjams` pada hasil analisis.
- Pastikan keputusan produk untuk alur pengembalian Peminjam dan aturan validasi stok pada web diselesaikan sebelum menyatakan seluruh kebutuhan bisnis terpenuhi.
