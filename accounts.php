<?php
session_start();
require 'private/database.php';
$conn = getConnection();

function LoadUsers($conn)
{
    try {
        $pdo = $conn->prepare("SELECT * FROM gebruikers");
        $pdo->execute();
        $gebruikers = $pdo->fetchAll(PDO::FETCH_ASSOC);
        return $gebruikers;
    } catch (PDOException $e) {
        echo "Fout bij ophalen gebruikers: " . $e->getMessage();
    }
}

$users = LoadUsers($conn);

print_r($users);
echo "<br>";
echo "<h1>Gebruikerslijst</h1>";
echo "<br>";
echo "<br>";
foreach ($users as $user) {
    echo "<div>";
    echo "<p>id: " . htmlspecialchars($user['id']) . "</p>";
    echo "<p>naam: " . htmlspecialchars($user['naam']) . "</p>";
    echo "<p>email: " . htmlspecialchars($user['email']) . "</p>";
    echo "<p>wachtwoord_hash: " . htmlspecialchars($user['wachtwoord_hash']) . "</p>";
    echo "<p>aangemaakt_op: " . htmlspecialchars($user['aangemaakt_op']) . "</p>";
    echo "</div>";
}