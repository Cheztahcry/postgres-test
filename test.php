<?php
// Replace with the PostgreSQL PC's IPv4 address (from its ipconfig)
$host = '192.168.31.11'; 
$port = '5432';
$db   = 'db_borrowing'; // or your database name
$user = 'postgres';
$pass = 'admin123'; // Replace with your actual PostgreSQL password

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "<h2 style='color: green;'>Success: Connected remotely to PostgreSQL!</h2>";
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Connection failed:</h2> " . $e->getMessage();
}
?>