<?php
session_start();
require 'database.php';
require 'checklogin.php';

$token = trim($_GET['token'] ?? '');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getConnection();
    $email = trim($_POST['email']);
    $userid = getUserIdFromEmail($conn, $email);
    generateResetToken($conn, $userid, $email);
}
