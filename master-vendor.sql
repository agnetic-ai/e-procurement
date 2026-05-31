-- ============================================
-- DATABASE SCHEMA: e-PROCUREMENT SYSTEM
-- FILE: create_tables_and_insert_data.sql
-- ============================================

-- ============================================
-- 1. TABEL MASTER: status_codes (Universal untuk semua module)
-- ============================================
CREATE TABLE status_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_code VARCHAR(20) NOT NULL COMMENT 'Prefix module: VEND, PO, PR',
    status_code VARCHAR(30) UNIQUE NOT NULL COMMENT 'Kode unik: VEND_ACTIVE, PO_PENDING',
    status_name VARCHAR(50) NOT NULL,
    DESCRIPTION TEXT,
    color VARCHAR(20) DEFAULT '#6c757d' COMMENT 'Warna untuk UI',
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE COMMENT 'Status default untuk module',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_module_code (module_code),
    INDEX idx_status_code (status_code),
    INDEX idx_display_order (display_order),
    UNIQUE KEY unique_module_status (module_code, status_name)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. TABEL REFERENSI: cities (Master data kota)
-- ============================================
CREATE TABLE cities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    city_name VARCHAR(100) NOT NULL,
    province_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_city_name (city_name),
    INDEX idx_province (province_name),
    UNIQUE KEY unique_city_province (city_name, province_name)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. TABEL REFERENSI: business_types (Jenis usaha)
-- ============================================
CREATE TABLE business_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type_code VARCHAR(10) UNIQUE NOT NULL,
    type_name VARCHAR(100) NOT NULL,
    DESCRIPTION TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_type_code (type_code),
    INDEX idx_type_name (type_name)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. TABEL UTAMA: vendors (Data utama vendor)
