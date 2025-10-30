# user-management

📦 Sistem Manajemen Pengguna & Gudang
Sistem manajemen pengguna dengan fitur lengkap untuk administrasi gudang, termasuk CRUD produk, manajemen user, dan sistem keamanan yang komprehensif.

🌟 Fitur Utama
🔐 Sistem Autentikasi
Registrasi User dengan validasi email unik

Aktivasi Akun melalui link email

Login/Logout dengan session management

Lupa Password dengan reset token

Ubah Password dengan validasi keamanan

📊 Manajemen Produk (CRUD Lengkap)
Create - Tambah produk baru

Read - Lihat daftar dan detail produk

Update - Edit informasi produk

Delete - Hapus produk dengan konfirmasi

Stock Management - Management stok dengan alert system

Export & Print - Ekspor data dan cetak laporan

👤 Manajemen User
Profil User - Edit informasi pribadi

Dashboard - Ringkasan statistik dan quick actions

Security - Sistem keamanan multi-layer

🚀 Teknologi yang Digunakan
Backend: PHP Native

Database: MySQL

Frontend: HTML5, CSS3, JavaScript

Security: Password hashing, Session management, SQL injection protection

Styling: Custom CSS dengan design modern dan responsive

📋 Prerequisites
Sebelum instalasi, pastikan Anda memiliki:

XAMPP atau web server dengan PHP & MySQL

PHP 7.4 atau lebih tinggi

MySQL 5.7 atau lebih tinggi

Browser modern (Chrome, Firefox, Safari)

🛠️ Instalasi & Setup
Step 1: Download dan Install XAMPP
bash
1. Download XAMPP dari: https://www.apachefriends.org/
2. Install dengan setting default
3. Simpan di C:\xampp (Windows) atau /opt/lampp (Linux)
Step 2: Setup Project Structure
bash
# Buat folder project di htdocs
C:\xampp\htdocs\user-management\

# Atau clone dari GitHub (jika tersedia)
git clone [repository-url]
cd user-management
Step 3: Setup Database
Metode 1: Via phpMyAdmin
sql
1. Buka http://localhost/phpmyadmin
2. Klik "New" → Buat database: "user_management"
3. Import file user_management.sql yang tersedia
Metode 2: Manual SQL
Jalankan query SQL berikut di phpMyAdmin atau MySQL client:

sql
CREATE DATABASE user_management;
USE user_management;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    activation_token VARCHAR(255),
    reset_token VARCHAR(255),
    is_active TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- User sample (password: password123)
INSERT INTO users (email, password, full_name, is_active) 
VALUES ('admin@gudang.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Gudang', 1);
Step 4: Konfigurasi Database
Edit file config/database.php dan sesuaikan dengan environment Anda:

php
<?php
$host = 'localhost';
$dbname = 'user_management';
$username = 'root';      // Default XAMPP
$password = '';          // Default XAMPP (kosong)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
Step 5: Start Server
bash
1. Buka XAMPP Control Panel
2. Start "Apache" dan "MySQL"
3. Pastikan status berwarna HIJAU
Step 6: Akses Aplikasi
bash
Buka browser dan akses: http://localhost/user-management/
👤 Akun Default untuk Testing
text
Email: admin@gudang.com
Password: password123
📖 Cara Penggunaan
1. Registrasi User Baru
Buka halaman registrasi

Isi form dengan data lengkap

Cek folder emails untuk link aktivasi (simulasi email)

Klik link aktivasi untuk mengaktifkan akun

2. Login ke Sistem
Masukkan email dan password

Akses dashboard setelah login berhasil

3. Manajemen Produk
Tambah Produk: Gunakan form di halaman products

Edit Produk: Klik tombol ✏️ pada produk yang ingin diedit

Lihat Detail: Klik tombol 👁️ untuk melihat detail lengkap

Hapus Produk: Klik tombol 🗑️ dengan konfirmasi

4. Manajemen Profil
Edit Profil: Ubah nama lengkap di halaman profil

Ubah Password: Ganti password dengan validasi keamanan

5. Fitur Lupa Password
Klik "Lupa Password" di halaman login

Masukkan email terdaftar

Cek folder emails untuk link reset password

Buat password baru melalui link tersebut

🗂️ Struktur File
text
user-management/
├── 📄 index.php                 # Redirect ke login
├── 📄 login.php                 # Halaman login
├── 📄 register.php              # Halaman registrasi
├── 📄 activate.php              # Aktivasi akun
├── 📄 forgot_password.php       # Lupa password
├── 📄 reset_password.php        # Reset password
├── 📄 dashboard.php             # Dashboard utama
├── 📄 profile.php               # Manajemen profil
├── 📄 change_password.php       # Ubah password
├── 📄 products.php              # CRUD produk (utama)
├── 📄 export_products.php       # Ekspor data Excel
├── 📄 print_products.php        # Cetak laporan
├── 📄 logout.php                # Logout system
├── 📁 config/
│   └── 📄 database.php          # Konfigurasi database
├── 📁 includes/
│   ├── 📄 functions.php         # Fungsi helper
│   ├── 📄 auth.php              # Authentication middleware
│   └── 📄 header.php            # Template header
├── 📁 assets/
│   └── 📁 css/
│       └── 📄 style.css         # Styling utama
└── 📁 emails/                   # Simulasi penyimpanan email
🔒 Fitur Keamanan
Password Hashing: Menggunakan password_hash() dan password_verify()

SQL Injection Protection: Prepared statements dengan PDO

XSS Protection: Input sanitization dengan htmlspecialchars()

Session Management: System login dengan session validation

Email Verification: Aktivasi akun melalui email

CSRF Protection (basic): Form validation

🎨 Fitur UI/UX
Responsive Design: Tampilan optimal di desktop dan mobile

Modern Interface: Clean design dengan gradient background

Interactive Elements: Hover effects dan animations

Real-time Validation: Form validation dengan JavaScript

Alert System: Notifikasi sukses dan error

Loading States: Feedback selama proses

🚀 Deployment
Local Development
bash
1. Pastikan XAMPP/LAMPP berjalan
2. Akses http://localhost/user-management/
3. Gunakan akun default atau registrasi baru
Production Deployment
Upload files ke web hosting dengan PHP & MySQL support

Buat database dan import SQL schema

Update konfigurasi database di config/database.php

Set folder permissions untuk folder emails/

Test semua fitur secara menyeluruh

🐛 Troubleshooting
Common Issues:
Database Connection Error

Pastikan MySQL berjalan di XAMPP

Cek kredensial database di config/database.php

Pastikan database user_management sudah dibuat

Session Issues

Pastikan tidak ada output sebelum session_start()

Cek folder permissions untuk session storage

Email Simulation

Folder emails/ harus ada dan writable

Cek file email di folder emails/ untuk link aktivasi/reset

Page Not Loading

Pastikan Apache berjalan di XAMPP

Cek path folder di htdocs/user-management/

Restart XAMPP jika diperlukan

📞 Support
Jika mengalami masalah:

Cek section Troubleshooting di atas

Pastikan semua prerequisites terpenuhi

Verifikasi konfigurasi database

Check error logs di XAMPP untuk detail error

📄 License
Project ini dibuat untuk keperluan educational dan dapat dikembangkan lebih lanjut.

Selamat menggunakan Sistem Manajemen Pengguna & Gudang! 🎉
