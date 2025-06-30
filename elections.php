<?php require 'php_require/autoload.php'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Neutraal Kieslab - Verkiezingen</title>
    <?php require 'php_require/resources.php'; ?>
    <link rel="stylesheet" href="styles/elections.css">
</head>

<body>
    <?php require 'php_require/header.php'; ?>
    <div id="maincontent">
        <div id="elections-container">
            <?php
            $isView = isset($_GET['view']);
            $mode = $isView ? 'viewing' : 'normal';
            $conn = getConnection();
            if ($mode === 'normal') {
                $elections = getDataFromTable($conn, 'verkiezingen');
                foreach ($elections as $election) {
                    $electionId = htmlspecialchars($election['id']);
                    $electionName = htmlspecialchars($election['naam']);
                    $electionEndDate = htmlspecialchars($election['einddatum']);

                    echo "<section>";
                    echo "<div class='election-info'>";
                    echo "<h2>$electionName</h2>";
                    echo "<p>Eindigt: $electionEndDate</p>";
                    echo "</div>";
                    echo "<div class='buttons-container'>";
                    echo "<form method='POST' action='?view'>";
                    echo "<button type='submit'>Deelnemende Partijen</button>";
                    echo "<input type='hidden' name='value' value='$electionId'>";
                    echo "</form>";
                    echo "</div>";
                    echo "</section>";
                }
            } elseif ($mode === 'viewing') {
                echo "<a href='elections.php' id='back-button'><img src='images/arrow-right.svg' alt='Back Arrow'>Terug naar Verkiezingen</a>";
                $electionId = $_POST['value'];
                $parties = getPartiesFromElection($conn, $electionId);
                foreach ($parties as $partie) {
                    $partyName = htmlspecialchars($partie['naam']);
                    $partyLogo = htmlspecialchars($partie['afbeelding']);
                    $partyDescription = htmlspecialchars($partie['beschrijving']);

                    echo "<section class='party-section'>";
                    $logoUrl = explode(',', $partyLogo)[0];
                    echo "<img src='https://$logoUrl' alt='$partyName Logo'>";
                    echo "<div class='party-details'>";
                    echo "<h2>$partyName</h2>";
                    echo "<p>" . htmlspecialchars(explode('Banner afbeeldingen:', $partie['beschrijving'])[0]) . "</p>";
                    echo "</div>";
                    echo "</section>";
                }
            }
            ?>
        </div>
    </div>
    <?php require 'php_require/footer.php'; ?>
</body>

</html>