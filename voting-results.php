<?php require 'php_require/autoload.php'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Neutraal Kieslab - Stemwijzer Resultaten</title>
    <link rel="stylesheet" href="styles/stemwijzer.css">
    <script src="js/votingresults.js"></script>
    <?php require 'php_require/resources.php'; ?>
</head>

<body>
    <?php require 'php_require/header.php'; ?>
    <div id="maincontent">
        <?php
        CheckVotingResults();
        ?>
    </div>
    <?php require 'php_require/footer.php'; ?>
</body>

</html>