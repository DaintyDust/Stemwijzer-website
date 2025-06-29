<?php
session_start();
require 'database.php';
require 'checklogin.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getConnection();
    header('Content-Type: application/json');

    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }

    if (isset($_POST['action']) && $_POST['action'] === 'pfp') {
        if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Bestand upload error']);
            exit();
        }
        $oldPfp = $_SESSION['ProfilePicture'] ?? '';
        $fileName = htmlspecialchars($_FILES['profile_picture']["name"]);
        $currentUserId = $_SESSION['UserId'];

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $tempName = $_FILES["profile_picture"]["tmp_name"];
        $targetPath = "../pfpUploads/" . $fileName;

        if (in_array($ext, $allowedExtensions)) {
            if (move_uploaded_file($tempName, $targetPath)) {
                $result = updateUserInfo($conn, "pfp", $fileName, $currentUserId, $oldPfp);
                echo json_encode(['success' => $result, 'message' => $result ? 'Profile picture updated successfully' : 'Failed to update profile picture']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Bestand kon niet worden geüpload']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Ongeldig bestandstype']);
        }
        exit();
    }
}