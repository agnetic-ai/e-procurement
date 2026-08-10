# End-to-End Test Cases

Suite ini memeriksa alur aplikasi dari browser sampai controller dan database. Seluruh test default bersifat non-destruktif: tidak membuat, menyetujui, membayar, atau menghapus transaksi.

## Prasyarat

- Aplikasi dan MySQL aktif dengan database uji yang representatif.
- Akun admin uji aktif dan tidak dipakai bersamaan oleh proses lain.
- `E2E_BASE_URL` mengarah ke deployment yang diuji dan diakhiri `/`.
- Gunakan database staging/disposable, bukan production.

Konfigurasi aplikasi dapat dioverride tanpa mengubah source melalui `EPROC_BASE_URL`, `EPROC_DB_HOST`, `EPROC_DB_PORT`, `EPROC_DB_USER`, `EPROC_DB_PASS`, dan `EPROC_DB_NAME`. Nilai default aplikasi tetap dipakai jika variable tersebut tidak diset.

Untuk PHP built-in server, gunakan router test agar URL MVC tetap berfungsi:

```powershell
$env:EPROC_BASE_URL = 'http://127.0.0.1:8765/'
php -S 127.0.0.1:8765 -t . tests/e2e/router.php
```

Pada Laragon, runner berikut dapat menyalakan server PHP dan MySQL port `3307`, mengimpor dump ke database terisolasi `eprocurement_e2e`, menjalankan test, lalu menghentikan kedua server:

```powershell
npm run test:e2e:local
```

## Matriks Test

| ID | Area | Skenario | Hasil yang diharapkan |
| --- | --- | --- | --- |
| AUTH-001 | Login | Buka halaman login | Form username/password dan tombol masuk tampil; password kosong |
| AUTH-002 | Login | Login dengan username acak dan password salah | Tetap di login; pesan generik tampil; detail SQL/PHP tidak bocor |
| AUTH-003 | Login | Login dengan akun admin valid | Berpindah ke dashboard dan identitas pengguna tampil |
| AUTH-004 | Logout | Logout lalu akses dashboard | Session tidak berlaku; kembali ke login dan ada notifikasi logout |
| AUTH-005 | Guard | Akses tiap modul tanpa session | Semua halaman protected diarahkan ke login |
| AUTH-006 | Email approval | Akses endpoint token invalid tanpa login | Hanya `ApproveByEmail` boleh publik dan menolak token invalid dengan aman |
| PAGE-001 | Smoke | Buka seluruh halaman utama sebagai admin | HTTP sukses, heading benar, tanpa diagnostic PHP, error JS, atau resource internal 4xx/5xx |
| VEND-001 | Vendor list | Buka dan cari vendor | AJAX sukses; pagination tampil; vendor dari response dapat dicari di DataTables |
| VEND-002 | Vendor form | Submit form kosong | Browser menahan submit pada field nama yang wajib |
| VEND-003 | Vendor lifecycle | Buat, cari, update, lalu coba email duplikat | Vendor tersimpan dan dapat dicari; update terbaca; duplikat ditolak |
| API-001 | List APIs | Panggil seluruh endpoint daftar | HTTP 200, JSON valid, envelope `{status,message,result}`, result berupa array |
| API-002 | Vendor create | Kirim payload kosong | HTTP 400 tanpa perubahan data |
| API-003 | Vendor update | Kirim payload kosong | HTTP 4xx dan JSON valid tanpa perubahan data |
| SEC-001 | API auth | Panggil API protected tanpa session | HTTP 401 JSON, bukan redirect/HTML login |
| SEC-002 | Routing | Kirim markup pada route 404 | HTTP 404 dan markup tidak dieksekusi |

## Menjalankan

```powershell
npm install
npx playwright install chromium

$env:E2E_BASE_URL = 'http://localhost/e-procurement/'
$env:E2E_USERNAME = 'admin'
$env:E2E_PASSWORD = 'admin123'
npm run test:e2e
```

Untuk melihat browser saat test berjalan:

```powershell
npm run test:e2e:headed
```

Jika ada kegagalan, screenshot, video, dan trace disimpan di `test-results/`. Laporan HTML dibuat di `playwright-report/` dan dapat dibuka melalui `npm run test:e2e:report`.

## Catatan keamanan data

Test lifecycle vendor hanya aktif jika `E2E_ALLOW_MUTATION=true`. Runner lokal mengaktifkannya karena database `eprocurement_e2e` selalu dibuat ulang dari dump sebelum test. Jangan set variable tersebut ketika target mengarah ke production.
