-- 1. Create Users Table (For "Data Pengguna")
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    role VARCHAR(20) DEFAULT 'admin',
    status ENUM('Active', 'Inactive') DEFAULT 'Active'
);

-- 2. Create Members Table (For "Data Anggota")
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    phone VARCHAR(15),
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    joined_date DATE
);

-- 3. Create Transactions Table (For "Pinjaman", "Simpanan", "Kas")
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    type ENUM('loan_out', 'loan_pay', 'saving_in', 'saving_out', 'expense') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    trans_date DATE NOT NULL,
    description VARCHAR(255)
);

-- ==========================================
-- INSERT DUMMY DATA (To make the dashboard look alive)
-- ==========================================

-- Insert 4 Users (Matches screenshot: "4 User Aktif")
INSERT INTO users (username, status) VALUES 
('admin', 'Active'), ('staff1', 'Active'), ('staff2', 'Active'), ('manager', 'Active');

-- Insert some Members
INSERT INTO members (name, status, joined_date) VALUES 
('Budi Santoso', 'Active', '2024-01-10'),
('Siti Aminah', 'Active', '2024-02-15'),
('Joko Anwar', 'Inactive', '2023-11-20'),
('Rina Nose', 'Active', '2025-12-01');

-- Insert Transactions for December 2025
-- Simpanan (Savings) - Matches screenshot Green Card
INSERT INTO transactions (member_id, type, amount, trans_date, description) VALUES 
(1, 'saving_in', 1500000, '2025-12-01', 'Simpanan Wajib'),
(2, 'saving_in', 1500000, '2025-12-05', 'Simpanan Pokok');

-- Kas Awal (Initial Cash) - Matches screenshot Purple Card
INSERT INTO transactions (member_id, type, amount, trans_date, description) VALUES 
(0, 'saving_in', 40000000, '2025-12-01', 'Modal Awal Koperasi');