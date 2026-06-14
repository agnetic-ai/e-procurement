# E-Procurement System — System Analysis Document

## Overview

Sistem e-Procurement adalah platform pengadaan barang/jasa yang mengelola seluruh siklus **Procure-to-Pay** — dari permintaan pembayaran (Purchase Request) hingga pembayaran akhir (Payment).

---

## Actors (Roles)

| Role | Code | Deskripsi |
|------|------|-----------|
| Administrator | `admin` | Full system access, kelola master data |
| Requester | `requestor` | Buat Purchase Request (PR) |
| Manager | `manager` | Approval level 1 (PR & PO) |
| Head | `head` | Approval level 2 (PR & PO) |
| Director | `director` | Approval level 3 (PR & PO) |
| Procurement | `procurement` | Kelola PO, GR, Invoice |
| Finance | `finance` | Verifikasi invoice, proses pembayaran |

---

## Business Flow (Procure-to-Pay)

### Tahap 1 — Purchase Request (PR)

**Actor:** Requester

**Proses:**
1. Requester membuat PR: pilih produk, vendor, qty, estimasi harga
2. Sistem generate nomor PR otomatis (`PR-2026-XXXXX`)
3. Sistem hitung `total_estimated` dari semua item
4. Status berubah: `PR_DRAFT → PR_PENDING`
5. Kirim notifikasi email ke approver

**Approval Rules (berdasarkan total_estimated):**

| Min Amount | Max Amount | Level Approval | Approver |
|------------|------------|----------------|----------|
| 0 | 10,000,000 | 1 | Manager |
| 10,000,001 | 50,000,000 | 2 | Manager → Head |
| 50,000,001 | - | 3 | Manager → Head → Director |

**Approval Flow:**
- Setiap approver bisa `APR_APPROVED` atau `APR_REJECTED`
- Approval sequential (level 1 dulu, baru level 2, dst)
- Semua level approve → status `PR_APPROVED`
- Salah satu reject → status `PR_REJECTED`

**Status Lifecycle:**
```
PR_DRAFT → PR_PENDING → PR_APPROVED → PR_CONVERTED (ke PO)
                    ↘ PR_REJECTED
                    ↘ PR_CANCELLED
```

---

### Tahap 2 — Purchase Order (PO)

**Actor:** Procurement

**Proses:**
1. Procurement buat PO dari PR yang sudah `PR_APPROVED`
2. Pilih vendor final, payment terms (Net 30, COD, dll)
3. Sistem generate nomor PO otomatis (`PO-2026-XXXXX`)
4. Status: `PO_DRAFT → PO_SUBMITTED`
5. PO mengikuti approval rules terpisah dari PR

**Approval Rules (berdasarkan total_amount):**

| Min Amount | Max Amount | Level Approval | Approver |
|------------|------------|----------------|----------|
| 0 | 20,000,000 | 1 | Manager |
| 20,000,001 | 60,000,000 | 2 | Manager → Head |
| 60,000,001 | - | 3 | Manager → Head → Director |

**Status Lifecycle:**
```
PO_DRAFT → PO_SUBMITTED → PO_APPROVED → PO_PROCESS → PO_COMPLETED
                      ↘ PO_REJECTED
                              ↘ PO_PARTIAL (sebagian diterima)
```

---

### Tahap 3 — Goods Receipt (GR)

**Actor:** Procurement

**Proses:**
1. Procurement buat GR dari PO yang sudah `PO_APPROVED`
2. Pilih PO number → sistem load item dari PO
3. Input qty diterima per item (bisa partial)
4. Sistem generate nomor GR otomatis (`GR-XXXXX`)
5. Status: `GR_DRAFT → GR_POSTED`
6. Setelah GR posted:
   - PO status update: `PO_COMPLETED` (full) atau `PO_PARTIAL` (sebagian)
   - Jika item bertipe aset → sistem auto-generate `asset_units` dengan serial number

**Status Lifecycle:**
```
GR_DRAFT → GR_PROCESS → GR_POSTED
```

---

### Tahap 4 — Invoice

**Actor:** Procurement (buat), Finance (verifikasi)

**Proses Pembuatan:**
1. Procurement buat invoice dari PO yang sudah ada GR
2. Sistem load item dari PO, match dengan qty yang diterima
3. Input invoice number dari vendor, tanggal, jatuh tempo
4. Status: `INV_DRAFT`

