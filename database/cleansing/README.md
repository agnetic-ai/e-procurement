# Master Data Cleansing

## Mengosongkan data transaksi

Jika tujuan cleansing adalah menghapus semua data transaksi dan mempertahankan master, gunakan `04_truncate_transaction_data.sql`. Script `01` sampai `03` adalah normalisasi isi master dan tidak diperlukan untuk proses truncate transaksi.

Data yang dikosongkan: PR, approval PR, PO, approval PO, goods receipt, invoice, payment, unit aset, dan riwayat assignment aset. `asset_units` ikut dikosongkan karena record-nya dihasilkan dari goods receipt dan bergantung pada detail penerimaan barang.

Script mempunyai mode preview secara default. Eksekusi truncate memerlukan session variable `@confirm_truncate = 'TRUNCATE_TRANSACTION_DATA'`.

## Normalisasi master

Jalankan file secara berurutan pada database staging atau salinan database produksi:

1. `01_audit_master_data.sql` — audit read-only.
2. `02_cleanse_master_data_safe.sql` — backup dan normalisasi tanpa menghapus master.
3. Jalankan kembali `01_audit_master_data.sql` untuk membandingkan hasil.
4. `03_merge_duplicate_products_review.sql` — preview merge produk. Default tidak mengubah data bisnis karena `@apply_product_merge = 0`; tabel backup tetap dibuat.

Sebelum mengaktifkan merge produk, pastikan seluruh baris `valid_mapping = 1` dan query `PRICE_COLLISION` tidak menghasilkan baris. Setelah disetujui, ubah `@apply_product_merge` menjadi `1`, jalankan script, lalu audit ulang.

Kasus NPWP vendor ganda dan serial aset ganda hanya ditampilkan untuk review manual karena tidak aman diputuskan dari data teknis saja.
