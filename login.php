<?php
require 'php_require/autoload.php';

$isCreate = isset($_GET['create']);
$isResetPassword = isset($_GET['resetpassword']);
$hasResetToken = isset($_GET['token']) && isset($_GET['resetpassword']);
$token = $_GET['token'] ?? '';

$error = "";
$success = "";
$username = "";
$email = "";

$mode = $isCreate ? 'create' : ($isResetPassword ? 'reset' : 'login');

if ($mode !== 'reset' && isLoggedIn()) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getConnection();
    if ($mode === 'create') {
        $email = trim($_POST['email']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $error = "Gebruikersnaam mag alleen letters, cijfers, underscores en streepjes bevatten.";
        } elseif (empty($username) || empty($email) || empty($password)) {
            $error = 'Vul alle velden in.';
        } elseif (strlen($username) < 3) {
            $error = 'Volledige naam moet minimaal 2 tekens lang zijn.';
        } elseif (strlen($password) < 6) {
            $error = 'Wachtwoord moet minimaal 6 tekens lang zijn.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Voer een geldig e-mailadres in.';
        } elseif (checkUserExists($conn, $username, $email)) {
            $error = 'Gebruikersnaam of e-mailadres bestaat al.';
        } elseif ($error === "") {
            $createUser_response = CreateUser($conn, $username, $email, $password);
            if ($createUser_response === null) {
                $error = "Er is een fout opgetreden bij het aanmaken van het account. Probeer het opnieuw.";
            } else {
                $success = 'Account succesvol aangemaakt! Je kunt nu inloggen.';
                header("Location: login.php");
            }
        }
    } elseif ($mode === 'login') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if ($error === "") {
            $login_response = authenticateUser($conn, $username, $password);
            if ($login_response === false) {
                $error = "Ongeldige gebruikersnaam of wachtwoord.";
            } else {
                loginUser($login_response);
                header("Location: index.php");
                exit();
            }
        }
    } elseif ($mode === 'reset') {
        if ($hasResetToken) {
            $tokenFromPost  = $_POST['token'];
            $password = $_POST['password'];
            $repeat_password = $_POST['repeatpassword'];

            if (empty($password) || empty($repeat_password)) {
                $error = 'Vul alle velden in.';
            } elseif (strlen($password) < 6) {
                $error = 'Nieuwe wachtwoord moet minimaal 6 tekens lang zijn.';
            } elseif ($password !== $repeat_password) {
                $error = 'Nieuwe wachtwoord en herhaal nieuwe wachtwoord komen niet overeen.';
            } elseif ($error === "") {
                if (checkResetToken($conn, $tokenFromPost)) {
                    $email = getEmailFromResetToken($conn, $tokenFromPost);
                    $userid = getUserIdFromEmail($conn, $email);
                    $updateUser_response = updateUserInfo($conn, 'password', $password, $userid, "");
                    $resetToken_response = removeResetToken($conn, $userid);
                    if ($updateUser_response === false || $resetToken_response === false) {
                        $error = "Er is een fout opgetreden bij het bijwerken van de gebruikersinformatie. Probeer het opnieuw.";
                    } else {
                        $success = 'Wachtwoord succesvol bijgewerkt!';
                        // header('Location: account.php');
                    }
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Neutraal Kieslab - Inloggen</title>
    <link rel="stylesheet" href="styles/login.css">
</head>

<body>
    <?php require 'php_require/header.php'; ?>
    <div id="maincontent">
        <div id="account-creation">
            <?php if ($mode === "create"): ?>
                <h1>Account aanmaken</h1>
                <form action="?create" method="post">
                    <input type="text" id="username" name="username" placeholder="Voer Gebruikersnaam in" value="<?= $username ?>" required>
                    <input type="email" id="email" name="email" placeholder="Voer E-mailadres in" value="<?= $email ?>" required>
                    <input type="password" id="password" name="password" placeholder="Voer Wachtwoord in" required>
                    <button type="submit">Account aanmaken</button>
                    <?php if (isset($error) && $error != "") {
                        echo "<p class='error-message'>$error</p>";
                    } elseif (isset($success) && $success != "") {
                        echo "<p class='success-message'>$success</p>";
                    } ?>
                    <p>Heb je al een account? <a href="login.php">Log hier in</a></p>
                </form>
            <?php elseif ($mode === "login"): ?>
                <h1>Inloggen</h1>
                <form action="login.php" method="post">
                    <input type="text" id="username" name="username" placeholder="Voer Gebruikersnaam of Email in" required>
                    <input type="password" id="password" name="password" placeholder="Voer Wachtwoord in" required>
                    <a href="login.php?resetpassword">Wachtwoord vergeten?</a>
                    <button type="submit">Inloggen</button>
                    <?php if (isset($error) && $error != "") {
                        echo "<p class='error-message'>$error</p>";
                    } elseif (isset($success) && $success != "") {
                        echo "<p class='success-message'>$success</p>";
                    } ?>
                    <p>Nog geen account? <a href="login.php?create">Maak hier een account aan</a></p>
                </form>
            <?php elseif ($mode === "reset"): ?>
                <?php if ($hasResetToken): ?>
                    <h1>Wachtwoord resetten</h1>
                    <form method="post">
                        <input type="password" id="password" name="password" placeholder="Voer nieuw Wachtwoord in" required>
                        <input type="password" id="repeatpassword" name="repeatpassword" placeholder="Herhaal nieuw Wachtwoord" required>
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                        <button type="submit">Wachtwoord resetten</button>
                        <?php if (isset($error) && $error != "") {
                            echo "<p class='error-message'>$error</p>";
                        } elseif (isset($success) && $success != "") {
                            echo "<p class='success-message'>$success</p>";
                        } ?>
                        <p>Heb je al een account? <a href="login.php">Log hier in</a></p>
                    </form>
                <?php else: ?>
                    <h1>Wachtwoord vergeten</h1>
                    <button onclick="history.back()" id="back-button"><img src="images/arrow-right.svg" alt="Back Arrow">Terug</button>
                    <form action="private/resetpassword.php" method="post">
                        <input type="email" id="email" name="email" placeholder="Voer E-mailadres in" required>
                        <button type="submit">Wachtwoord resetten</button>
                        <?php if (isset($error) && $error != "") {
                            echo "<p class='error-message'>$error</p>";
                        } elseif (isset($success) && $success != "") {
                            echo "<p class='success-message'>$success</p>";
                        } ?>
                        <?php if (!isset($_SESSION['UserId'])): ?>
                            <div class="flex gap">
                                <a href="login.php">Login</a>
                                <a href="login.php?create">Account aanmaken</a>
                            </div>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php require 'php_require/footer.php'; ?>
</body>

</html>