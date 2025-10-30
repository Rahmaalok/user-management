<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function sendActivationEmail($email, $token) {
    $activationLink = "http://localhost/user-management/activate.php?token=$token";
    
    // Simulasi pengiriman email - buat file txt
    $emailContent = "Halo,\n\n";
    $emailContent .= "Terima kasih telah mendaftar di Sistem Manajemen Gudang.\n";
    $emailContent .= "Silakan klik link berikut untuk mengaktifkan akun Anda:\n";
    $emailContent .= "$activationLink\n\n";
    $emailContent .= "Link ini berlaku selama 24 jam.\n\n";
    $emailContent .= "Salam,\nAdmin Sistem";
    
    // Simpan ke file (simulasi email)
    if (!file_exists('emails')) {
        mkdir('emails', 0777, true);
    }
    file_put_contents("emails/activation_$email.txt", $emailContent);
    
    return true;
}

function sendResetEmail($email, $token) {
    $resetLink = "http://localhost/user-management/reset_password.php?token=$token";
    
    // Simulasi pengiriman email - buat file txt
    $emailContent = "Halo,\n\n";
    $emailContent .= "Anda menerima email ini karena meminta reset password.\n";
    $emailContent .= "Silakan klik link berikut untuk reset password:\n";
    $emailContent .= "$resetLink\n\n";
    $emailContent .= "Link ini berlaku selama 1 jam.\n";
    $emailContent .= "Jika Anda tidak meminta reset, abaikan email ini.\n\n";
    $emailContent .= "Salam,\nAdmin Sistem";
    
    // Simpan ke file (simulasi email)
    if (!file_exists('emails')) {
        mkdir('emails', 0777, true);
    }
    file_put_contents("emails/reset_$email.txt", $emailContent);
    
    return true;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>