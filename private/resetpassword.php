<?php
ini_set('session.save_path', '/home/daintydust/php_sessions');
if (function_exists('session_start')) {
    if (!is_dir('/home/daintydust/php_sessions')) {
        @mkdir('/home/daintydust/php_sessions', 0700, true);
    }
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} else {
    error_log('session_start() unavailable under current php-fpm build');
}
require 'database.php';
require 'checklogin.php';

$token = trim($_GET['token'] ?? '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getConnection();
    $email = trim($_POST['email']);
    $userid = getUserIdFromEmail($conn, $email);
    generateResetToken($conn, $userid, $email);
}