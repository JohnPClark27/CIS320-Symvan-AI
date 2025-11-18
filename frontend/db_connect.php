<?php
// ===========================================
// DATABASE CONNECTION — shared across app
// ===========================================

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "symvan_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: force MySQLi to throw exceptions (helps with debugging)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
