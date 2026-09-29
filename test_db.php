<?php
$start = microtime(true);
echo "Connecting to DB...\n";
try {
    $pdo = new PDO('mysql:host=cinema_db;dbname=cinema_ticket_management', 'root', 'secret');
    $time = microtime(true) - $start;
    echo "Connected in {$time} seconds\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
