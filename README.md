# e-Procurement System

Sistem manajemen vendor e-procurement berbasis PHP MVC dengan antarmuka Bootstrap dan jQuery.

## 📋 Daftar Isi

- [Requirement](#-requirement)
- [Instalasi](#-instalasi)
- [Konfigurasi Database](#-konfigurasi-database)
- [Setup Node Modules](#-setup-node-modules)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Penggunaan Sistem](#-penggunaan-sistem)
- [Struktur Folder](#-struktur-folder)
- [Fitur Utama](#-fitur-utama)

---

## 🔧 Requirement

Sebelum memulai, pastikan Anda memiliki:

- **PHP** 7.4 atau lebih tinggi
- **MySQL** 5.7 atau lebih tinggi
- **Node.js & npm** (untuk install dependencies frontend)
- **XAMPP/LAMPP/MAMP** (atau server web lainnya) - opsional
- **Composer** composer require phpmailer/phpmailer

---

## 📦 Instalasi

### 1. Clone atau Download Project

```bash
# Jika menggunakan git
git clone <repository-url>
cd e-procurement

# Atau ekstrak file ZIP ke folder
# C:\xampp\htdocs\e-procurement (untuk Windows XAMPP)
```

### 2. Pastikan Struktur Folder Benar

```
e-procurement/
├── app/
│   ├── config/
│   ├── controllers/
│   ├── core/
│   ├── helpers/
│   ├── models/
│   ├── service/
│   └── views/
├── public/
│   ├── voler/          # Template UI
│   ├── node_modules/   # Akan dibuat saat npm install
│   └── package.json
├── index.php           # Entry point aplikasi
├── master-vendor.sql   # Database schema
└── README.md
```

---

## 🗄️ Konfigurasi Database

### 1. Import Database Schema

#### Opsi A: Menggunakan phpMyAdmin (XAMPP)

1. Buka `http://localhost/phpmyadmin`
2. Buat database baru: `eprocurement_db`
3. Pilih tab "Import"
4. Upload file `master-vendor.sql`
5. Klik "Go"

#### Opsi B: Menggunakan Command Line

```bash
# Windows
mysql -u root -p eprocurement_db < master-vendor.sql

# Linux/Mac
mysql -u root -p eprocurement_db < master-vendor.sql
```

### 2. Update Konfigurasi Database

Edit file `app/config/constants.php`:

```php
<?php
// Sesuaikan dengan setup MySQL Anda
define('DB_HOST', 'localhost');      // Host MySQL
define('DB_USER', 'root');           // Username MySQL
define('DB_PASS', '');               // Password MySQL (kosong jika tidak ada)
define('DB_NAME', 'eprocurement_db'); // Nama database
```

### 3. Verifikasi Koneksi Database

Buka `http://localhost/e-procurement/` di browser. Jika ada error database, periksa:

- Username dan password MySQL
- Database `eprocurement_db` sudah dibuat
- MySQL service sudah running

---

## 📥 Setup Node Modules

Project ini menggunakan 4 npm packages untuk frontend:

| Package              | Versi  | Fungsi                                 |
| -------------------- | ------ | -------------------------------------- |
| `jquery`             | ^3.7.1 | Manipulasi DOM & AJAX                  |
| `bootstrap`          | ^5.3.8 | CSS Framework & Components             |
| `datatables.net`     | ^2.3.5 | Data table dengan sorting & pagination |
| `datatables.net-bs5` | ^2.3.5 | Bootstrap 5 styling untuk DataTables   |

### Instalasi Node Modules

```bash
# Navigasi ke folder public
cd public

# Install semua dependencies (auto-generate folder node_modules)
npm install

# Atau install package spesifik
npm install jquery bootstrap datatables.net datatables.net-bs5
```

**Output yang diharapkan:**

```
added 150 packages in 2m
```

Folder `public/node_modules/` akan berisi semua library yang dibutuhkan.

### Verifikasi Instalasi

```bash
# Di dalam folder public, cek versi
npm list

# Output:
# e-procurement@1.0.0
# ├── bootstrap@5.3.8
# ├── datatables.net@2.3.5
# ├── datatables.net-bs5@2.3.5
# └── jquery@3.7.1
```

---

## 🚀 Menjalankan Aplikasi

### Setup XAMPP (Windows)

1. **Letakkan folder di htdocs**

   ```
   C:\xampp\htdocs\e-procurement
   ```

2. **Jalankan XAMPP Control Panel**
   - Start **Apache**
   - Start **MySQL**

3. **Akses di Browser**
   ```
   http://localhost/e-procurement/
   ```

### Setup LAMPP (Linux)

```bash
# Copy ke folder apache
sudo cp -r e-procurement /var/www/html/

# Jalankan Apache & MySQL
sudo service apache2 start
sudo service mysql start

# Akses aplikasi
http://localhost/e-procurement/
```

### Setup Lokal dengan PHP Built-in Server

```bash
# Navigasi ke root project
cd e-procurement

# Jalankan server development (tidak perlu XAMPP)
php -S localhost:8000

# Buka di browser
http://localhost:8000/
```

---

## 📖 Penggunaan Sistem

### Login ke Sistem

1. **Default User** (dari database):
   - Username: `admin`
   - Password: `password123` (atau sesuai data di `master-vendor.sql`)

2. **Proses Login**:
   - Masukkan username dan password
   - Klik tombol "Login"
   - Jika berhasil → redirect ke Dashboard
   - Jika gagal → tampil pesan error (max 5 percobaan = 15 menit lock)

### Dashboard

Setelah login, Anda akan melihat:

- **Sidebar**: Menu navigasi (Vendor, Procurement, Reports)
- **Topbar**: User info, notification, logout
- **Main Content**: Halaman dashboard dengan informasi ringkas

### Manajemen Vendor

#### 1. Lihat Daftar Vendor

```
Dashboard → Menu Vendor → List Vendors
```

- Tampil tabel dengan sorting dan search
- Gunakan DataTables untuk pagination
- Klik vendor untuk melihat detail

#### 2. Tambah Vendor Baru

```
Dashboard → Menu Vendor → Add New Vendor
```

- Isi form dengan data vendor:
  - Nama Vendor
  - Email & Phone
  - Kota (pilih dari dropdown)
  - Tipe Bisnis
  - Nomor Pajak (NPWP)
  - Payment Terms
  - Website & Alamat
- Klik tombol "Submit"

#### 3. Edit Vendor

- Dari halaman List Vendor → klik vendor → Edit
- Update data yang diperlukan
- Klik "Save Changes"

#### 4. Lihat Detail Vendor

- Dari halaman List Vendor → klik nama vendor
- Tampil informasi lengkap:
  - Data dasar vendor
  - Kontak utama
  - Data bank
  - Dokumen/attachment
  - History perubahan

---

## 🏗️ Struktur Folder

```
e-procurement/
│
├── app/                          # Backend (PHP MVC)
│   ├── config/
│   │   ├── autoload.php         # Autoload classes
│   │   ├── constants.php        # Konfigurasi aplikasi & database
│   │   └── database.php         # Database config
│   │
│   ├── core/
│   │   ├── Controller.php       # Base class untuk semua controller
│   │   ├── Database.php         # Singleton database connection
│   │   └── Session.php          # Session management
│   │
│   ├── models/
│   │   ├── UserModel.php        # User authentication
│   │   ├── VendorModel.php      # Vendor CRUD operations
│   │   ├── CitiesModel.php      # Master cities
│   │   └── BusinessTypeModel.php # Master business types
│   │
│   ├── controllers/
│   │   ├── AuthController.php   # Login & logout
│   │   ├── DashboardController.php
│   │   └── VendorController.php
│   │
│   ├── views/
│   │   ├── auth/
│   │   │   └── login.php        # Login form
│   │   ├── dashboard/
│   │   │   └── index.php        # Dashboard page
│   │   ├── vendor/
│   │   │   ├── index.php        # Vendor list
│   │   │   ├── Create.php       # Add vendor form
│   │   │   └── VendorDetail.php # Vendor detail view
│   │   └── layouts/
│   │       ├── header.php       # HTML head & navbar
│   │       ├── sidebar.php      # Left sidebar menu
│   │       ├── footer.php       # Footer
│   │       └── home.php         # Main layout wrapper
│   │
│   ├── helpers/
│   │   └── ResponseHelper.php   # Standardized JSON response
│   │
│   └── service/
│       ├── vendor/
│       │   ├── CreateVendor.js  # Frontend vendor creation logic
│       │   └── main.js
│       └── dashboard/
│
├── public/                        # Frontend (Static Assets)
│   ├── package.json             # NPM dependencies
│   ├── node_modules/            # Install location untuk npm packages
│   │   ├── jquery/
│   │   ├── bootstrap/
│   │   ├── datatables.net/
│   │   └── datatables.net-bs5/
│   │
│   ├── voler/                   # Bootstrap template theme
│   │   ├── assets/
│   │   │   ├── css/            # Bootstrap & custom styles
│   │   │   ├── js/             # Template JS files
│   │   │   └── images/         # Icons & images
│   │   └── layouts/            # Voler HTML layouts
│   │
│   └── assets/                  # Custom assets
│       ├── css/
│       ├── js/
│       └── images/
│
├── index.php                     # Application entry point (router)
├── master-vendor.sql            # Database schema & sample data
├── README.md                     # Documentation (file ini)
└── LICENSE                       # License information
```

---

## ⚡ Fitur Utama

### 1. Authentication & Authorization

- ✅ Login dengan username & password
- ✅ Session management (timeout 30 menit)
- ✅ Account lockout setelah 5 kali gagal login (15 menit)
- ✅ Password hashing dengan bcrypt
- ✅ Logout & session cleanup

### 2. Vendor Management

- ✅ CRUD (Create, Read, Update, Delete) vendor
- ✅ Master data: Kota, Tipe Bisnis, Status
- ✅ Multi-contact vendor support
- ✅ Bank account management
- ✅ Search & filter vendors
- ✅ DataTables integration (sorting, pagination)

### 3. Dashboard

- ✅ Overview statistik vendor
- ✅ Recent activities
- ✅ User profile & settings
- ✅ Responsive design

### 4. User Interface

- ✅ Bootstrap 5 framework
- ✅ Voler responsive template
- ✅ jQuery untuk interaktivitas
- ✅ DataTables untuk data kompleks
- ✅ Form validation

---

## 🔒 Security Notes

1. **Database Credentials**: Ubah password MySQL default di `constants.php`
2. **Debug Mode**: Set `DEBUG_MODE` ke `false` di production
3. **Session Timeout**: Dapat disesuaikan via `SESSION_TIMEOUT` constant
4. **Input Validation**: Selalu validate & sanitize input user
5. **CORS**: Configure jika API diakses dari domain berbeda

---

## 🐛 Troubleshooting

### Error: "Database connection failed"

```
Solusi:
1. Cek MySQL sudah running
2. Verifikasi DB_HOST, DB_USER, DB_PASS di constants.php
3. Pastikan database 'eprocurement_db' sudah dibuat
```

### Error: "npm command not found"

```
Solusi:
1. Install Node.js dari https://nodejs.org/
2. Verifikasi dengan: node --version && npm --version
3. Restart terminal setelah instalasi
```

### Error: "node_modules not found"

```
Solusi:
1. Buka folder: cd e-procurement/public
2. Jalankan: npm install
3. Pastikan sudah ada folder node_modules/
```

### Session timeout terlalu cepat

```
Solusi:
Ubah konstanta di app/config/constants.php:
define('SESSION_TIMEOUT', 3600); // 1 jam (dalam detik)
```

---

## 📝 Catatan Developer

### Menambah Feature Baru

Ikuti langkah-langkah di `.github/copilot-instructions.md`:

1. **Buat Model** → `app/models/NewFeatureModel.php`
2. **Buat Controller** → `app/controllers/NewFeatureController.php`
3. **Update routing** → `index.php` (tambah case di switch)
4. **Buat View** → `app/views/newfeature/index.php`
5. **Tambah services** → `app/service/newfeature/` (jika diperlukan)

### Testing AJAX Calls

Gunakan browser DevTools:

1. Buka **F12** → Tab **Network**
2. Lakukan action yang trigger AJAX
3. Klik request untuk melihat detail
4. Cek response JSON di tab **Response**

### Debug Mode

Set `DEBUG_MODE` di `constants.php`:

```php
define('DEBUG_MODE', true);  // Show error messages
define('DEBUG_MODE', false); // Hide error messages (production)
```

---

## 📞 Support & Kontribusi

Untuk questions atau issues:

1. Cek dokumentasi di `README.md` dan `.github/copilot-instructions.md`
2. Review file yang relevan di folder `app/`
3. Untuk bug reports, buat issue di repository

---

## 📄 License

[Sesuai dengan LICENSE file]

**Version**: 1.0.0  
**Last Updated**: December 2024
