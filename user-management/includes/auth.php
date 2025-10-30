<?php
require_once 'functions.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = "Silakan login terlebih dahulu";
    redirect('login.php');
}
?>