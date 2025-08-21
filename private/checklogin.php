<?php

isLoggedIn();
function isLoggedIn()
{
    $excludedPages = ['login.php', 'resetpassword.php'];

    $loggedIn = isset($_SESSION['UserId']) && !empty($_SESSION['UserId'])
        && isset($_SESSION['Username']) && !empty($_SESSION['Username']);

    if (!$loggedIn) {
        $currentPage = basename($_SERVER['PHP_SELF']);
        if (!in_array($currentPage, $excludedPages)) {
            header('Location: login.php');
            exit();
        }
    }

    return $loggedIn;
}

function loginUser($user)
{
    $_SESSION['UserId'] = $user['id'];
    $_SESSION['Username'] = $user['naam'];
    $_SESSION['Email'] = $user['email'];
    $_SESSION['ProfilePicture'] = $user['profielfoto'] ?? null;
}

function logoutUser()
{
    if (isLoggedIn()) {
        session_unset();
        session_destroy();
    }
}

// echo "User is logged in: " . (isLoggedIn() ? "Yes" : "No") . "\n";