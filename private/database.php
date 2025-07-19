<?php
require_once 'databaseinfo.php';

// Voorkom functie herdeclaratie
if (!function_exists('getConnection')) {
    function getConnection()
    {
        global $db_host, $db_port, $db_name, $db_user, $db_password;
        try {
            $conn = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo "Verbinding mislukt: " . $e->getMessage();
            return null;
        }
    }

    if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
        $conn = getConnection();
    }

    function CreateUser($conn, $name, $email, $password)
    {
        if (!$conn) {
            echo "Geen database verbinding beschikbaar.";
            return null;
        }
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $conn->beginTransaction();
            $pdo = $conn->prepare("INSERT INTO gebruikers (naam, email, wachtwoord_hash, rol, profielfoto, aangemaakt_op) VALUES (:naam, :email, :wachtwoord_hash, :rol, :profielfoto, :aangemaakt_op)");
            $pdo->bindParam(':naam', $name);
            $pdo->bindParam(':email', $email);
            $pdo->bindParam(':wachtwoord_hash', $hashedPassword);
            $pdo->bindValue(':rol', 'gebruiker');
            $pdo->bindValue(':profielfoto', 'https://example.com/profiel1.jpg');
            $pdo->bindValue(':aangemaakt_op', date('Y-m-d H:i:s'));
            $pdo->execute();
            $lastId = $conn->lastInsertId();
            $conn->commit();
            return $lastId;
        } catch (PDOException $e) {
            $conn->rollBackBack();
            echo "Fout bij aanmaken gebruiker: " . $e->getMessage();
            return null;
        }
    }

    function checkUserExists($conn, $username, $email)
    {
        try {
            $pdo = $conn->prepare("SELECT COUNT(*) FROM gebruikers WHERE naam = :username OR email = :email");
            $pdo->bindParam(':username', $username);
            $pdo->bindParam(':email', $email);
            $pdo->execute();
            return $pdo->fetchColumn() > 0;
        } catch (PDOException $e) {
            echo "Fout bij controleren gebruiker: " . $e->getMessage();
            return false;
        }
    }

    function authenticateUser($conn, $username, $password)
    {
        try {
            $pdo = $conn->prepare("SELECT * FROM gebruikers WHERE naam = :username OR email = :username");
            $pdo->bindParam(':username', $username);
            $pdo->execute();
            $user = $pdo->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['wachtwoord_hash'])) {
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            echo "Fout bij authenticeren gebruiker: " . $e->getMessage();
            return false;
        }
    }

    function updateUserInfo($conn, $type, $value, $userId, $originvalue)
    {
        try {
            if ($type === 'email') {
                $pdo = $conn->prepare("UPDATE gebruikers SET email = :email WHERE email = :originemail AND id = :userId");
                $pdo->bindParam(':email', $value);
                $pdo->bindParam(':originemail', $originvalue);
                $pdo->bindParam(':userId', $userId);
                if ($pdo->execute()) {
                    $_SESSION['Email'] = $value;
                    return true;
                }
            } elseif ($type === 'username') {
                $pdo = $conn->prepare("UPDATE gebruikers SET naam = :username WHERE naam = :originusername AND id = :userId");
                $pdo->bindParam(':username', $value);
                $pdo->bindParam(':originusername', $originvalue);
                $pdo->bindParam(':userId', $userId);
                if ($pdo->execute()) {
                    $_SESSION['Username'] = $value;
                    return true;
                }
            } elseif ($type === 'password') {
                $hashedPassword = password_hash($value, PASSWORD_DEFAULT);
                $pdo = $conn->prepare("UPDATE gebruikers SET wachtwoord_hash = :password WHERE id = :userId");
                $pdo->bindParam(':password', $hashedPassword);
                $pdo->bindParam(':userId', $userId);
                $pdo->execute();
                return true;
            } elseif ($type === 'pfp') {
                $pdo = $conn->prepare("UPDATE gebruikers SET profielfoto = :picture  WHERE id = :userId");
                $pdo->bindParam(':picture', $value);
                $pdo->bindParam(':userId', $userId);
                if ($pdo->execute()) {
                    $_SESSION['ProfilePicture'] = $value;
                    if ($originvalue && $originvalue !== $value) {
                        $oldPfpPath = "../pfpUploads/" . $originvalue;
                        if (file_exists($oldPfpPath)) {
                            unlink($oldPfpPath);
                        }
                    }
                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            echo "Fout bij updaten gebruikersinformatie: " . $e->getMessage();
            return false;
        }
    }

    function getPartiesFromElection($conn, $electionId)
    {
        try {
            $pdo = $conn->prepare("SELECT p.id, p.naam, p.afkorting, p.beschrijving, p.afbeelding, p.PositiePartij, pv.id as partij_verkiezing_id, pv.partij_id, pv.verkiezing_id FROM partijen p INNER JOIN partij_verkiezing pv ON p.id = pv.partij_id WHERE pv.verkiezing_id = :electionId");
            $pdo->bindParam(':electionId', $electionId);
            $pdo->execute();

            return $pdo->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Fout bij ophalen partijen van verkiezing: " . $e->getMessage();
            return [];
        }
    }

    function getParty($conn, $partyName)
    {
        try {
            $pdo = $conn->prepare("SELECT * FROM partijen WHERE afkorting = :partyId LIMIT 1");
            $pdo->bindParam(':partyId', $partyName);
            $pdo->execute();

            return $pdo->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Fout bij ophalen partijen: " . $e->getMessage();
            return [];
        }
    }

    function getParties($conn)
    {
        try {
            $pdo = $conn->prepare("SELECT * FROM partijen");
            $pdo->execute();

            return $pdo->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Fout bij ophalen partijen: " . $e->getMessage();
            return [];
        }
    }

    function getNews($conn)
    {
        try {
            $pdo = $conn->prepare("SELECT * FROM nieuws WHERE status = 1 ORDER BY aangemaakt_op DESC");
            $pdo->execute();
            return $pdo->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Fout bij ophalen nieuws: " . $e->getMessage();
            return [];
        }
    }

    function getNewsArticleInfo($conn, $articleId)
    {
        try {
            $pdo = $conn->prepare("SELECT * FROM nieuws WHERE id = :articleId AND status = 1");
            $pdo->bindParam(':articleId', $articleId);
            $pdo->execute();
            return $pdo->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Fout bij ophalen nieuwsartikel: " . $e->getMessage();
            return null;
        }
    }

    function getComments($conn, $articleId)
    {
        try {
            $pdo = $conn->prepare("SELECT * FROM comments WHERE nieuws_id = :articleId");
            $pdo->bindParam(':articleId', $articleId);
            $pdo->execute();
            return $pdo->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Fout bij ophalen reacties: " . $e->getMessage();
            return [];
        }
    }

    function postComment($conn, $articleId, $comment)
    {
        try {
            $conn->beginTransaction();
            $pdo = $conn->prepare("INSERT INTO comments (nieuws_id, gebruiker_id, comment_text, created_at, updated_at) VALUES (:articleId, :userId, :comment, :created_at, :updated_at)");
            $pdo->bindParam(':articleId', $articleId);
            $pdo->bindParam(':comment', $comment);
            $pdo->bindParam(':userId', $_SESSION['UserId']);
            $pdo->bindValue(':created_at', date('Y-m-d H:i:s'));
            $pdo->bindValue(':updated_at', date('Y-m-d H:i:s'));
            $pdo->execute();
            $lastId = $conn->lastInsertId();
            $conn->commit();
            return $lastId;
        } catch (PDOException $e) {
            $conn->rollBack();
            echo "Fout bij plaatsen reactie: " . $e->getMessage();
            return false;
        }
    }

    function deleteComment($conn, $commentId, $userId)
    {
        try {
            $pdo = $conn->prepare("DELETE FROM comments WHERE comment_id = :commentId AND gebruiker_id = :userId");
            $pdo->bindParam(':commentId', $commentId);
            $pdo->bindParam(':userId', $userId);
            return $pdo->execute();
        } catch (PDOException $e) {
            echo "Fout bij verwijderen reactie: " . $e->getMessage();
            return false;
        }
    }

    function editComment($conn, $commentId, $userId, $commentText)
    {
        try {
            $pdo = $conn->prepare("UPDATE comments SET comment_text = :commentText WHERE comment_id = :commentId AND gebruiker_id = :userId");
            $pdo->bindParam(':commentId', $commentId);
            $pdo->bindParam(':userId', $userId);
            $pdo->bindParam(':commentText', $commentText);
            return $pdo->execute();
        } catch (PDOException $e) {
            echo "Error editing comment: " . $e->getMessage();
            return false;
        }
    }

    function getUserInfo($conn, $userId)
    {
        try {
            $pdo = $conn->prepare("SELECT * FROM gebruikers WHERE id = :userId");
            $pdo->bindParam(':userId', $userId);
            $pdo->execute();
            return $pdo->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Fout bij ophalen gebruikersinformatie: " . $e->getMessage();
            return null;
        }
    }

    function getUserVotingResults($conn, $userId)
    {
        try {
            $pdo = $conn->prepare("SELECT uitslag FROM gebruikers WHERE id = :userId");
            $pdo->bindParam(':userId', $userId);
            $pdo->execute();
            $result = $pdo->fetch(PDO::FETCH_ASSOC);

            if ($result && !empty($result['uitslag'])) {
                // Check if the uitslag is a JSON string (old format) or a plain string (new format)
                $data = json_decode($result['uitslag'], true);

                if ($data !== null) {
                    // Old format (JSON)
                    // Convert the compact format back to the original format if needed
                    if (isset($data['up']) && !isset($data['thumbsUp'])) {
                        return [
                            'thumbsUp' => $data['up'],
                            'thumbsDown' => $data['down'],
                            'partyMatches' => array_map(function ($match) {
                                // Fetch additional party info if needed
                                return [
                                    'party' => ['id' => $match['id']],
                                    'matchPercentage' => $match['pct']
                                ];
                            }, $data['matches'])
                        ];
                    }

                    return $data;
                } else {
                    // New format (string with party names and percentages)
                    return $result['uitslag'];
                }
            }
            return null;
        } catch (PDOException $e) {
            echo "Fout bij ophalen stemwijzer resultaten: " . $e->getMessage();
            return null;
        }
    }

    function getDataFromTable($conn, $tableName)
    {
        try {
            $pdo = $conn->prepare("SELECT * FROM $tableName");
            $pdo->execute();
            return $pdo->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Fout bij ophalen data uit $tableName: " . $e->getMessage();
            return [];
        }
    }


    function getVotingStatements()
    {
        //pagina laadt voor het eerst dus we verwijderen de cookies
        if (isset($_COOKIE['thumbsUp'])) {
            setcookie('thumbsUp', '', time() - 3600, '/');
        }
        if (isset($_COOKIE['thumbsDown'])) {
            setcookie('thumbsDown', '', time() - 3600, '/');
        }
        //functie om de stellingen op te halen voor de stemwijzer
        //Toegevoegd door Daan
        echo '
<section class="stemwijzer"><div class="stemhokje"><div class="stemsplash"></div><button class="stembutton juist" onclick="nextStatement(true)">👍</button><button class="stembutton onjuist" onclick="nextStatement(false)">👎</button>
';
        $conn = getConnection();
        if (!$conn) {
            echo "Geen database verbinding beschikbaar.";
            return [];
        }
        try {
            $pdo = $conn->prepare("SELECT * FROM stellingen");
            $pdo->execute();
            $statements = $pdo->fetchAll(PDO::FETCH_ASSOC);

            // Controleer of er stellingen zijn
            if (count($statements) > 0) {
                echo '<h1 class="standpunt" id="statement" data-total="' . count($statements) . '" data-current="0">' . $statements[0]['tekst'] . '</h1>';
                echo '<script>
                let currentStatement = 0;
                let statements = ' . json_encode(array_column($statements, 'tekst')) . ';
                let thumbsUp = [];
                let thumbsDown = [];

                function nextStatement(isThumbsUp) {
                    if(isThumbsUp) {
                        thumbsUp.push(currentStatement);
                        document.cookie = "thumbsUp=" + JSON.stringify(thumbsUp);
                    } else {
                        thumbsDown.push(currentStatement);
                        document.cookie = "thumbsDown=" + JSON.stringify(thumbsDown);
                    }

                    currentStatement++;
                    if(currentStatement >= statements.length) {
                        window.location.href = "voting-results.php";
                        return;
                    }
                    document.getElementById("statement").textContent = statements[currentStatement];
                    document.getElementById("statement").dataset.current = currentStatement;
                }
            </script></section>';
            } else {
                echo '<h1 class="standpunt">Geen stellingen beschikbaar</h1></section>';
            }
        } catch (PDOException $e) {
            echo "Fout bij ophalen stemwijzer stellingen: " . $e->getMessage();
            return [];
        }
    }

    function saveVotingResults($conn, $userId, $thumbsUp, $thumbsDown, $partyMatches)
    {
        if (!$conn || !$userId) {
            return false;
        }

        try {
            // Create a more compact JSON representation of the voting results
            // Using shorter property names to reduce the overall size
            $results = [
                'up' => $thumbsUp,
                'down' => $thumbsDown,
                'matches' => array_map(function ($match) {
                    // Only store the essential information with shorter property names
                    return [
                        'id' => $match['party']['id'],
                        'pct' => $match['matchPercentage']
                    ];
                }, $partyMatches)
            ];

            $resultsJson = json_encode($results, JSON_NUMERIC_CHECK);

            // Update the user's record in the database using the requested SQL format
            // UPDATE gebruikers SET uitslag = DE UITSLAG VAN DE TEST WHERE id = SESSION nogwat;
            $pdo = $conn->prepare("UPDATE gebruikers SET uitslag = :uitslag WHERE id = :userId");
            $pdo->bindParam(':uitslag', $resultsJson);
            $pdo->bindParam(':userId', $userId);
            return $pdo->execute();
        } catch (PDOException $e) {
            echo "Fout bij opslaan stemwijzer resultaten: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Update de stemwijzer resultaten voor een gebruiker
     * 
     * @param PDO $conn Database connectie
     * @param string $uitslag De uitslag van de stemwijzer test (string met partijnamen en percentages gescheiden door komma's)
     * @param int $userId De gebruiker ID uit de sessie
     * @return bool True als het updaten is gelukt, anders False
     */
    function updateStemwijzerUitslag($conn, $uitslag, $userId = null)
    {
        if (!$conn) {
            return false;
        }

        // Als geen userId is opgegeven, gebruik de gebruiker ID uit de sessie
        if ($userId === null && isset($_SESSION['UserId'])) {
            $userId = $_SESSION['UserId'];
        }

        if (!$userId) {
            return false;
        }

        try {
            // Update gebruikers SET uitslag = DE UITSLAG VAN DE TEST WHERE id = SESSION nogwat;
            $pdo = $conn->prepare("UPDATE gebruikers SET uitslag = :uitslag WHERE id = :userId");
            $pdo->bindParam(':uitslag', $uitslag);
            $pdo->bindParam(':userId', $userId);
            return $pdo->execute();
        } catch (PDOException $e) {
            echo "Fout bij updaten stemwijzer uitslag: " . $e->getMessage();
            return false;
        }
    }

    function CheckVotingResults()
    {
        // Controleer of de cookies bestaan
        if (!isset($_COOKIE['thumbsUp']) || !isset($_COOKIE['thumbsDown'])) {
            // Als de cookies niet bestaan, controleer of de gebruiker is ingelogd en eerdere resultaten heeft
            if (isset($_SESSION['UserId']) && !empty($_SESSION['UserId'])) {
                $conn = getConnection();
                if (!$conn) {
                    echo "Geen database verbinding beschikbaar.";
                    return;
                }

                // Haal de eerdere resultaten van de gebruiker op
                $previousResults = getUserVotingResults($conn, $_SESSION['UserId']);

                // Als er eerdere resultaten zijn, toon deze als eenvoudige tekst
                if ($previousResults && is_string($previousResults)) {
                    echo '<div class="results-container">';
                    echo '<h1>Jouw stemwijzer resultaten</h1>';
                    echo '<p>Je hebt de stemwijzer al eerder ingevuld. Hieronder zie je je resultaten.</p>';
                    echo '<p>Wil je de stemwijzer opnieuw invullen? <a href="voting-guide.php">Klik hier</a>.</p>';

                    echo '<div class="user-results">';
                    echo '<h2>Jouw uitslag:</h2>';
                    echo '<p>' . $previousResults . '</p>';
                    echo '</div>';

                    echo '</div>';
                    return;
                }
            }

            // Als er geen cookies zijn en geen eerdere resultaten, toon het standaard bericht
            echo '<div class="results-container">';
            echo '<h1>Geen resultaten beschikbaar</h1>';
            echo '<p>Je hebt nog geen stemwijzer ingevuld. <a href="voting-guide.php">Klik hier om de stemwijzer in te vullen</a>.</p>';
            echo '</div>';
            return;
        }

        // Haal de thumbs up en thumbs down arrays op uit cookie
        $thumbsUp = json_decode($_COOKIE['thumbsUp']);
        $thumbsDown = json_decode($_COOKIE['thumbsDown']);

        // Maak database verbinding
        $conn = getConnection();
        if (!$conn) {
            echo "Geen database verbinding beschikbaar.";
            return;
        }

        try {
            // Haal alle stellingen op
            $pdo = $conn->prepare("SELECT * FROM stellingen");
            $pdo->execute();
            $statements = $pdo->fetchAll(PDO::FETCH_ASSOC);

            // Haal alle partijen op
            $pdo = $conn->prepare("SELECT * FROM partijen");
            $pdo->execute();
            $parties = $pdo->fetchAll(PDO::FETCH_ASSOC);

            // Toon resultaten header
            echo '<div class="results-container">';
            echo '<h1>Stemwijzer Resultaten</h1>';
            echo "<button id='print'>Print als PDF</button>";
            // Toon samenvatting van gebruikersantwoorden
            echo '<div class="user-answers">';
            echo '<h2>Jouw antwoorden</h2>';
            echo '<ul>';
            foreach ($thumbsUp as $statementIndex) {
                // Check if index exists before accessing
                if (isset($statements[$statementIndex])) {
                    echo '<li>👍 ' . $statements[$statementIndex]['tekst'] . '</li>';
                }
            }
            foreach ($thumbsDown as $statementIndex) {
                // Check if index exists before accessing
                if (isset($statements[$statementIndex])) {
                    echo '<li>👎 ' . $statements[$statementIndex]['tekst'] . '</li>';
                }
            }
            echo '</ul>';
            echo '</div>';

            // Bereken match percentages voor elke partij
            // Aangezien we geen echte partijposities in de database hebben, simuleren we deze
            // en vergelijken we met de antwoorden van de gebruiker
            $partyMatches = [];
            $totalMatchScore = 0;

            // Eerst berekenen we de ruwe match scores voor elke partij
            foreach ($parties as $party) {
                $partyId = intval($party['id']);
                $matchCount = 0;
                $totalStatements = count($statements);

                // Voor elke stelling, bepaal of de partij het eens of oneens zou zijn
                // Dit is een deterministische simulatie gebaseerd op partij ID en stelling ID
                foreach ($statements as $index => $statement) {
                    $statementId = intval($statement['id']);

                    // Simuleer partijpositie: als (partyId + statementId) even is, is partij het eens, anders oneens
                    $partyAgrees = (($partyId + $statementId) % 2 == 0);

                    // Controleer of gebruiker het eens is met deze stelling (in thumbsUp array)
                    $userAgrees = in_array($index, $thumbsUp);

                    // Controleer of gebruiker het oneens is met deze stelling (in thumbsDown array)
                    $userDisagrees = in_array($index, $thumbsDown);

                    // Als beide het eens zijn of beide oneens, is het een match
                    if (($partyAgrees && $userAgrees) || (!$partyAgrees && $userDisagrees)) {
                        $matchCount++;
                    }
                }

                // Bereken ruwe match score
                $matchScore = $matchCount;
                $totalMatchScore += $matchScore;

                $partyMatches[] = [
                    'party' => $party,
                    'matchScore' => $matchScore
                ];
            }

            // Normaliseer de scores zodat ze optellen tot 100%
            foreach ($partyMatches as &$match) {
                if ($totalMatchScore > 0) {
                    $match['matchPercentage'] = round(($match['matchScore'] / $totalMatchScore) * 100);
                } else {
                    $match['matchPercentage'] = 0;
                }
            }

            // Sorteer partijen op match percentage (hoogste eerst)
            usort($partyMatches, function ($a, $b) {
                return $b['matchPercentage'] - $a['matchPercentage'];
            });

            // Als de gebruiker is ingelogd, sla de resultaten op in de database
            if (isset($_SESSION['UserId']) && !empty($_SESSION['UserId'])) {
                // Bereid de uitslag voor als een string met partijnamen en percentages
                $resultsString = '';
                foreach ($partyMatches as $match) {
                    $party = $match['party'];
                    $percentage = $match['matchPercentage'];
                    $resultsString .= $party['naam'] . ' ' . $percentage . '%, ';
                }
                // Verwijder de laatste komma en spatie
                $resultsString = rtrim($resultsString, ', ');

                // Update de uitslag in de database met de nieuwe functie
                updateStemwijzerUitslag($conn, $resultsString);
            }

            // Toon partijmatches en zet de resultaten in de database van de gebruiker

            $ingelogde_gebruiker = isset($_SESSION['UserId']) && !empty($_SESSION['UserId']);
            if ($ingelogde_gebruiker) {
                //echo '<script> alert("TEST ' . $_SESSION['UserId'] . '")</script>';
            }



            echo '<div class="party-matches">';
            echo '<h2>Partijen die bij jou passen</h2>';
            echo '<div class="party-list">';
            foreach ($partyMatches as $match) {
                $party = $match['party'];
                $percentage = $match['matchPercentage'];

                echo '<div class="party-match">';
                echo '<div class="party-info">';
                if (isset($party['afbeelding']) && !empty($party['afbeelding'])) {
                    $logoUrl = explode(',', $party['afbeelding'])[0];
                    echo "<img src='https://$logoUrl' alt='" . $party['naam'] . "' class='party-logo'>";
                }
                echo '<h3>' . $party['naam'] . ' (' . $party['afkorting'] . ')</h3>';
                echo '</div>';
                echo '<div class="match-percentage">';
                echo '<div class="progress-bar">';
                echo '<div class="progress" style="width: ' . $percentage . '%"></div>';
                echo '</div>';
                echo '<span>' . $percentage . '%</span>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';

            echo '</div>';
        } catch (PDOException $e) {
            echo "Fout bij ophalen stemwijzer resultaten: " . $e->getMessage();
        }
    }


    function generateResetToken($conn, $userId, $email)
    {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 3600); // 1 uur geldig
        try {
            $pdo = $conn->prepare("UPDATE gebruikers SET reset_token = :token, reset_verloopdatum = :expires WHERE email = :email AND id = :userId");
            $pdo->bindParam(':token', $token);
            $pdo->bindParam(':expires', $expires);
            $pdo->bindParam(':email', $email);
            $pdo->bindParam(':userId', $userId);
            if ($pdo->execute()) {
                header("Location: ../login.php?resetpassword&token=" . $token);
                return true;
            }
            return $pdo->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Fout bij ophalen gebruikersinformatie: " . $e->getMessage();
            return null;
        }
    }

    function checkResetToken($conn, $token)
    {
        try {
            $pdo = $conn->prepare("SELECT * FROM gebruikers WHERE reset_token = :token AND reset_verloopdatum > NOW()");
            $pdo->bindParam(':token', $token);
            $pdo->execute();
            return $pdo->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Fout bij controleren reset token: " . $e->getMessage();
            return null;
        }
    }

    function removeResetToken($conn, $userId)
    {
        try {
            $pdo = $conn->prepare("UPDATE gebruikers SET reset_token = NULL, reset_verloopdatum = NULL WHERE id = :userId");
            $pdo->bindParam(':userId', $userId);
            return $pdo->execute();
        } catch (PDOException $e) {
            echo "Fout bij verwijderen reset token: " . $e->getMessage();
            return false;
        }
    }

    function getUserIdFromEmail($conn, $email)
    {
        try {
            $pdo = $conn->prepare("SELECT id FROM gebruikers WHERE email = :email");
            $pdo->bindParam(':email', $email);
            $pdo->execute();
            return $pdo->fetchColumn();
        } catch (PDOException $e) {
            echo "Fout bij ophalen gebruikers ID: " . $e->getMessage();
            return null;
        }
    }

    function getEmailFromResetToken($conn, $token)
    {
        try {
            $pdo = $conn->prepare("SELECT email FROM gebruikers WHERE reset_token = :token");
            $pdo->bindParam(':token', $token);
            $pdo->execute();
            return $pdo->fetchColumn();
        } catch (PDOException $e) {
            echo "Fout bij ophalen e-mailadres van reset token: " . $e->getMessage();
            return null;
        }
    }
}