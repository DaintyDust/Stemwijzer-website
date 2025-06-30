<?php
require 'php_require/autoload.php';

$partyName = '';
$partyDescription = '';
$partyLeader = '';
$leaderIcon = '';
$banner1 = '';
$banner2 = '';


$conn = getConnection();
$party = isset($_GET['party']) ? htmlspecialchars($_GET['party']) : '';
$parties = getParty($conn, $party);

if ($party) {
    $descriptionText = htmlspecialchars($parties[0]['beschrijving']);
    $BannerIcons = explode("Banner afbeeldingen:", $descriptionText);

    $partyName = htmlspecialchars($parties[0]['naam']);
    $partyDescription = trim($BannerIcons[0]);
    $partyLeader = htmlspecialchars($parties[0]['partij_leider']);

    if (count($BannerIcons) > 1) {
        $bannerParts = explode(",", trim($BannerIcons[1]));
        $leaderIcon = isset($bannerParts[0]) ? trim($bannerParts[0]) : '';
        $banner1 = isset($bannerParts[1]) ? trim($bannerParts[1]) : '';
        $banner2 =  htmlspecialchars($parties[0]['afbeelding']);
    }
} else {
    echo "Geen partij geselecteerd.";
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Neutraal Kieslab - <?php echo $partyName; ?></title>
    <?php require 'php_require/resources.php'; ?>
    <link rel="stylesheet" href="styles/party.css" />
</head>

<body>
    <?php require 'php_require/header.php'; ?>
    <div id="maincontent">
        <a href='parties.php' id='back-button'>
            <img src='images/arrow-right.svg' alt='Back Arrow'>
            <span>Terug naar Partijen</span>
        </a>

        <div class="party-container">
            <header class="party-header">
                <h1 class="party-name"><?php echo $partyName; ?></h1>
            </header>

            <div class="party-content">
                <section class="leader-section">
                    <div class="leader-info">
                        <div class="leader-image">
                            <img src="https://<?php echo $leaderIcon; ?>" alt="<?php echo $partyLeader; ?>" />
                        </div>
                        <div class="leader-details">
                            <h2>Partijleider</h2>
                            <h3><?php echo $partyLeader; ?></h3>
                            <p class="leader-title">Huidige leider van <?php echo $partyName; ?></p>
                        </div>
                    </div>
                </section>

                <section class="party-description">
                    <div class="description-content">
                        <div class="description-text">
                            <h2>Over <?php echo $partyName; ?></h2>
                            <p><?php echo $partyDescription; ?></p>
                        </div>
                        <div class="party-logo">
                            <img src="https://<?php echo $banner1; ?>" alt="<?php echo $partyName; ?> Logo" />
                        </div>
                    </div>
                </section>

                <section class="party-vision">
                    <div class="vision-content">
                        <div class="vision-image">
                            <img src="https://<?php echo $banner2; ?>" alt="<?php echo $partyName; ?> Visie" />
                        </div>
                        <div class="vision-text">
                            <h2>Standpunten & Visie</h2>
                            <p>De <?php echo $partyName; ?> heeft duidelijke standpunten over belangrijke maatschappelijke onderwerpen en werkt aan een betere toekomst voor Nederland.</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <?php require 'php_require/footer.php'; ?>
</body>

</html>