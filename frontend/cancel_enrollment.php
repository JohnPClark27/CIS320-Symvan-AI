<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $user_id = $_SESSION['user_id'];
    $event_id = $_POST['event_id'];

    require_once 'db_connect.php';
    
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("DELETE FROM enrollment WHERE user_id = ? AND event_id = ?");
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}

header("Location: myevents.php");
exit();
?>
