# E2E Test Results

Run terakhir: 10 Agustus 2026, menggunakan Chromium dan database terisolasi `eprocurement_e2e` yang dibuat ulang dari `eprocurement_db (17).sql`.

## Ringkasan

- Total: 55 test
- Lulus: 49
- Gagal karena defect aplikasi: 6
- Durasi: 1,7 menit
- Seluruh 92 file PHP juga lulus pemeriksaan sintaks `php -l`.

## Temuan

### 1. Critical — Reflected XSS pada halaman 404

Nilai route ditampilkan langsung sebagai HTML oleh `show404()`. Payload `<script>` benar-benar dieksekusi oleh browser walaupun response berstatus 404.

Lokasi: `index.php`, fungsi `show404()`.

Rekomendasi: encode `$url` menggunakan `htmlspecialchars($url, ENT_QUOTES, 'UTF-8')` sebelum ditampilkan.

### 2. High — Update vendor kosong dianggap berhasil

`POST vendors/SubmitUpdateVendor` dengan `{}` mengembalikan HTTP 201. Log server juga mencatat beberapa `Undefined array key`; query tidak mengubah row karena `vendorCode` kosong, tetapi API tetap melaporkan sukses.

Lokasi: `app/models/VendorModel.php`, fungsi `UpdateVendor()`.

Rekomendasi: validasi semua field wajib, validasi email dan foreign key, lalu pastikan `rowCount()` sesuai sebelum commit dan response sukses.

### 3. High — Approval melalui email tidak cocok dengan schema database

Endpoint token invalid seharusnya menampilkan pesan token tidak valid, tetapi menghasilkan error SQL karena controller membaca `purchase_request_approvals.approval_token`, sedangkan kolom tersebut tidak ada pada dump database yang diuji.

Lokasi: `app/controllers/OrdersApprovalController.php`, fungsi `ApproveByEmail()`; schema `eprocurement_db (17).sql`.

Rekomendasi: tambahkan migration/dump untuk `approval_token` beserta unique index dan expiry, atau sesuaikan implementasi controller dengan schema yang resmi.

### 4. Medium — API protected mengembalikan redirect HTML

Request tanpa session ke `vendors/GetVendorList` mengembalikan HTTP 302 menuju login, bukan HTTP 401 JSON. Client AJAX dapat menerima HTML ketika mengharapkan envelope JSON.

Lokasi: `app/core/Controller.php`, fungsi `checkLogin()`.

Rekomendasi: bedakan request halaman dan API; endpoint API sebaiknya memakai `ResponseHelper::unauthorized()`.

### 5. Medium — Notifikasi logout hilang

Session berhasil dibatalkan dan route protected tidak dapat diakses lagi, tetapi pesan “You have been logged out successfully” tidak tampil. Controller menghancurkan session sebelum menyimpan flash message.

Lokasi: `app/controllers/AuthController.php`, fungsi `logout()`.

Rekomendasi: mulai session baru setelah destroy sebelum menyimpan flash, atau simpan flash menggunakan mekanisme yang tetap tersedia setelah rotasi session.

### 6. Medium — Dashboard error jika CDN Chart.js gagal

Dashboard menghasilkan `ReferenceError: Chart is not defined` ketika resource Chart.js dari CDN tidak tersedia. Halaman tidak memiliki fallback atau guard sebelum memanggil `new Chart()`.

Lokasi: `app/views/dashboard/index.php`.

Rekomendasi: sediakan Chart.js secara lokal atau periksa `typeof Chart !== 'undefined'` dan tampilkan fallback yang ramah pengguna.

## Yang sudah tervalidasi

- Login valid dan invalid.
- Session benar-benar tidak berlaku setelah logout.
- Guard anonim pada 14 modul utama.
- Kontrak JSON 11 endpoint daftar.
- Render 18 halaman utama tanpa error aplikasi, selain dashboard yang tercatat di atas.
- Daftar dan pencarian vendor melalui DataTables.
- Lifecycle vendor pada database disposable: create, cari, update, dan penolakan email duplikat.
- Validasi payload kosong pada create vendor.

Artefak screenshot, video, trace, dan laporan HTML tersedia di `test-results/` serta `playwright-report/` setelah menjalankan suite.
