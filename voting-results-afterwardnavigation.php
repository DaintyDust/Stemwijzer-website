<?php require 'php_require/autoload.php'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Neutraal Kieslab - Stemwijzer Resultaten</title>
    <link rel="stylesheet" href="styles/stemwijzer.css">
    <?php require 'php_require/resources.php'; ?>
</head>

<body>
<?php require 'php_require/header.php'; ?>
<div id="maincontent">
    <?php
    // Check if user is logged in
    if (isset($_SESSION['UserId'])) {
        $conn = getConnection();
        if ($conn) {
            // Get user's voting results
            $results = getUserVotingResults($conn, $_SESSION['UserId']);

            if ($results && !empty($results)) {
                echo '<div class="results-container">';
                echo '<h1>UW RECENTE RESULTAAT</h1>';

                echo '<div class="user-results">';
                // Check if results is a string or an array
                if (is_string($results)) {
                    echo '<p>' . htmlspecialchars($results) . '</p>';
                } else if (is_array($results)) {
                    // Handle array format (old format)
                    echo '<ul>';
                    if (isset($results['partyMatches'])) {
                        foreach ($results['partyMatches'] as $match) {
                            $partyName = isset($match['party']['name']) ? $match['party']['name'] : 'Partij ' . $match['party']['id'];
                            $percentage = isset($match['matchPercentage']) ? $match['matchPercentage'] : '0';
                            echo '<li>' . htmlspecialchars($partyName) . ': ' . htmlspecialchars($percentage) . '%</li>';
                        }
                    }
                    echo '</ul>';
                }
                echo '</div>';
                echo '<a href="voting-guide.php" class="button">DOE DE STEMWIJZER OPNIEUW</a>';
                echo '</div>';
            } else {
                echo '<div class="results-container">';
                echo '<h1>Geen resultaten beschikbaar</h1>';
                echo '<p>Je hebt nog geen stemwijzer ingevuld. <a href="voting-guide.php">Klik hier om de stemwijzer in te vullen</a>.</p>';
                echo '</div>';
            }
        } else {
            echo '<div class="results-container">';
            echo '<h1>Fout bij verbinden met database</h1>';
            echo '<p>Er is een probleem opgetreden bij het verbinden met de database. Probeer het later opnieuw.</p>';
            echo '</div>';
        }
    } else {
        echo '<div class="results-container">';
        echo '<h1>Niet ingelogd</h1>';
        echo '<p>Je moet ingelogd zijn om je stemwijzer resultaten te bekijken. <a href="login.php">Klik hier om in te loggen</a>.</p>';
        echo '</div>';
    }
    ?>
</div>
<?php require 'php_require/footer.php'; ?>
</body>

</html>