**Price Audit:**
- Sistem otomatis membandingkan harga PO vs harga invoice
- Selisih dicatat di `invoice_price_audits` (DISCOUNT / PRICE_ADJUSTMENT)
- Catatan alasan perubahan harga wajib diisi

**Verifikasi (Finance):**
- Finance review invoice, bandingkan dengan PO dan GR
- `INV_VERIFIED` → lanjut ke pembayaran
- `INV_REJECTED` → tolak dengan alasan

**Status Lifecycle:**
```
INV_DRAFT → INV_VERIFIED → INV_PAID
                    ↘ INV_REJECTED
```

---

### Tahap 5 — Payment

**Actor:** Finance

**Proses:**
1. Finance proses pembayaran untuk invoice yang `INV_VERIFIED`
2. Input: tanggal bayar, jumlah, metode (transfer), nomor referensi bank
3. **Wajib upload bukti pembayaran** (payment_proof)
4. Bisa partial payment (bayar sebagian)
5. Status invoice berubah: `INV_PAID`

**Data yang dicatat:**
- Payment date
- Paid amount
- Payment method (Transfer)
- Reference number (nomor referensi bank)
- Payment proof (file upload: PDF/JPEG/PNG)
- Notes

---

## Supporting Modules

### Master Data

**Products**
- Code, name, description, category, unit of measure
- Status: `PRODUCT_ACTIVE`, `PRODUCT_INACTIVE`, `PRODUCT_EXP`
- Relasi ke `product_vendor_prices` (harga per vendor)

**Vendors**
- Vendor code, company name, email, phone, address
- Relasi ke `cities`, `business_types`, `payment_terms`
- Sub-entity: `vendor_contacts` (kontak person), `vendor_bank_accounts` (rekening bank)
- Status: `VEND_PENDING → VEND_ACTIVE → VEND_SUSPENDED / VEND_BLACKLISTED`

**Employees**
- Employee code, full name, email, phone, department, position
- Employment status: `EMP_ACTIVE`, `EMP_RESIGNED`, `EMP_TERMINATED`
- Digunakan sebagai: requester, receiver, asset assignee

**Payment Terms**
- Payment code, name, days (Net 30 = 30 hari), description

---

### RBAC (Role-Based Access Control)

**Struktur:**
```
roles (1) ──→ users (many)
menus (1) ──→ role_menus (many) ──→ roles (1)
```

**Permission per Menu:**
- `can_view` — akses lihat
- `can_create` — akses buat data
- `can_edit` — akses edit
- `can_delete` — akses hapus

**Menu Hierarchy:**
- Parent menu → Child menu (nested)
- `menu_order` untuk urutan tampil
- `is_active` untuk enable/disable

---

### Asset Tracking

**Asset Units:**
- Dibuat otomatis dari GR (saat item bertipe aset diterima)
- Punya serial number (unique)
- Status lifecycle: `ASSET_IN_STOCK → ASSET_ASSIGNED → ASSET_RETURNED`
- Bisa: `ASSET_DAMAGED`, `ASSET_LOST`, `ASSET_DISPOSED`

**Asset Assignments:**
- Assign unit aset ke employee
- Catat: assigned_at, assigned_by, notes
- Bisa return (returned_at diisi)

---

### Email Notification

- Trigger: PR di-submit → email ke approver
- Service: `EmailNotificationService`
- Non-blocking (async-like, error di-log tapi tidak block proses)

---

## Entity Relationship Summary

### Core Transaction Tables

```
purchase_requests (1) ──→ purchase_request_details (many)
       │                        │
       │                        ├──→ products
       │                        └──→ vendors
       │
       ├──→ purchase_request_approvals (many)
       │         └──→ users (approver)
       │
       └──→ purchase_orders (1) ──→ purchase_order_details (many)
                │                        │
                │                        ├──→ products
                │                        └──→ purchase_request_details
                │
                ├──→ purchase_orders_approvals (many)
                │         └──→ users (approver)
                │
                ├──→ goods_receipts (1) ──→ goods_receipt_details (many)
                │         │                    │
                │         │                    └──→ purchase_order_details
                │         │
                │         └──→ asset_units (many) ──→ asset_assignments
                │                    │                     │
                │                    ├──→ products         └──→ employees
                │                    └──→ employees
                │
                ├──→ invoices (1) ──→ invoice_details (many)
                │         │              │
                │         │              ├──→ products
                │         │              └──→ purchase_order_details
                │         │
                │         ├──→ invoice_price_audits (many)
                │         │
                │         └──→ payments (many)
                │
                └──→ vendors ──→ vendor_contacts
                              ──→ vendor_bank_accounts
                              ──→ cities
                              ──→ business_types
                              ──→ payment_terms
```

