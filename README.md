# 🏦 DIKOMETA  
Digital Cooperative Management System

![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

DIKOMETA is a lightweight, web-based management system designed for **Cooperatives (Koperasi)**.  
It helps manage **members**, **savings**, **loans**, **expenses**, and provides **real-time financial reports**.

Built with **Native PHP** and **MySQL**, this project is simple, fast, and suitable for learning or small cooperative use.

---

## ✨ Features

### 📊 Dashboard
- Displays total savings (net)
- Displays outstanding loans
- Displays cash balance (Kas)
- Live clock based on user timezone
- Summary widgets for members, borrowers, and users

### 👥 Member Management
- Add new members
- Edit member information
- View detailed member profile
- Delete members
- Activate / Deactivate members

### 💰 Transaction System
- Savings:
  - Deposit (Simpanan Masuk)
  - Withdrawal (Penarikan)
- Loans:
  - Loan disbursement (Pencairan)
  - Installment payment (Bayar Cicilan)
- Expenses:
  - Record operational expenses

### 🔐 Security & Roles
- Role-Based Access Control (RBAC)
  - Admin: Full access
  - Staff: Limited access
- Session-based authentication
- Password hashing using MD5

---

## 🚀 Installation Guide (Step by Step)

### 🔧 Prerequisites
Before starting, make sure you have:
1. XAMPP / WAMP / MAMP installed
2. PHP version **8.0 or higher**
3. MySQL / MariaDB
4. Git installed
5. Web browser (Chrome, Firefox, Edge)

---

### 📁 Step 1: Download or Clone the Project

#### Option A: Using Git (Recommended)
1. Open **Command Prompt / Git Bash**
2. Navigate to your web server folder:

```bash
cd C:\xampp\htdocs
```

3. Clone the repository:

```bash
git clone https://github.com/Ankushred/dikometa.git
```

#### Option B: Download ZIP
1. Go to the GitHub repository
2. Click **Code → Download ZIP**
3. Extract the ZIP file
4. Move the folder to:

```
C:\xampp\htdocs\dikometa
```

---

### 🗄️ Step 2: Start Local Server
1. Open **XAMPP Control Panel**
2. Start:
   - Apache
   - MySQL
3. Make sure both services show **Running** status

---

### 🗃️ Step 3: Create Database
1. Open browser
2. Go to:
```
http://localhost/phpmyadmin
```
3. Click **New**
4. Enter database name:
```
dikometa_db
```
5. Click **Create**

---

### 📥 Step 4: Import Database
1. Select database **dikometa_db**
2. Click **Import**
3. Click **Choose File**
4. Select `database.sql` from project root
5. Click **Go**
6. Wait until success message appears

---

### ⚙️ Step 5: Configure Database Connection
1. Open project folder:
```
C:\xampp\htdocs\dikometa
```
2. Find file:
```
config.example.php
```
3. Rename it to:
```
config.php
```
4. Open `config.php` with text editor
5. Verify or edit settings:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dikometa_db";
```

6. Save the file

---

### ▶️ Step 6: Run the Application
1. Open browser
2. Visit:
```
http://localhost/dikometa
```
3. Login using default credentials

---

## 🔑 Default Login Credentials

| Role | Username | Password | Access |
|-----|---------|----------|--------|
| Admin | admin | admin0 | Full |
| Staff | staff | admin0 | Limited |

⚠️ **Important:** Change passwords after first login.

---

## 📁 Project Structure

```plaintext
dikometa/
├── assets/              # CSS, JS, images
├── auth.php             # Authentication & session
├── index.php            # Dashboard
├── anggota.php          # Member list
├── anggota_tambah.php   # Add member
├── anggota_detail.php   # Member details
├── users.php            # User management (Admin)
├── users_add.php        # Add staff (Admin)
├── transaksi_tambah.php # Add transaction
├── transaksi_proses.php # Transaction logic
├── laporan.php          # Reports
├── config.php           # Database config
├── database.sql         # Database schema
└── README.md            # Documentation
```

---

## ❗ Common Issues & Solutions

- **Database connection failed**
  - Check `config.php`
  - Make sure MySQL is running

- **Page not found**
  - Ensure project is inside `htdocs`
  - Check URL spelling

- **Login not working**
  - Re-import database
  - Check default credentials

---

## 🤝 Contributing
1. Fork the repository
2. Create a new branch
3. Commit your changes
4. Submit a Pull Request

---

## 📄 License
This project is open-source and free for **educational purposes only**.

