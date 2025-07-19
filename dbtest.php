<?php
require 'private/database.php';

function test($conn)
{
    try {
        $conn->exec("SET search_path TO public");
        $info = $conn->query("SELECT current_database(), current_schema()");
        $row = $info->fetch(PDO::FETCH_NUM);
        echo "DB: {$row[0]}, Schema: {$row[1]}";
    } catch (PDOException $e) {
        echo "Debug error: " . $e->getMessage();
    }
}

function test2($conn)
{
    $result = $conn->query("SELECT tablename FROM pg_tables WHERE schemaname = 'public';");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo '<pre>' . print_r($tables, true) . '</pre>';
}

test(getConnection());
test2(getConnection());