-- ============================================
CREATE TABLE vendors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Format: VEND-YYYY-NNN',
    company_name VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city_id INT NOT NULL,
    business_type_id INT NOT NULL,
    status_code VARCHAR(30) NOT NULL DEFAULT 'VEND_PENDING',
    tax_number VARCHAR(25) COMMENT 'NPWP format: 12.345.678.9-012.345',
    credit_limit DECIMAL(15,2) DEFAULT 0.00,
    payment_terms VARCHAR(50) DEFAULT 'Net 30',
    website VARCHAR(200),
    established_year YEAR,
    total_transactions DECIMAL(15,2) DEFAULT 0.00,
    last_transaction_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (business_type_id) REFERENCES business_types(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (status_code) REFERENCES status_codes(status_code) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_vendor_code (vendor_code),
    INDEX idx_company_name (company_name),
    INDEX idx_status_code (status_code),
    INDEX idx_city_id (city_id),
    INDEX idx_business_type_id (business_type_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. TABEL: vendor_contacts (Kontak person vendor)
-- ============================================
CREATE TABLE vendor_contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    POSITION VARCHAR(100),
    department VARCHAR(100),
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    is_primary BOOLEAN DEFAULT FALSE COMMENT '1 = Kontak utama',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key
    FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_vendor_id (vendor_id),
    INDEX idx_contact_name (contact_name),
    INDEX idx_is_primary (is_primary)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. TABEL: vendor_bank_accounts (Rekening bank)
-- ============================================
CREATE TABLE vendor_bank_accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    bank_name VARCHAR(100) NOT NULL,
    bank_code VARCHAR(10) COMMENT 'Kode bank: BCA, BRI, etc',
    account_number VARCHAR(50) NOT NULL,
    account_name VARCHAR(100) NOT NULL,
    account_type ENUM('savings', 'current', 'others') DEFAULT 'current',
    currency VARCHAR(10) DEFAULT 'IDR',
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign Key
    FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Indexes & Constraints
    UNIQUE KEY unique_account (bank_name, account_number),
    INDEX idx_vendor_id (vendor_id),
    INDEX idx_is_primary (is_primary)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. TABEL: purchase_requests (Purchase Request)
-- ============================================
CREATE TABLE purchase_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pr_number VARCHAR(20) UNIQUE NOT NULL COMMENT 'Format: PR-YYYY-NNN',
    title VARCHAR(200) NOT NULL,
    DESCRIPTION TEXT,
    requested_by INT COMMENT 'User ID yang request',
    request_date DATE NOT NULL,
    needed_by DATE,
    department VARCHAR(100),
    status_code VARCHAR(30) NOT NULL DEFAULT 'PR_DRAFT',
    total_estimated DECIMAL(15,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key
    FOREIGN KEY (status_code) REFERENCES status_codes(status_code) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_pr_number (pr_number),
    INDEX idx_status_code (status_code),
    INDEX idx_request_date (request_date),
    INDEX idx_requested_by (requested_by)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. TABEL: purchase_orders (Purchase Order)
-- ============================================
CREATE TABLE purchase_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    po_number VARCHAR(20) UNIQUE NOT NULL COMMENT 'Format: PO-YYYY-NNN',
    pr_id INT COMMENT 'Reference ke Purchase Request',
    vendor_id INT NOT NULL,
    po_date DATE NOT NULL,
    delivery_date DATE,
    total_amount DECIMAL(15,2) NOT NULL,
    status_code VARCHAR(30) NOT NULL DEFAULT 'PO_DRAFT',
    payment_terms VARCHAR(50),
    shipping_address TEXT,
    notes TEXT,
    created_by INT COMMENT 'User ID yang buat PO',
    approved_by INT COMMENT 'User ID yang approve',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (status_code) REFERENCES status_codes(status_code) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (pr_id) REFERENCES purchase_requests(id) ON DELETE SET NULL ON UPDATE CASCADE,
    
    -- Indexes
    INDEX idx_po_number (po_number),
    INDEX idx_status_code (status_code),
    INDEX idx_vendor_id (vendor_id),
    INDEX idx_po_date (po_date),
    INDEX idx_pr_id (pr_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT DATA: status_codes
-- ============================================

-- STATUS UNTUK MODULE VENDOR (VEND)
INSERT INTO status_codes (module_code, status_code, status_name, DESCRIPTION, color, display_order, is_default) VALUES
('VEND', 'VEND_PENDING', 'Pending Review', 'Vendor baru, menunggu verifikasi dokumen', '#ffc107', 1, TRUE),
('VEND', 'VEND_ACTIVE', 'Active', 'Vendor aktif dan bisa bertransaksi', '#28a745', 2, FALSE),
('VEND', 'VEND_INACTIVE', 'Inactive', 'Vendor tidak aktif sementara', '#6c757d', 3, FALSE),
('VEND', 'VEND_SUSPENDED', 'Suspended', 'Vendor ditangguhkan karena masalah', '#dc3545', 4, FALSE),
('VEND', 'VEND_BLACKLISTED', 'Blacklisted', 'Vendor diblacklist permanen', '#343a40', 5, FALSE);

-- STATUS UNTUK MODULE PURCHASE ORDER (PO)
INSERT INTO status_codes (module_code, status_code, status_name, DESCRIPTION, color, display_order) VALUES
('PO', 'PO_DRAFT', 'Draft', 'PO dalam proses draft', '#6c757d', 1),
('PO', 'PO_PENDING', 'Pending Approval', 'Menunggu approval dari manager', '#ffc107', 2),
('PO', 'PO_APPROVED', 'Approved', 'PO sudah disetujui', '#20c997', 3),
('PO', 'PO_REJECTED', 'Rejected', 'PO ditolak oleh approver', '#dc3545', 4),
('PO', 'PO_ORDERED', 'Ordered', 'PO sudah dikirim ke vendor', '#0dcaf0', 5),
('PO', 'PO_PARTIAL', 'Partially Received', 'Barang sebagian sudah diterima', '#fd7e14', 6),
('PO', 'PO_COMPLETED', 'Completed', 'Semua barang sudah diterima', '#28a745', 7),
('PO', 'PO_CANCELLED', 'Cancelled', 'PO dibatalkan', '#343a40', 8);

-- STATUS UNTUK MODULE PURCHASE REQUEST (PR)
INSERT INTO status_codes (module_code, status_code, status_name, DESCRIPTION, color, display_order) VALUES
('PR', 'PR_DRAFT', 'Draft', 'PR dalam proses draft', '#6c757d', 1),
('PR', 'PR_PENDING', 'Pending', 'Menunggu review', '#ffc107', 2),
('PR', 'PR_APPROVED', 'Approved', 'PR disetujui', '#28a745', 3),
('PR', 'PR_REJECTED', 'Rejected', 'PR ditolak', '#dc3545', 4),
('PR', 'PR_CONVERTED', 'Converted to PO', 'Sudah dikonversi ke PO', '#0d6efd', 5),
('PR', 'PR_CANCELLED', 'Cancelled', 'PR dibatalkan', '#343a40', 6);

-- ============================================
-- INSERT DATA: cities
-- ============================================
INSERT INTO cities (city_name, province_name) VALUES
('Jakarta Selatan', 'DKI Jakarta'),
('Jakarta Pusat', 'DKI Jakarta'),
('Jakarta Utara', 'DKI Jakarta'),
('Jakarta Barat', 'DKI Jakarta'),
('Jakarta Timur', 'DKI Jakarta'),
('Bandung', 'Jawa Barat'),
('Surabaya', 'Jawa Timur'),
('Medan', 'Sumatera Utara'),
('Semarang', 'Jawa Tengah'),
('Makassar', 'Sulawesi Selatan'),
('Denpasar', 'Bali'),
('Yogyakarta', 'DI Yogyakarta'),
('Malang', 'Jawa Timur'),
('Bekasi', 'Jawa Barat'),
('Tangerang', 'Banten');

-- ============================================
-- INSERT DATA: business_types
-- ============================================
INSERT INTO business_types (type_code, type_name, DESCRIPTION) VALUES
('SUP', 'Supplier Material', 'Penyedia bahan baku dan material konstruksi'),
('CONT', 'Contractor', 'Kontraktor pekerjaan konstruksi dan bangunan'),
('SERV', 'Service Provider', 'Penyedia jasa maintenance, cleaning, security'),
('LOG', 'Logistics', 'Penyedia jasa transportasi dan logistik'),
('CONS', 'Consultant', 'Konsultan teknis, hukum, dan manajemen'),
('IT', 'IT Vendor', 'Penyedia perangkat keras dan lunak IT'),
('OFF', 'Office Supplier', 'Penyedia perlengkapan kantor');

-- ============================================
-- INSERT DATA: vendors
-- ============================================
INSERT INTO vendors (
    vendor_code, company_name, email, phone, address, city_id, business_type_id,
    status_code, tax_number, credit_limit, payment_terms, website, established_year,
    total_transactions, last_transaction_date, notes
) VALUES 
('VEND-2025-001', 'PT Supplier Jaya Abadi', 'info@supplierjaya.com', '021-1234567',
 'Jl. Sudirman No. 123, Kav. 45, Lt. 8', 1, 1,
 'VEND_ACTIVE',
 '01.234.567.8-912.000', 500000000.00, 'Net 30',
 'www.supplierjaya.com', 2010, 1250000000.00, '2025-01-15',
 'Vendor terpercaya, pengiriman tepat waktu'),

('VEND-2025-002', 'CV Mandiri Sejahtera', 'contact@mandirisejahtera.co.id', '022-7654321',
 'Jl. Dago No. 456, Bandung', 6, 2,
 'VEND_ACTIVE',
 '02.345.678.9-123.000', 250000000.00, 'Net 45',
 'www.mandirisejahtera.co.id', 2015, 750000000.00, '2025-01-10',
 'Spesialis konstruksi bangunan tinggi'),

('VEND-2025-003', 'UD Sumber Makmur', 'ud.sumbermakmur@gmail.com', '031-9876543',
 'Jl. Raya Darmo No. 789, Surabaya', 7, 1,
 'VEND_PENDING',
 '03.456.789.0-234.000', 100000000.00, 'Net 60',
 NULL, 2018, 350000000.00, '2024-12-20',
 'Menunggu verifikasi dokumen legal'),

('VEND-2025-004', 'PT Global Teknik Indonesia', 'sales@globalteknik.co.id', '021-5551234',
 'Jl. Gatot Subroto No. 321, Jakarta Selatan', 1, 6,
 'VEND_ACTIVE',
 '04.567.890.1-345.000', 750000000.00, 'Net 30',
 'www.globalteknik.co.id', 2005, 2500000000.00, '2025-01-18',
 'Vendor IT terkemuka'),

('VEND-2025-005', 'CV Logistik Cepat', 'cs@logistikcepat.com', '024-1237890',
 'Jl. Pemuda No. 56, Semarang', 9, 4,
 'VEND_ACTIVE',
 '05.678.901.2-456.000', 300000000.00, 'Net 15',
 'www.logistikcepat.com', 2012, 900000000.00, '2025-01-12',
 'Specialist logistik antar pulau');

-- ============================================
-- INSERT DATA: vendor_contacts
-- ============================================
INSERT INTO vendor_contacts (vendor_id, contact_name, POSITION, department, contact_phone, contact_email, is_primary) VALUES
(1, 'Budi Santoso', 'Director', 'Management', '0812-3456-7890', 'budi@supplierjaya.com', TRUE),
(1, 'Siti Rahayu', 'Sales Manager', 'Sales & Marketing', '0813-4567-8901', 'siti@supplierjaya.com', FALSE),
(2, 'Ahmad Rizki', 'Owner', 'Management', '0821-2345-6789', 'ahmad@mandirisejahtera.co.id', TRUE),
(3, 'Dewi Anggraeni', 'Manager', 'Management', '0831-3456-7890', 'dewi@sumbermakmur.com', TRUE),
(4, 'Rudi Hartono', 'Sales Director', 'Sales', '0815-1234-5678', 'rudi@globalteknik.co.id', TRUE),
(5, 'Joko Prasetyo', 'Operations Manager', 'Operations', '0817-3456-7890', 'joko@logistikcepat.com', TRUE);

-- ============================================
-- INSERT DATA: vendor_bank_accounts
-- ============================================
INSERT INTO vendor_bank_accounts (vendor_id, bank_name, bank_code, account_number, account_name, account_type, is_primary) VALUES
(1, 'Bank Central Asia', 'BCA', '1234567890', 'PT Supplier Jaya Abadi', 'current', TRUE),
(2, 'Bank BRI', 'BRI', '1122334455', 'CV Mandiri Sejahtera', 'current', TRUE),
(3, 'Bank BNI', 'BNI', '2233445566', 'UD Sumber Makmur', 'current', TRUE),
(4, 'Bank CIMB Niaga', 'CIMB', '3344556677', 'PT Global Teknik Indonesia', 'current', TRUE),
(5, 'Bank Permata', 'PERMATA', '4455667788', 'CV Logistik Cepat', 'current', TRUE);

-- ============================================
-- INSERT DATA: purchase_requests
-- ============================================
INSERT INTO purchase_requests (
    pr_number, title, DESCRIPTION, request_date, needed_by, department,
    status_code, total_estimated, notes
) VALUES
('PR-2025-001', 'Purchase Office Supplies', 'Printer paper, toner, stationery for Q1 2025', '2025-01-10', '2025-01-20', 'Administration',
 'PR_APPROVED', 2500000.00, 'Standard office supplies'),
 
('PR-2025-002', 'IT Equipment Upgrade', '10 units of Dell laptops for new employees', '2025-01-12', '2025-02-01', 'IT Department',
 'PR_PENDING', 150000000.00, 'Need approval from IT manager'),
 
('PR-2025-003', 'Construction Materials', 'Cement, steel, bricks for warehouse renovation', '2025-01-15', '2025-01-25', 'Facility Management',
 'PR_CONVERTED', 85000000.00, 'Already converted to PO');

-- ============================================
-- INSERT DATA: purchase_orders
-- ============================================
INSERT INTO purchase_orders (
    po_number, pr_id, vendor_id, po_date, delivery_date, total_amount,
    status_code, payment_terms, shipping_address, notes
) VALUES
('PO-2025-001', 3, 1, '2025-01-16', '2025-01-25', 85000000.00,
 'PO_APPROVED', 'Net 30', 'Jl. Industri No. 45, Jakarta Timur', 'Construction materials for warehouse renovation'),
 
('PO-2025-002', NULL, 4, '2025-01-18', '2025-01-30', 120000000.00,
 'PO_PENDING', 'Net 45', 'Jl. Sudirman No. 123, Jakarta Selatan', 'IT equipment purchase'),
 
('PO-2025-003', NULL, 5, '2025-01-20', '2025-01-28', 45000000.00,
 'PO_ORDERED', 'Net 15', 'Jl. Gatot Subroto No. 321, Jakarta Selatan', 'Logistics services for product distribution');

-- ============================================
-- VERIFIKASI DATA
-- ============================================
SELECT 'Database eprocurement_db created successfully!' AS message;

SELECT 
    (SELECT COUNT(*) FROM status_codes) AS total_status_codes,
    (SELECT COUNT(*) FROM cities) AS total_cities,
    (SELECT COUNT(*) FROM business_types) AS total_business_types,
    (SELECT COUNT(*) FROM vendors) AS total_vendors,
    (SELECT COUNT(*) FROM vendor_contacts) AS total_contacts,
    (SELECT COUNT(*) FROM vendor_bank_accounts) AS total_bank_accounts,
    (SELECT COUNT(*) FROM purchase_requests) AS total_pr,
    (SELECT COUNT(*) FROM purchase_orders) AS total_po;

-- ============================================
-- END OF SCRIPT
-- ============================================