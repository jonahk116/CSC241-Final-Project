<?php
$host = 'db5019056548.hosting-data.io';
$db   = 'dbs14993273';
$user = 'dbu5700738';
$pass = 'P@ssw0rd!!#123'; // *** MUST BE FILLED IN ***
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Default fetch mode to associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulation for better security
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Die on connection failure and display the error message
     die("Connection failed: " . $e->getMessage());
}
