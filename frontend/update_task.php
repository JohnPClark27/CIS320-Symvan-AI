<?php
session_start();
require_once 'db_connect.php';
require_once 'audit.php'; // Include audit function

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Make sure we got a valid POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'], $_POST['status'])) {
    $task_id = intval($_POST['task_id']);
    $status  = $_POST['status'];
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;

    // Update only if user owns the task
    $stmt = $conn->prepare("UPDATE task SET status = ? WHERE id = ? AND created_by = ?");
    $stmt->bind_param("sii", $status, $task_id, $user_id);
    $stmt->execute();

    // Update audit_log
    log_audit($conn, $user_id, 'Updated task status', $task_id);
    
    $stmt->close();

    // Redirect back to the same event board
    header("Location: planning.php?event_id=" . $event_id);
    exit();
}

$conn->close();

// If invalid access, go back
header("Location: planning.php");
exit();
?>
