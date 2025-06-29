<header>
    <div class="left">
        <img id="logo" src="images/logo-neutraal-kieslab-lichtblauw.svg" alt="Neutraal Kieslab Logo">
        <img id="logo-text" src="images/neutraal-kieslab-text.svg" alt="Neutraal Kieslab Logo">
    </div>
    <div class="right">
        <button id="dark-mode">
            <img src="images/sun.svg" alt="Dark Mode Icon">
        </button>
        <?php if (isset($_SESSION['Username'])) { ?>
            <div id="account-dropdown-container">
                <button id="account-button">
                    <span><?php echo htmlspecialchars($_SESSION['Username']); ?></span>
                    <?php
                    $profileImage = $_SESSION['ProfilePicture'] ?? '';
                    if (empty($profileImage) || str_contains($profileImage, 'example.com')) {
                        $profileImage = 'images/DefaultProfile.svg';
                    } else {
                        $profileImage = 'pfpUploads/' . htmlspecialchars($profileImage);
                    }
                    ?>
                    <img class="Profile-Picture" src="<?php echo htmlspecialchars($profileImage); ?>" alt="User Icon">
                </button>
                <div id="account-dropdown">
                    <a href="account.php">Account</a>
                    <a href="voting-results-afterwardnavigation.php">Resultaten</a>
                    <a href="logout.php">Uitloggen</a>
                </div>
            </div>
        <?php } else { ?>
            <button id="account-button" onclick="window.location.href='login.php'">
                <span>Inloggen</span>
                <img src="images/DefaultProfile.svg" alt="Login Icon">
            </button>
        <?php } ?>
    </div>
</header>
<?php if (basename($_SERVER['PHP_SELF']) !== 'login.php') { ?>
    <nav>
        <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
        <a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'selected' : ''; ?>">Home</a>
        <a href="elections.php" class="<?php echo ($currentPage == 'elections.php') ? 'selected' : ''; ?>">Verkiezingen</a>
        <a href="parties.php" class="<?php echo ($currentPage == 'parties.php') ? 'selected' : ''; ?>">Partijen</a>
        <a href="voting-results-afterwardnavigation.php" class="<?php echo ($currentPage == 'voting-results-afterwardnavigation.php') ? 'selected' : ''; ?>">Stemwijzer</a>
        <a href="news.php" class="<?php echo ($currentPage == 'news.php') ? 'selected' : ''; ?>">Nieuws</a>
    </nav>
<?php } ?>