<?php require 'php_require/autoload.php'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Neutraal Kieslab - Partijen</title>
    <?php require 'php_require/resources.php'; ?>
    <link rel="stylesheet" href="styles/parties.css">
</head>

<body>
    <?php require 'php_require/header.php'; ?>
    <div id="maincontent">
        <div class="partijen-grid">

            <?php
            $conn = getConnection();


            $parties = getParties($conn);
            foreach ($parties as $party) {
                $partyName = htmlspecialchars($party['naam']);
                $partyImage = htmlspecialchars(explode(',', $party['afbeelding'])[0]);
                $partyAbbreviation = htmlspecialchars($party['afkorting']);
                echo "<div class='partij'>
                        <img src='https://$partyImage' alt='$partyName'>
                        <a href='party.php?party=$partyAbbreviation'>Voor info klik <span>hier</span></a>
                      </div>";
            }
            ?>
        </div>
    </div>
    <?php require 'php_require/footer.php'; ?>
</body>

</html>