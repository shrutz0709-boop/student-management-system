<?php
// Database connection settings for local development.
$host = '127.0.0.1';
$dbname = 'student_management';
$username = 'root';
$password = ''; // Change this if your MySQL root account has a password.

$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Keep database details out of the page shown to visitors.
    http_response_code(500);
    exit('Database connection failed. Check config.php and confirm MySQL is running and database.sql has been imported.');
}
