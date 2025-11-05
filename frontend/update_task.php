<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'], $_POST['status'])) {
    $task_id = $_POST['task_id'];
    $status = $_POST['status'];

    require_once 'db_connect.php';

    $stmt = $conn->prepare("UPDATE task SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $task_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

header("Location: planning.php");
exit();
?>
