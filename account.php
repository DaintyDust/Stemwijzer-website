<?php require 'php_require/autoload.php'; ?>
<?php
$updateType = isset($_GET['update']) ? $_GET['update'] : '';
$error = "";
$success = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getConnection();
    if ($updateType === 'username') {
        $username = trim($_POST['username']);
        $originUsername = trim($_POST['originusername']);

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $error = "Gebruikersnaam mag alleen letters, cijfers, underscores en streepjes bevatten.";
        } elseif (empty($username) || empty($originUsername)) {
            $error = 'Vul alle velden in.';
        } elseif (strlen($username) < 3) {
            $error = 'Volledige naam moet minimaal 3 tekens lang zijn.';
        } elseif (checkUserExists($conn, $username, "")) {
            $error = 'Deze gebruikersnaam bestaat al.';
        } elseif ($error === "") {
            $updateUser_response = updateUserInfo($conn, 'username', $username, $_SESSION['UserId'], $originUsername);
            if ($updateUser_response === null) {
                $error = "Er is een fout opgetreden bij het bijwerken van de gebruikersinformatie. Probeer het opnieuw.";
            } else {
                $success = 'Gebruikersnaam succesvol bijgewerkt!';
                // header('Location: account.php');
            }
        }
    } elseif ($updateType === 'email') {
        $email = trim($_POST['email']);
        $originEmail = trim($_POST['originemail']);

        if (empty($email) || empty($originEmail)) {
            $error = 'Vul alle velden in.';
        } elseif (strlen($email) < 3) {
            $error = 'Email moet minimaal 3 tekens lang zijn.';
        } elseif (checkUserExists($conn, "", $email)) {
            $error = 'Dit e-mailadres bestaat al.';
        } elseif ($error === "") {
            $updateUser_response = updateUserInfo($conn, 'email', $email, $_SESSION['UserId'], $originEmail);
            if ($updateUser_response === null) {
                $error = "Er is een fout opgetreden bij het bijwerken van de gebruikersinformatie. Probeer het opnieuw.";
            } else {
                $success = 'Email succesvol bijgewerkt!';
                // header('Location: account.php');
            }
        }
    } elseif ($updateType === 'password') {
        if (isset($_POST['action']) && $_POST['action'] === 'openpage') {
            header('Location: account.php?update=password');
            exit;
        }

        $oldpassword = trim($_POST['oldpassword']);
        $newpassword = trim($_POST['newpassword']);
        $repeatnewpassword = trim($_POST['repeatnewpassword']);

        if (empty($oldpassword) || empty($newpassword) || empty($repeatnewpassword)) {
            $error = 'Vul alle velden in.';
        } elseif (strlen($newpassword) < 6) {
            $error = 'Nieuwe wachtwoord moet minimaal 6 tekens lang zijn.';
        } elseif ($oldpassword === $newpassword) {
            $error = 'Nieuwe wachtwoord en oude wachtwoord mogen niet hetzelfde zijn.';
        } elseif ($newpassword !== $repeatnewpassword) {
            $error = 'Nieuwe wachtwoord en herhaal nieuwe wachtwoord komen niet overeen.';
        } elseif (!password_verify($oldpassword, getUserInfo($conn, $_SESSION["UserId"])['wachtwoord_hash'])) {
            $error = 'Oude wachtwoord is onjuist.';
        } elseif ($error === "") {
            $updateUser_response = updateUserInfo($conn, 'password', $newpassword, $_SESSION['UserId'], "");
            if ($updateUser_response === null) {
                $error = "Er is een fout opgetreden bij het bijwerken van de gebruikersinformatie. Probeer het opnieuw.";
            } else {
                $success = 'Wachtwoord succesvol bijgewerkt!';
                // header('Location: account.php');
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
    <title>Neutraal Kieslab - Account</title>
    <link rel="stylesheet" href="styles/account.css">
    <script src="js/account.js"></script>
</head>

<body>
    <?php require 'php_require/header.php'; ?>
    <div id="maincontent">
        <div id="account-information">
            <h1>Account</h1>
            <div class="flex maxsize seperateflex">
                <section id="profile-picture-container">
                    <?php
                    $profileImage = $_SESSION['ProfilePicture'] ?? '';
                    if (empty($profileImage) || str_contains($profileImage, 'example.com')) {
                        $profileImage = 'images/DefaultProfile.svg';
                    } else {
                        $profileImage = 'pfpUploads/' . htmlspecialchars($profileImage);
                    }
                    ?>
                    <img class="Profile-Picture" src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Image">
                    <label for="profile_picture">Profielfoto Veranderen</label>
                    <input type="file" name="profile_picture" id="profile_picture" accept=".jpg,.jpeg,.png,.webp,.svg">
                </section>
                <?php if ($updateType !== "password"): ?>
                    <div id="account-details">
                        <label>Gebruikersnaam:</label>
                        <form action="?update=username" method="post" class="input-container">
                            <div class="edit-icon"></div>
                            <input type="text" name="username" value="<?php echo $_SESSION['Username']; ?>" required>
                            <input type="hidden" name="originusername" value="<?php echo $_SESSION['Username']; ?>">
                            <button type="submit" class="small">Wijzigen</button>
                        </form>
                        <label>E-mailadres:</label>
                        <form action="?update=email" method="post" class="input-container">
                            <div class="edit-icon"></div>
                            <input type="email" name="email" value="<?php echo $_SESSION['Email']; ?>" required>
                            <input type="hidden" name="originemail" value="<?php echo $_SESSION['Email']; ?>">
                            <button type="submit" class="small">Wijzigen</button>
                        </form>
                        <label>Wachtwoord:</label>
                        <form action="?update=password" method="post" class="input-container">
                            <div class="edit-icon"></div>
                            <input type="password" name="password" value="TemplatePassword" disabled>
                            <input type="hidden" name="action" value="openpage">
                            <button type="submit" class="small">Wijzigen</button>
                        </form>
                        <form action="login.php?resetpassword" method="post">
                            <button type="submit">Reset Wachtwoord</button>
                        </form>
                        <?php if (isset($error) && $error != "") {
                            echo "<p class='error-message'>$error</p>";
                        } elseif (isset($success) && $success != "") {
                            echo "<p class='success-message'>$success</p>";
                        } ?>
                    </div>
                <?php else: ?>
                    <div id="account-details">
                        <a href="account.php" id="back-button"><img src="images/arrow-right.svg" alt="Back Arrow">Terug naar Account</a>
                        <form id="update-password-form" action="?update=password" method="post" class="input-container">
                            <label for="oldpassword">Oude wachtwoord:</label>
                            <input type="password" name="oldpassword" placeholder="Oude wachtwoord" required>
                            <label for="newpassword">Nieuwe wachtwoord:</label>
                            <input type="password" name="newpassword" placeholder="Nieuwe wachtwoord" required>
                            <label for="repeatnewpassword">Herhaal nieuwe wachtwoord:</label>
                            <input type="password" name="repeatnewpassword" placeholder="Herhaal nieuwe wachtwoord" required>
                            <button type="submit">Verander Wachtwoord</button>
                        </form>
                        <?php if (isset($error) && $error != "") {
                            echo "<p class='error-message'>$error</p>";
                        } elseif (isset($success) && $success != "") {
                            echo "<p class='success-message'>$success</p>";
                        } ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
    <?php require 'php_require/footer.php'; ?>
</body>

</html>