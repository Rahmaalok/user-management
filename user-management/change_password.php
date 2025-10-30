<?php
require_once 'includes/auth.php';

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$error = '';
$success = '';

// Ubah password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi input
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif (!password_verify($current_password, $user['password'])) {
        $error = "Password saat ini salah!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password baru tidak cocok!";
    } elseif (strlen($new_password) < 6) {
        $error = "Password baru minimal 6 karakter!";
    } else {
        // Hash password baru
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password di database
        $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        
        if ($stmt->execute([$hashedPassword, $_SESSION['user_id']])) {
            $success = "Password berhasil diubah!";
            
            // Clear form fields
            $_POST = array();
        } else {
            $error = "Terjadi kesalahan saat mengubah password. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password - Sistem Manajemen Gudang</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .password-form {
            max-width: 500px;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 14px;
        }
        
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
        
        .form-requirements {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #6c757d;
        }
        
        .form-requirements ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .form-requirements li {
            margin-bottom: 8px;
            transition: color 0.3s;
        }
        
        .requirements-met {
            color: #28a745;
            font-weight: bold;
        }
        
        .requirements-not-met {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Ubah Password</h2>
        <div class="user-info">
            <span>Halo, <?= htmlspecialchars($_SESSION['user_name']) ?></span> | 
            <a href="dashboard.php">Dashboard</a> | 
            <a href="profile.php">Profil</a> | 
            <a href="products.php">Produk</a> | 
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="breadcrumb">
            <a href="dashboard.php">Dashboard</a> &raquo; <a href="profile.php">Profil</a> &raquo; Ubah Password
        </div>
        
        <h3>Ubah Password</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert error">
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert success">
                <strong>Sukses:</strong> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <div class="form-requirements">
            <h4>Persyaratan Password:</h4>
            <ul>
                <li id="req-length" class="requirements-not-met">Minimal 6 karakter</li>
                <li id="req-match" class="requirements-not-met">Password baru dan konfirmasi harus cocok</li>
                <li id="req-different" class="requirements-not-met">Password baru harus berbeda dengan password lama</li>
            </ul>
        </div>
        
        <form method="POST" class="password-form">
            <div class="form-group">
                <label for="current_password">Password Saat Ini:</label>
                <input type="password" id="current_password" name="current_password" required 
                       value="<?= isset($_POST['current_password']) ? htmlspecialchars($_POST['current_password']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="new_password">Password Baru:</label>
                <input type="password" id="new_password" name="new_password" required 
                       value="<?= isset($_POST['new_password']) ? htmlspecialchars($_POST['new_password']) : '' ?>"
                       onkeyup="checkPasswordStrength()">
                <div id="password-strength" class="password-strength"></div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru:</label>
                <input type="password" id="confirm_password" name="confirm_password" required 
                       value="<?= isset($_POST['confirm_password']) ? htmlspecialchars($_POST['confirm_password']) : '' ?>"
                       onkeyup="checkPasswordMatch()">
                <div id="password-match" class="password-strength"></div>
            </div>
            
            <div class="form-actions">
                <button type="submit" id="submit-btn">Ubah Password</button>
                <a href="profile.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
        
        <div class="security-tips">
            <h4>Tips Keamanan Password:</h4>
            <ul>
                <li>Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol</li>
                <li>Jangan gunakan informasi pribadi yang mudah ditebak</li>
                <li>Gunakan password yang berbeda untuk setiap akun</li>
                <li>Pertimbangkan untuk menggunakan password manager</li>
            </ul>
        </div>
    </div>

    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('new_password').value;
            const strengthText = document.getElementById('password-strength');
            const reqLength = document.getElementById('req-length');
            const reqDifferent = document.getElementById('req-different');
            const currentPassword = document.getElementById('current_password').value;
            
            // Check length
            if (password.length >= 6) {
                reqLength.className = 'requirements-met';
            } else {
                reqLength.className = 'requirements-not-met';
            }
            
            // Check if different from current password
            if (password !== currentPassword) {
                reqDifferent.className = 'requirements-met';
            } else {
                reqDifferent.className = 'requirements-not-met';
            }
            
            // Calculate strength
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    strengthText.innerHTML = 'Kekuatan: <span class="strength-weak">Lemah</span>';
                    break;
                case 2:
                case 3:
                    strengthText.innerHTML = 'Kekuatan: <span class="strength-medium">Sedang</span>';
                    break;
                case 4:
                case 5:
                    strengthText.innerHTML = 'Kekuatan: <span class="strength-strong">Kuat</span>';
                    break;
            }
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchText = document.getElementById('password-match');
            const reqMatch = document.getElementById('req-match');
            
            if (password && confirmPassword) {
                if (password === confirmPassword) {
                    matchText.innerHTML = '<span class="strength-strong">Password cocok</span>';
                    reqMatch.className = 'requirements-met';
                } else {
                    matchText.innerHTML = '<span class="strength-weak">Password tidak cocok</span>';
                    reqMatch.className = 'requirements-not-met';
                }
            } else {
                matchText.innerHTML = '';
                reqMatch.className = 'requirements-not-met';
            }
        }
        
        // Initialize validation on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to all password fields
            document.getElementById('current_password').addEventListener('keyup', function() {
                checkPasswordStrength();
            });
            document.getElementById('new_password').addEventListener('keyup', function() {
                checkPasswordStrength();
                checkPasswordMatch();
            });
            document.getElementById('confirm_password').addEventListener('keyup', checkPasswordMatch);
        });
    </script>
</body>
</html>