<?php
require_once 'private/databaseinfo.php';

function testDatabaseConnection()
{
    global $db_host, $db_port, $db_name, $db_user, $db_password;

    echo "<h1>Database Connection Test</h1>";
    echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px;'>";

    echo "<h2>Connection Parameters:</h2>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><td style='padding: 8px; background: #f0f0f0;'><strong>Host:</strong></td><td style='padding: 8px;'>$db_host</td></tr>";
    echo "<tr><td style='padding: 8px; background: #f0f0f0;'><strong>Port:</strong></td><td style='padding: 8px;'>$db_port</td></tr>";
    echo "<tr><td style='padding: 8px; background: #f0f0f0;'><strong>Database:</strong></td><td style='padding: 8px;'>$db_name</td></tr>";
    echo "<tr><td style='padding: 8px; background: #f0f0f0;'><strong>User:</strong></td><td style='padding: 8px;'>$db_user</td></tr>";
    echo "<tr><td style='padding: 8px; background: #f0f0f0;'><strong>Password:</strong></td><td style='padding: 8px;'>" . str_repeat('*', strlen($db_password)) . "</td></tr>";
    echo "</table>";

    echo "<h2>Connection Test:</h2>";

    try {
        $start_time = microtime(true);
        $conn = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $end_time = microtime(true);
        $connection_time = round(($end_time - $start_time) * 1000, 2);

        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>✅ SUCCESS:</strong> Database connection established successfully!<br>";
        echo "<strong>Connection time:</strong> {$connection_time}ms";
        echo "</div>";

        echo "<h3>Database Information:</h3>";

        $version = $conn->query("SELECT version()")->fetchColumn();
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><td style='padding: 8px; background: #f0f0f0;'><strong>PostgreSQL Version:</strong></td><td style='padding: 8px;'>$version</td></tr>";

        $current_db = $conn->query("SELECT current_database()")->fetchColumn();
        echo "<tr><td style='padding: 8px; background: #f0f0f0;'><strong>Current Database:</strong></td><td style='padding: 8px;'>$current_db</td></tr>";

        $current_user = $conn->query("SELECT current_user")->fetchColumn();
        echo "<tr><td style='padding: 8px; background: #f0f0f0;'><strong>Current User:</strong></td><td style='padding: 8px;'>$current_user</td></tr>";

        $encoding = $conn->query("SHOW server_encoding")->fetchColumn();
        echo "<tr><td style='padding: 8px; background: #f0f0f0;'><strong>Server Encoding:</strong></td><td style='padding: 8px;'>$encoding</td></tr>";
        echo "</table>";

        echo "<h3>Table Check:</h3>";
        try {
            $tables_query = $conn->query("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' 
                ORDER BY table_name
            ");
            $tables = $tables_query->fetchAll(PDO::FETCH_COLUMN);

            if (count($tables) > 0) {
                echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border: 1px solid #b8daff; border-radius: 5px; margin: 10px 0;'>";
                echo "<strong>Tables found:</strong> " . count($tables) . "<br>";
                echo "<strong>Table names:</strong> " . implode(', ', $tables);
                echo "</div>";

                $expected_tables = ['gebruikers', 'partijen', 'stellingen', 'verkiezingen', 'nieuws', 'comments', 'partij_verkiezing'];
                $missing_tables = array_diff($expected_tables, $tables);
                if (count($missing_tables) > 0) {
                    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;'>";
                    echo "<strong>⚠️ WARNING:</strong> Some expected tables are missing: " . implode(', ', $missing_tables);
                    echo "</div>";
                }
            } else {
                echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0;'>";
                echo "<strong>⚠️ WARNING:</strong> No tables found in the database.";
                echo "</div>";
            }
        } catch (PDOException $e) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
            echo "<strong>❌ ERROR checking tables:</strong> " . $e->getMessage();
            echo "</div>";
        }

        $conn = null;
    } catch (PDOException $e) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>❌ CONNECTION FAILED:</strong><br>";
        echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
        echo "<strong>Error Code:</strong> " . $e->getCode();
        echo "</div>";

        echo "<h3>Troubleshooting Tips:</h3>";
        echo "<ul>";
        echo "<li>Check if the database server is running</li>";
        echo "<li>Verify the hostname and port are correct</li>";
        echo "<li>Ensure the database name exists</li>";
        echo "<li>Verify username and password are correct</li>";
        echo "<li>Check if your IP is allowed to connect to the database</li>";
        echo "</ul>";
    }

    echo "</div>";
}

testDatabaseConnection();