### Master Data Tables

```
categories ──→ products
cities ──→ vendors
business_types ──→ vendors
payment_terms ──→ vendors, purchase_orders
roles ──→ users, role_menus, approval_level_roles
menus ──→ role_menus
approval_levels ──→ approval_level_roles
status_codes (lookup untuk semua module)
```

---

## Status Codes Reference

| Module | Code | Name | Deskripsi |
|--------|------|------|-----------|
| **PR** | PR_DRAFT | Draft | PR baru dibuat |
| | PR_PENDING | Pending | Menunggu approval |
| | PR_APPROVED | Approved | Disetujui semua level |
| | PR_REJECTED | Rejected | Ditolak |
| | PR_CANCELLED | Cancelled | Dibatalkan |
| | PR_CONVERTED | Converted to PO | Sudah jadi PO |
| **PO** | PO_DRAFT | Draft | PO baru dibuat |
| | PO_SUBMITTED | Submitted | Diajukan ke approval |
| | PO_APPROVED | Approved | Disetujui |
| | PO_REJECTED | Rejected | Ditolak |
| | PO_PROCESS | In Process | Dalam proses |
| | PO_PARTIAL | Partial | Sebagian diterima |
| | PO_COMPLETED | Completed | Selesai |
| **GR** | GR_DRAFT | Draft | GR baru dibuat |
| | GR_PROCESS | Process | Dalam proses |
| | GR_POSTED | Posted | Sudah diposting |
| **INV** | INV_DRAFT | Draft | Invoice baru |
| | INV_VERIFIED | Verified | Sudah diverifikasi |
| | INV_REJECTED | Rejected | Ditolak |
| | INV_PAID | Paid | Sudah dibayar |
| **VENDOR** | VEND_PENDING | Pending Review | Vendor baru |
| | VEND_ACTIVE | Active | Aktif |
| | VEND_INACTIVE | Inactive | Nonaktif |
| | VEND_SUSPENDED | Suspended | Ditangguhkan |
| | VEND_BLACKLISTED | Blacklisted | Diblacklist |
| **ASSET** | ASSET_DRAFT | Draft | Unit baru |
| | ASSET_IN_STOCK | In Stock | Tersedia |
| | ASSET_ASSIGNED | Assigned | Sudah di-assign |
| | ASSET_RETURNED | Returned | Sudah dikembalikan |
| | ASSET_DAMAGED | Damaged | Rusak |
| | ASSET_LOST | Lost | Hilang |
| | ASSET_DISPOSED | disposed | Dimusnahkan |

---

## Technology Stack

- **Backend:** PHP 8.3 (custom MVC framework, bukan Laravel)
- **Database:** MySQL (MariaDB)
- **Frontend:** Vanilla JS, Tailwind CSS, Select2, SweetAlert
- **Server:** nginx + PHP-FPM
- **Architecture:** Front-controller pattern (`index.php` routing via `?url=`)

---

## Database Tables (32 tables)

### Transaction Tables
- `purchase_requests` — header PR
- `purchase_request_details` — item PR
- `purchase_request_approvals` — approval log PR
- `purchase_orders` — header PO
- `purchase_order_details` — item PO
- `purchase_orders_approvals` — approval log PO
- `goods_receipts` — header GR
- `goods_receipt_details` — item GR
- `invoices` — header invoice
- `invoice_details` — item invoice
- `invoice_price_audits` — audit selisih harga
- `payments` — pembayaran

### Master Data Tables
- `products` — produk/jasa
- `categories` — kategori produk
- `vendors` — vendor/supplier
- `vendor_contacts` — kontak vendor
- `vendor_bank_accounts` — rekening vendor
- `employees` — karyawan
- `payment_terms` — termin pembayaran
- `business_types` — jenis usaha
- `cities` — kota/provinsi
- `product_vendor_prices` — harga per vendor

### System Tables
- `users` — user login
- `roles` — role/hak akses
- `menus` — menu navigasi
- `role_menus` — permission per role
- `approval_levels` — level approval
- `approval_level_roles` — role per level
- `approval_rules` — rules threshold approval
- `status_codes` — lookup status semua module
- `asset_units` — unit aset
- `asset_assignments` — assignment aset ke karyawan
