<?php require 'php_require/autoload.php'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Neutraal Kieslab - Stemwijzer</title>
    <link rel="stylesheet" href="styles/stemwijzer.css">
    <?php require 'php_require/resources.php'; ?>
</head>

<body>
    <?php require 'php_require/header.php'; ?>
    <section class="intro">
        <h1 class="intro-header">Stemwijzer</h1>
        <p class="intro-tekst">Welkom bij de stemwijzer! Hier kun je jouw standpunten vergelijken met die van de partijen.</p>
        <p class="intro-tekst">Lees de stellingen zorgvuldig door en geef aan of je het eens of oneens bent met elk standpunt.</p>
        <p class="intro-tekst">Je kunt altijd teruggaan naar de vorige stelling door op de knop "Vorige" te klikken.</p>
        <button onclick="startStemwijzer()" class="intro-button">
            Start de Stemwijzer
        </button>
        <script>
        function startStemwijzer() {
            const stemwijzerElement = document.querySelector('.stemwijzer');
            const introElement = document.querySelector('.intro');

            if (stemwijzerElement && introElement) {
                stemwijzerElement.style.visibility = 'visible';
                introElement.style.display = 'none';
            } else {
                console.error('Required elements not found: stemwijzer or intro');
            }
        }
        </script>
    </section>

    <?php
    getVotingStatements();

    require 'php_require/footer.php'; ?>
</body>

</html>