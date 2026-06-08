-- =====================================================
-- Production OS - MVP Database Schema
-- Minimum Viable Product - Phase 1
-- MySQL 8.0+
-- =====================================================

DROP DATABASE IF EXISTS production_os;
CREATE DATABASE production_os CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE production_os;

-- =====================================================
-- 1. ROLES TABLE
-- =====================================================
CREATE TABLE roles (
    role_id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_role_name (role_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. USERS TABLE
-- =====================================================
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    role_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE RESTRICT,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role_id (role_id),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. PERMISSIONS TABLE
-- =====================================================
CREATE TABLE permissions (
    permission_id INT PRIMARY KEY AUTO_INCREMENT,
    permission_name VARCHAR(100) UNIQUE NOT NULL,
    module VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_module (module)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. ROLE PERMISSIONS TABLE
-- =====================================================
CREATE TABLE role_permissions (
    role_permission_id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_role_permission (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON DELETE CASCADE,
    INDEX idx_role_id (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. CATEGORIES TABLE
-- =====================================================
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(7),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category_name (category_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. UNITS TABLE
-- =====================================================
CREATE TABLE units (
    unit_id INT PRIMARY KEY AUTO_INCREMENT,
    unit_name VARCHAR(50) NOT NULL,
    unit_symbol VARCHAR(10) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_unit_name (unit_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. SUPPLIERS TABLE
-- =====================================================
CREATE TABLE suppliers (
    supplier_id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_code VARCHAR(50) UNIQUE NOT NULL,
    supplier_name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_supplier_code (supplier_code),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. PRODUCTS TABLE
-- =====================================================
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_code VARCHAR(50) UNIQUE NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    unit_id INT NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_product_code (product_code),
    INDEX idx_category_id (category_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. RAW MATERIALS TABLE
-- =====================================================
CREATE TABLE raw_materials (
    rm_id INT PRIMARY KEY AUTO_INCREMENT,
    rm_code VARCHAR(50) UNIQUE NOT NULL,
    rm_name VARCHAR(100) NOT NULL,
    unit_id INT NOT NULL,
    min_stock DECIMAL(10, 2) NOT NULL DEFAULT 0,
    max_stock DECIMAL(10, 2),
    current_stock DECIMAL(10, 2) DEFAULT 0,
    supplier_id INT,
    price DECIMAL(10, 2),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES units(unit_id) ON DELETE RESTRICT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_rm_code (rm_code),
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. FORMULAS TABLE (Formula Master)
-- =====================================================
CREATE TABLE formulas (
    formula_id INT PRIMARY KEY AUTO_INCREMENT,
    formula_code VARCHAR(50) UNIQUE NOT NULL,
    formula_name VARCHAR(100) NOT NULL,
    product_id INT NOT NULL,
    version INT DEFAULT 1,
    status ENUM('active', 'inactive') DEFAULT 'active',
    description TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_product_id (product_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. FORMULA DETAILS TABLE
-- =====================================================
CREATE TABLE formula_details (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    formula_id INT NOT NULL,
    rm_id INT NOT NULL,
    percent_qty DECIMAL(5, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formula_id) REFERENCES formulas(formula_id) ON DELETE CASCADE,
    FOREIGN KEY (rm_id) REFERENCES raw_materials(rm_id) ON DELETE RESTRICT,
    INDEX idx_formula_id (formula_id),
    INDEX idx_rm_id (rm_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 12. INVENTORY TABLE
-- =====================================================
CREATE TABLE inventory (
    inventory_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    rm_id INT,
    qty DECIMAL(10, 2) NOT NULL DEFAULT 0,
    location VARCHAR(100),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (rm_id) REFERENCES raw_materials(rm_id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_rm_id (rm_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 13. PURCHASE REQUESTS TABLE
-- =====================================================
CREATE TABLE purchase_requests (
    pr_id INT PRIMARY KEY AUTO_INCREMENT,
    pr_no VARCHAR(50) UNIQUE NOT NULL,
    request_date DATE NOT NULL,
    request_by INT NOT NULL,
    status ENUM('draft', 'pending', 'approved', 'received', 'closed') DEFAULT 'draft',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (request_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_pr_no (pr_no),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 14. PURCHASE REQUEST DETAILS TABLE
-- =====================================================
CREATE TABLE purchase_request_details (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    pr_id INT NOT NULL,
    rm_id INT NOT NULL,
    qty DECIMAL(10, 2) NOT NULL,
    unit_price DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pr_id) REFERENCES purchase_requests(pr_id) ON DELETE CASCADE,
    FOREIGN KEY (rm_id) REFERENCES raw_materials(rm_id) ON DELETE RESTRICT,
    INDEX idx_pr_id (pr_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 15. RECEIVINGS TABLE (GRN - Goods Receipt Note)
-- =====================================================
CREATE TABLE receivings (
    grn_id INT PRIMARY KEY AUTO_INCREMENT,
    grn_no VARCHAR(50) UNIQUE NOT NULL,
    pr_id INT,
    receive_date DATETIME NOT NULL,
    supplier_id INT,
    received_by INT NOT NULL,
    status ENUM('pending', 'received', 'verified') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pr_id) REFERENCES purchase_requests(pr_id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE SET NULL,
    FOREIGN KEY (received_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_grn_no (grn_no),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 16. MANUFACTURING ORDERS TABLE
-- =====================================================
CREATE TABLE manufacturing_orders (
    mo_id INT PRIMARY KEY AUTO_INCREMENT,
    mo_no VARCHAR(50) UNIQUE NOT NULL,
    product_id INT NOT NULL,
    formula_id INT,
    batch_size INT NOT NULL,
    target_qty INT NOT NULL,
    produced_qty INT DEFAULT 0,
    start_date DATE,
    end_date DATE,
    status ENUM('draft', 'pending', 'in_production', 'qc', 'finished', 'closed') DEFAULT 'draft',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    FOREIGN KEY (formula_id) REFERENCES formulas(formula_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_mo_no (mo_no),
    INDEX idx_product_id (product_id),
    INDEX idx_status (status),
    INDEX idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 17. MATERIAL ISSUES TABLE
-- =====================================================
CREATE TABLE material_issues (
    issue_id INT PRIMARY KEY AUTO_INCREMENT,
    mo_id INT NOT NULL,
    issue_date DATETIME NOT NULL,
    issued_by INT NOT NULL,
    status ENUM('draft', 'issued', 'returned', 'completed') DEFAULT 'draft',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mo_id) REFERENCES manufacturing_orders(mo_id) ON DELETE RESTRICT,
    FOREIGN KEY (issued_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_mo_id (mo_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 18. MATERIAL ISSUE DETAILS TABLE
-- =====================================================
CREATE TABLE material_issue_details (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    issue_id INT NOT NULL,
    rm_id INT NOT NULL,
    qty_issued DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (issue_id) REFERENCES material_issues(issue_id) ON DELETE CASCADE,
    FOREIGN KEY (rm_id) REFERENCES raw_materials(rm_id) ON DELETE RESTRICT,
    INDEX idx_issue_id (issue_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 19. PRODUCTION RECORDS TABLE
-- =====================================================
CREATE TABLE production_records (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    mo_id INT NOT NULL,
    process_step VARCHAR(100) NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME,
    operator_id INT NOT NULL,
    qty_produced DECIMAL(10, 2),
    waste_qty DECIMAL(10, 2) DEFAULT 0,
    status ENUM('in_progress', 'completed', 'paused', 'stopped') DEFAULT 'in_progress',
    remark TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mo_id) REFERENCES manufacturing_orders(mo_id) ON DELETE CASCADE,
    FOREIGN KEY (operator_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_mo_id (mo_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 20. QC RECORDS TABLE
-- =====================================================
CREATE TABLE qc_records (
    qc_id INT PRIMARY KEY AUTO_INCREMENT,
    mo_id INT NOT NULL,
    inspection_date DATETIME NOT NULL,
    sample_size INT,
    defects_found INT DEFAULT 0,
    result ENUM('pass', 'fail', 'hold') DEFAULT 'hold',
    inspector_id INT NOT NULL,
    remark TEXT,
    approved_by INT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mo_id) REFERENCES manufacturing_orders(mo_id) ON DELETE CASCADE,
    FOREIGN KEY (inspector_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_mo_id (mo_id),
    INDEX idx_result (result)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 21. ACTIVITY LOGS TABLE
-- =====================================================
CREATE TABLE activity_logs (
    log_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    module VARCHAR(50) NOT NULL,
    description TEXT,
    related_id INT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_module (module),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT: ROLES
-- =====================================================
INSERT INTO roles (role_name, description) VALUES
('Super Admin', 'Full system access'),
('Admin', 'Administrative functions'),
('Manager', 'Department manager'),
('Warehouse', 'Warehouse operations'),
('Production', 'Production operations'),
('QC', 'Quality control staff');

-- =====================================================
-- INSERT: PERMISSIONS
-- =====================================================
INSERT INTO permissions (permission_name, module, description) VALUES
('view_products', 'products', 'View products'),
('create_products', 'products', 'Create products'),
('edit_products', 'products', 'Edit products'),
('delete_products', 'products', 'Delete products'),
('view_formulas', 'formulas', 'View formulas'),
('create_formulas', 'formulas', 'Create formulas'),
('edit_formulas', 'formulas', 'Edit formulas'),
('view_materials', 'materials', 'View materials'),
('create_materials', 'materials', 'Create materials'),
('edit_materials', 'materials', 'Edit materials'),
('view_inventory', 'inventory', 'View inventory'),
('manage_inventory', 'inventory', 'Manage inventory'),
('view_pr', 'purchasing', 'View purchase requests'),
('create_pr', 'purchasing', 'Create purchase requests'),
('approve_pr', 'purchasing', 'Approve purchase requests'),
('view_mo', 'manufacturing', 'View MO'),
('create_mo', 'manufacturing', 'Create MO'),
('edit_mo', 'manufacturing', 'Edit MO'),
('start_mo', 'manufacturing', 'Start production'),
('view_production', 'production', 'View production'),
('create_production', 'production', 'Record production'),
('view_qc', 'qc', 'View QC'),
('create_qc', 'qc', 'Create QC'),
('approve_qc', 'qc', 'Approve QC'),
('view_reports', 'reports', 'View reports'),
('export_reports', 'reports', 'Export reports');

-- =====================================================
-- INSERT: ROLE PERMISSIONS (Super Admin - All)
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, permission_id FROM permissions;

-- =====================================================
-- INSERT: ROLE PERMISSIONS (Admin - All except delete)
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions WHERE permission_name NOT LIKE 'delete_%';

-- =====================================================
-- INSERT: CATEGORIES
-- =====================================================
INSERT INTO categories (category_name, description, icon, color) VALUES
('Electronics', 'Electronic products', 'fa-microchip', '#6366f1'),
('Mechanical', 'Mechanical parts', 'fa-gears', '#8b5cf6'),
('Textiles', 'Textile products', 'fa-shirt', '#ec4899'),
('Chemicals', 'Chemical products', 'fa-flask', '#f59e0b'),
('Plastics', 'Plastic materials', 'fa-cube', '#06b6d4');

-- =====================================================
-- INSERT: UNITS
-- =====================================================
INSERT INTO units (unit_name, unit_symbol) VALUES
('Kilogram', 'kg'),
('Gram', 'g'),
('Liter', 'L'),
('Milliliter', 'ml'),
('Meter', 'm'),
('Piece', 'pcs'),
('Box', 'box'),
('Pack', 'pack');

-- =====================================================
-- INSERT: SUPPLIERS
-- =====================================================
INSERT INTO suppliers (supplier_code, supplier_name, contact_person, phone, email, address, status) VALUES
('SUP001', 'Global Materials Inc', 'John Smith', '555-0101', 'john@globalmaterials.com', '123 Main St', 'active'),
('SUP002', 'Quality Supplies Ltd', 'Jane Doe', '555-0102', 'jane@qualitysupplies.com', '456 Oak Ave', 'active'),
('SUP003', 'Premium Parts Co', 'Mike Johnson', '555-0103', 'mike@premiumparts.com', '789 Pine Rd', 'active');

-- =====================================================
-- INSERT: USERS (Demo - Password: password)
-- =====================================================
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, role_id, is_active) VALUES
('admin', 'admin@productionos.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/1Ee', 'System', 'Administrator', '555-0001', 1, TRUE),
('manager', 'manager@productionos.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/1Ee', 'John', 'Manager', '555-0002', 3, TRUE),
('operator', 'operator@productionos.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/1Ee', 'Mike', 'Operator', '555-0003', 5, TRUE),
('qc', 'qc@productionos.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/1Ee', 'Sarah', 'Inspector', '555-0004', 6, TRUE),
('warehouse', 'warehouse@productionos.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/1Ee', 'David', 'Warehouse', '555-0005', 4, TRUE);

-- =====================================================
-- Sample Products
-- =====================================================
INSERT INTO products (product_code, product_name, category_id, unit_id, description, status, created_by) VALUES
('PRD001', 'Industrial Control Panel', 1, 6, 'Advanced control panel for machinery', 'active', 1),
('PRD002', 'Metal Frame Assembly', 2, 6, 'Structural frame for equipment', 'active', 1),
('PRD003', 'Plastic Housing Unit', 5, 6, 'Protective housing for electronics', 'active', 1);

-- =====================================================
-- Sample Raw Materials
-- =====================================================
INSERT INTO raw_materials (rm_code, rm_name, unit_id, min_stock, max_stock, current_stock, supplier_id, price, status, created_by) VALUES
('MAT001', 'Steel Plate', 1, 100, 500, 250, 1, 45.50, 'active', 1),
('MAT002', 'Aluminum Wire', 2, 50, 200, 120, 2, 8.75, 'active', 1),
('MAT003', 'Plastic Resin', 1, 100, 400, 180, 3, 25.00, 'active', 1);

-- =====================================================
-- Database Setup Complete
-- =====================================================
