# user-management

Sistem Manajemen Gudang
Sistem manajemen gudang dengan fitur user management dan CRUD produk yang lengkap.

✨ Fitur
✅ Registrasi & Login dengan aktivasi email
✅ Lupa Password dengan sistem reset
✅ CRUD Produk lengkap (Create, Read, Update, Delete)
✅ Manajemen Stok dengan alert system
✅ Export & Print laporan produk
✅ Profil User & ubah password
✅ Dashboard dengan statistik

🚀 Instalasi Cepat
1. Setup Environment
bash
# Download XAMPP: https://www.apachefriends.org/
# Install dan start Apache + MySQL

2. Setup Project
bash
# Extract project ke: C:\xampp\htdocs\user-management\
# Atau clone repository

3. Setup Database
sql
1. Buka http://localhost/phpmyadmin
2. Buat database: user_management  
3. Import file user_management.sql
4. Konfigurasi

Edit config/database.php:
php
$host = 'localhost';
$dbname = 'user_management'; 
$username = 'root';
$password = '';

5. Akses Aplikasi
text
http://localhost/user-management/
👤 Login Default
text
Email: admin@gudang.com
Password: password123

📱 Cara Penggunaan
Registrasi → Aktivasi via email (cek folder emails/)
Login → Akses dashboard
Kelola Produk → Tambah, edit, hapus produk
Manajemen Stok → Pantau stok dengan alert system
Export Data → Ekspor ke Excel atau cetak laporan

🛠️ Teknologi
PHP Native
MySQL
HTML5, CSS3, JavaScript
PDO untuk keamanan database

📞 Support
Pastikan:
✅ XAMPP berjalan (Apache + MySQL)
✅ Database user_management sudah dibuat
✅ Folder emails/ ada dan writable
