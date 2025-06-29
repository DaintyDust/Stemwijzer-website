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

    if (isset($_POST['action']) && $_POST['action'] === 'delete') {

        $commentId = $_POST['commentId'];
        $authorId = $_POST['authorId'];
        $currentUserId = $_SESSION['UserId'];

        if ($authorId == $currentUserId) {
            $result = deleteComment($conn, $commentId, $currentUserId);
            echo json_encode(['success' => $result, 'message' => $result ? 'Comment deleted successfully' : 'Failed to delete comment']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        }
        exit();
    }

    if (isset($_POST['action']) && $_POST['action'] === 'edit') {

        $commentId = $_POST['commentId'];
        $authorId = $_POST['authorId'];
        $currentUserId = $_SESSION['UserId'];

        if ($authorId == $currentUserId) {
            $result = editComment($conn, $commentId, $currentUserId, $_POST['commentText']);
            echo json_encode(['success' => $result, 'message' => $result ? 'Comment edited successfully' : 'Failed to edit comment']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unauthorized ' . $authorId . ' ' . $currentUserId]);
        }
        exit();
    }

    $comment = trim($_POST['comment']);
    $articleId = $_POST['articleId'];
    $result = postComment($conn, $articleId, $comment);
    echo json_encode(['success' => $result, 'message' => $result ? 'Comment posted successfully' : 'Failed to post comment']);
    exit();
}
