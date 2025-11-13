<?php
// ===========================================
// DATABASE CONNECTION (XAMPP)
// ===========================================


require __DIR__ . '/vendor/autoload.php'; // <-- corrected path

// load .env
$dotenv = Dotenv\Dotenv::createImmutable('/var/www/');
$dotenv->load();

$host = 'localhost';
$user = $_ENV['db_user'];
$pass = $_ENV['db_password'];
$dbname = $_ENV['db_name'];
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: force MySQLi to throw exceptions (helps with debugging)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
