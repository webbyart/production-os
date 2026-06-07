-- =====================================================
-- Production OS - Manufacturing Management System
-- Database: production_os
-- MySQL 8.0+
-- =====================================================

-- Drop existing database if exists
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role_name (role_name),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. PERMISSIONS TABLE
-- =====================================================
CREATE TABLE permissions (
    permission_id INT PRIMARY KEY AUTO_INCREMENT,
    permission_name VARCHAR(100) UNIQUE NOT NULL,
    module VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_module (module),
    INDEX idx_action (action),
    INDEX idx_permission_name (permission_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. ROLE PERMISSIONS TABLE
-- =====================================================
CREATE TABLE role_permissions (
    role_permission_id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_role_permission (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON DELETE CASCADE,
    INDEX idx_role_id (role_id),
    INDEX idx_permission_id (permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. USERS TABLE
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
    profile_image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(255),
    last_login TIMESTAMP NULL,
    password_changed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE RESTRICT,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role_id (role_id),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. AUDIT LOG TABLE
-- =====================================================
CREATE TABLE audit_logs (
    log_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    module VARCHAR(50) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_module (module),
    INDEX idx_created_at (created_at),
    INDEX idx_table_name (table_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. CATEGORY TABLE
-- =====================================================
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category_name (category_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. UNITS TABLE
-- =====================================================
CREATE TABLE units (
    unit_id INT PRIMARY KEY AUTO_INCREMENT,
    unit_name VARCHAR(50) NOT NULL,
    unit_symbol VARCHAR(10) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_unit_name (unit_name)
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
    specification TEXT,
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
-- 9. SUPPLIERS TABLE
-- =====================================================
CREATE TABLE suppliers (
    supplier_id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_code VARCHAR(50) UNIQUE NOT NULL,
    supplier_name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    postal_code VARCHAR(20),
    country VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_supplier_code (supplier_code),
    INDEX idx_supplier_name (supplier_name),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. RAW MATERIALS TABLE
-- =====================================================
CREATE TABLE raw_materials (
    rm_id INT PRIMARY KEY AUTO_INCREMENT,
    rm_code VARCHAR(50) UNIQUE NOT NULL,
    rm_name VARCHAR(100) NOT NULL,
    unit_id INT NOT NULL,
    min_stock DECIMAL(10, 2) NOT NULL DEFAULT 0,
    max_stock DECIMAL(10, 2),
    supplier_id INT,
    current_stock DECIMAL(10, 2) DEFAULT 0,
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
-- 11. MANUFACTURING ORDERS TABLE
-- =====================================================
CREATE TABLE manufacturing_orders (
    mo_id INT PRIMARY KEY AUTO_INCREMENT,
    mo_no VARCHAR(50) UNIQUE NOT NULL,
    product_id INT NOT NULL,
    batch_size INT NOT NULL,
    target_qty INT NOT NULL,
    produced_qty INT DEFAULT 0,
    start_date DATE,
    end_date DATE,
    status ENUM('draft', 'pending', 'production', 'qc', 'finished', 'closed') DEFAULT 'draft',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_mo_no (mo_no),
    INDEX idx_product_id (product_id),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 12. INVENTORY TABLE
-- =====================================================
CREATE TABLE inventory (
    inventory_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    rm_id INT,
    warehouse_location VARCHAR(100),
    quantity DECIMAL(10, 2) NOT NULL DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (rm_id) REFERENCES raw_materials(rm_id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_rm_id (rm_id),
    CHECK ((product_id IS NOT NULL AND rm_id IS NULL) OR (product_id IS NULL AND rm_id IS NOT NULL))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 13. STOCK IN TABLE
-- =====================================================
CREATE TABLE stock_in (
    stock_in_id INT PRIMARY KEY AUTO_INCREMENT,
    rm_id INT NOT NULL,
    supplier_id INT,
    quantity DECIMAL(10, 2) NOT NULL,
    unit_price DECIMAL(10, 2),
    total_price DECIMAL(12, 2),
    reference_no VARCHAR(100),
    received_by INT NOT NULL,
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rm_id) REFERENCES raw_materials(rm_id) ON DELETE RESTRICT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE SET NULL,
    FOREIGN KEY (received_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_rm_id (rm_id),
    INDEX idx_received_at (received_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 14. STOCK OUT TABLE
-- =====================================================
CREATE TABLE stock_out (
    stock_out_id INT PRIMARY KEY AUTO_INCREMENT,
    mo_id INT,
    rm_id INT NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    issued_by INT NOT NULL,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mo_id) REFERENCES manufacturing_orders(mo_id) ON DELETE SET NULL,
    FOREIGN KEY (rm_id) REFERENCES raw_materials(rm_id) ON DELETE RESTRICT,
    FOREIGN KEY (issued_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_mo_id (mo_id),
    INDEX idx_rm_id (rm_id),
    INDEX idx_issued_at (issued_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 15. STOCK ADJUSTMENT TABLE
-- =====================================================
CREATE TABLE stock_adjustment (
    adjustment_id INT PRIMARY KEY AUTO_INCREMENT,
    rm_id INT,
    product_id INT,
    adjustment_type ENUM('increase', 'decrease') NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    reason VARCHAR(100),
    reference_no VARCHAR(100),
    approved_by INT,
    adjusted_by INT NOT NULL,
    adjusted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (rm_id) REFERENCES raw_materials(rm_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (adjusted_by) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_rm_id (rm_id),
    INDEX idx_product_id (product_id),
    INDEX idx_adjusted_at (adjusted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 16. PRODUCTION RECORDS TABLE
-- =====================================================
CREATE TABLE production_records (
    production_id INT PRIMARY KEY AUTO_INCREMENT,
    mo_id INT NOT NULL,
    process_step VARCHAR(100) NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME,
    operator_id INT NOT NULL,
    quantity_produced DECIMAL(10, 2),
    waste_quantity DECIMAL(10, 2) DEFAULT 0,
    status ENUM('in-progress', 'completed', 'paused', 'stopped') DEFAULT 'in-progress',
    remark TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mo_id) REFERENCES manufacturing_orders(mo_id) ON DELETE CASCADE,
    FOREIGN KEY (operator_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_mo_id (mo_id),
    INDEX idx_operator_id (operator_id),
    INDEX idx_start_time (start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 17. QUALITY CONTROL TABLE
-- =====================================================
CREATE TABLE quality_control (
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (mo_id) REFERENCES manufacturing_orders(mo_id) ON DELETE CASCADE,
    FOREIGN KEY (inspector_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_mo_id (mo_id),
    INDEX idx_result (result),
    INDEX idx_inspector_id (inspector_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 18. NOTIFICATIONS TABLE
-- =====================================================
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('stock_alert', 'pending_mo', 'qc_hold', 'production_alert', 'system_alert') NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT,
    related_id INT,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 19. ACTIVITY LOG TABLE
-- =====================================================
CREATE TABLE activity_logs (
    activity_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    related_module VARCHAR(50),
    related_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_related_module (related_module),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 20. SESSIONS TABLE
-- =====================================================
CREATE TABLE sessions (
    session_id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
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
('QC', 'Quality control staff'),
('Purchasing', 'Purchasing department');

-- =====================================================
-- INSERT: UNITS
-- =====================================================
INSERT INTO units (unit_name, unit_symbol) VALUES
('Kilogram', 'kg'),
('Gram', 'g'),
('Liter', 'L'),
('Milliliter', 'ml'),
('Meter', 'm'),
('Centimeter', 'cm'),
('Piece', 'pcs'),
('Box', 'box'),
('Pack', 'pack'),
('Ton', 'ton');

-- =====================================================
-- INSERT: PERMISSIONS
-- =====================================================
INSERT INTO permissions (permission_name, module, action, description) VALUES
-- Product Permissions
('view_products', 'products', 'view', 'View products'),
('create_products', 'products', 'create', 'Create new products'),
('edit_products', 'products', 'edit', 'Edit existing products'),
('delete_products', 'products', 'delete', 'Delete products'),
('export_products', 'products', 'export', 'Export products'),

-- Raw Material Permissions
('view_materials', 'materials', 'view', 'View raw materials'),
('create_materials', 'materials', 'create', 'Create raw materials'),
('edit_materials', 'materials', 'edit', 'Edit raw materials'),
('delete_materials', 'materials', 'delete', 'Delete raw materials'),

-- Inventory Permissions
('view_inventory', 'inventory', 'view', 'View inventory'),
('manage_inventory', 'inventory', 'manage', 'Manage inventory operations'),
('stock_in', 'inventory', 'stock_in', 'Stock in operations'),
('stock_out', 'inventory', 'stock_out', 'Stock out operations'),

-- Manufacturing Orders
('view_mo', 'manufacturing', 'view', 'View manufacturing orders'),
('create_mo', 'manufacturing', 'create', 'Create manufacturing orders'),
('edit_mo', 'manufacturing', 'edit', 'Edit manufacturing orders'),
('approve_mo', 'manufacturing', 'approve', 'Approve manufacturing orders'),

-- Production
('view_production', 'production', 'view', 'View production records'),
('create_production', 'production', 'create', 'Create production records'),
('edit_production', 'production', 'edit', 'Edit production records'),

-- Quality Control
('view_qc', 'qc', 'view', 'View QC records'),
('create_qc', 'qc', 'create', 'Create QC records'),
('approve_qc', 'qc', 'approve', 'Approve QC results'),

-- Reports
('view_reports', 'reports', 'view', 'View reports'),
('export_reports', 'reports', 'export', 'Export reports'),

-- System
('view_audit', 'system', 'audit', 'View audit logs'),
('manage_users', 'system', 'manage_users', 'Manage system users'),
('manage_roles', 'system', 'manage_roles', 'Manage roles');

-- =====================================================
-- INSERT: ROLE PERMISSIONS (Super Admin)
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, permission_id FROM permissions;

-- =====================================================
-- INSERT: ROLE PERMISSIONS (Admin)
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions WHERE permission_name NOT IN ('manage_roles');

-- =====================================================
-- INSERT: ROLE PERMISSIONS (Manager)
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id)
SELECT 3, permission_id FROM permissions WHERE module IN ('products', 'materials', 'manufacturing', 'reports') 
AND action IN ('view', 'export');

-- =====================================================
-- INSERT: ROLE PERMISSIONS (Warehouse)
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, permission_id FROM permissions WHERE module IN ('materials', 'inventory') 
AND action IN ('view', 'stock_in', 'stock_out', 'manage');

-- =====================================================
-- INSERT: ROLE PERMISSIONS (Production)
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id)
SELECT 5, permission_id FROM permissions WHERE module IN ('production', 'manufacturing') 
AND action IN ('view', 'create', 'edit');

-- =====================================================
-- INSERT: ROLE PERMISSIONS (QC)
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id)
SELECT 6, permission_id FROM permissions WHERE module = 'qc' 
AND action IN ('view', 'create', 'approve');

-- =====================================================
-- INSERT: ROLE PERMISSIONS (Purchasing)
-- =====================================================
INSERT INTO role_permissions (role_id, permission_id)
SELECT 7, permission_id FROM permissions WHERE module IN ('materials', 'inventory') 
AND action IN ('view', 'stock_in');

-- =====================================================
-- INSERT: CATEGORIES
-- =====================================================
INSERT INTO categories (category_name, description) VALUES
('Electronics', 'Electronic products and components'),
('Mechanical', 'Mechanical parts and assemblies'),
('Textiles', 'Textile products'),
('Chemicals', 'Chemical products'),
('Plastics', 'Plastic materials and products'),
('Metals', 'Metal materials and products'),
('Food', 'Food products'),
('Packaging', 'Packaging materials');

-- =====================================================
-- INSERT: SUPPLIERS
-- =====================================================
INSERT INTO suppliers (supplier_code, supplier_name, contact_person, phone, email, address, city, state, postal_code, country) VALUES
('SUP001', 'Global Materials Inc', 'John Smith', '555-0101', 'john@globalmaterials.com', '123 Main St', 'New York', 'NY', '10001', 'USA'),
('SUP002', 'Quality Supplies Ltd', 'Jane Doe', '555-0102', 'jane@qualitysupplies.com', '456 Oak Ave', 'Los Angeles', 'CA', '90001', 'USA'),
('SUP003', 'Premium Parts Co', 'Mike Johnson', '555-0103', 'mike@premiumparts.com', '789 Pine Rd', 'Chicago', 'IL', '60601', 'USA'),
('SUP004', 'Reliable Resources', 'Sarah Williams', '555-0104', 'sarah@reliableresources.com', '321 Elm St', 'Houston', 'TX', '77001', 'USA'),
('SUP005', 'Expert Exports Ltd', 'David Brown', '555-0105', 'david@expertexports.com', '654 Maple Dr', 'Phoenix', 'AZ', '85001', 'USA');

-- =====================================================
-- INSERT: RAW MATERIALS
-- =====================================================
INSERT INTO raw_materials (rm_code, rm_name, unit_id, min_stock, max_stock, supplier_id, current_stock, price, status) VALUES
('RM001', 'Steel Plate', 1, 100, 500, 1, 250, 45.50, 'active'),
('RM002', 'Aluminum Wire', 2, 50, 200, 2, 120, 8.75, 'active'),
('RM003', 'Plastic Resin', 1, 100, 400, 3, 180, 25.00, 'active'),
('RM004', 'Rubber Component', 7, 200, 800, 4, 450, 5.50, 'active'),
('RM005', 'Electronic Chip', 7, 100, 500, 5, 280, 15.00, 'active'),
('RM006', 'Paint', 3, 50, 200, 1, 85, 12.50, 'active'),
('RM007', 'Epoxy Adhesive', 3, 30, 100, 2, 45, 18.00, 'active'),
('RM008', 'Copper Wire', 2, 100, 300, 3, 150, 22.50, 'active');

-- =====================================================
-- INSERT: PRODUCTS
-- =====================================================
INSERT INTO products (product_code, product_name, category_id, unit_id, description, specification, status, created_by) VALUES
('PRD001', 'Industrial Control Panel', 1, 7, 'Advanced control panel for machinery', 'Dimensions: 60x40x30cm, Power: 220V', 'active', 1),
('PRD002', 'Metal Frame Assembly', 2, 7, 'Structural frame for equipment', 'Material: Steel, Load capacity: 500kg', 'active', 1),
('PRD003', 'Plastic Housing Unit', 5, 7, 'Protective housing for electronics', 'Color: White, Material: ABS Plastic', 'active', 1),
('PRD004', 'Motor Component Set', 1, 8, 'Complete motor assembly parts', 'For 3HP motors', 'active', 1),
('PRD005', 'Connector Assembly', 1, 7, 'Electrical connector parts', 'Type: Industrial Grade', 'active', 1),
('PRD006', 'Cooling Unit', 2, 7, 'Heat dissipation cooling unit', 'Aluminum body, 500W capacity', 'active', 1),
('PRD007', 'Wiring Harness', 1, 7, 'Pre-assembled wire harness', 'Multi-conductor, 5m length', 'active', 1),
('PRD008', 'Composite Sheet', 5, 1, 'High-strength composite material', 'Thickness: 10mm, Density: 1.2g/cm³', 'active', 1);

-- =====================================================
-- INSERT: MANUFACTURING ORDERS
-- =====================================================
INSERT INTO manufacturing_orders (mo_no, product_id, batch_size, target_qty, produced_qty, start_date, end_date, status, priority, notes, created_by) VALUES
('MO-2026-001', 1, 50, 500, 250, '2026-06-01', '2026-06-10', 'production', 'high', 'Rush order for client A', 1),
('MO-2026-002', 2, 100, 1000, 0, '2026-06-05', '2026-06-15', 'pending', 'medium', 'Regular production', 1),
('MO-2026-003', 3, 200, 2000, 1500, '2026-05-25', '2026-06-08', 'qc', 'medium', 'On schedule', 1),
('MO-2026-004', 4, 50, 500, 500, '2026-05-15', '2026-06-02', 'finished', 'low', 'Completed early', 1),
('MO-2026-005', 5, 100, 1000, 0, '2026-06-08', '2026-06-20', 'draft', 'urgent', 'Urgent client order', 1);

-- =====================================================
-- INSERT: INVENTORY
-- =====================================================
INSERT INTO inventory (product_id, warehouse_location, quantity) VALUES
(1, 'A1', 120),
(2, 'A2', 380),
(3, 'B1', 1200),
(4, 'B2', 450),
(5, 'C1', 280),
(6, 'C2', 90),
(7, 'D1', 320),
(8, 'D2', 850);

-- =====================================================
-- INSERT: STOCK IN RECORDS
-- =====================================================
INSERT INTO stock_in (rm_id, supplier_id, quantity, unit_price, total_price, reference_no, received_by, notes) VALUES
(1, 1, 100, 45.50, 4550.00, 'PO-001', 1, 'Quality checked and stored'),
(2, 2, 50, 8.75, 437.50, 'PO-002', 1, 'All items verified'),
(3, 3, 100, 25.00, 2500.00, 'PO-003', 1, 'Bulk order received'),
(4, 4, 200, 5.50, 1100.00, 'PO-004', 1, 'Scheduled delivery'),
(5, 5, 100, 15.00, 1500.00, 'PO-005', 1, 'Component shipment');

-- =====================================================
-- INSERT: PRODUCTION RECORDS
-- =====================================================
INSERT INTO production_records (mo_id, process_step, start_time, end_time, operator_id, quantity_produced, waste_quantity, status, remark) VALUES
(1, 'Assembly', '2026-06-01 08:00:00', '2026-06-01 16:00:00', 2, 50, 2, 'completed', 'Standard process completed'),
(1, 'Testing', '2026-06-02 08:00:00', '2026-06-02 12:00:00', 3, 48, 0, 'completed', 'All tests passed'),
(3, 'Injection Molding', '2026-05-25 06:00:00', '2026-06-01 20:00:00', 2, 1500, 25, 'completed', 'Production completed'),
(4, 'Assembly', '2026-05-15 08:00:00', '2026-05-22 16:00:00', 2, 500, 5, 'completed', 'Final batch completed');

-- =====================================================
-- INSERT: QUALITY CONTROL RECORDS
-- =====================================================
INSERT INTO quality_control (mo_id, inspection_date, sample_size, defects_found, result, inspector_id, remark, approved_by) VALUES
(1, '2026-06-02 14:00:00', 50, 2, 'pass', 3, 'Minor defects, within acceptable range', 1),
(3, '2026-06-01 10:00:00', 100, 5, 'hold', 3, 'Review required for quality standards', NULL),
(4, '2026-05-23 10:00:00', 50, 0, 'pass', 3, 'Perfect batch, zero defects', 1);

-- =====================================================
-- INSERT: SYSTEM ADMIN USER
-- =====================================================
INSERT INTO users (username, email, password_hash, first_name, last_name, phone, role_id, is_active) VALUES
('admin', 'admin@productionos.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/1Ee', 'System', 'Administrator', '555-0001', 1, TRUE),
('manager1', 'manager@productionos.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/1Ee', 'John', 'Manager', '555-0002', 3, TRUE),
('operator1', 'operator@productionos.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/1Ee', 'Mike', 'Operator', '555-0003', 5, TRUE),
('qc1', 'qc@productionos.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/1Ee', 'Sarah', 'Inspector', '555-0004', 6, TRUE),
('warehouse1', 'warehouse@productionos.local', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/1Ee', 'David', 'Warehouse', '555-0005', 4, TRUE);

-- =====================================================
-- INSERT: NOTIFICATIONS
-- =====================================================
INSERT INTO notifications (user_id, type, title, message, related_id) VALUES
(1, 'stock_alert', 'Low Stock Alert', 'Steel Plate stock is below minimum level', 1),
(1, 'pending_mo', 'Pending Order', 'MO-2026-002 is waiting to start', 2),
(3, 'qc_hold', 'QC Hold', 'MO-2026-003 awaiting quality approval', 3),
(2, 'production_alert', 'Production Started', 'MO-2026-001 production in progress', 1);

-- =====================================================
-- CREATE INDEXES FOR PERFORMANCE
-- =====================================================
CREATE INDEX idx_mo_status ON manufacturing_orders(status);
CREATE INDEX idx_qc_result ON quality_control(result);
CREATE INDEX idx_inventory_qty ON inventory(quantity);
CREATE INDEX idx_stock_in_date ON stock_in(received_at);
CREATE INDEX idx_stock_out_date ON stock_out(issued_at);

-- =====================================================
-- COMMIT AND FINALIZE
-- =====================================================
-- Database setup complete!
