<?php
session_start();

require_once 'audit.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $event_id = intval($_POST['event_id']);

    // Verify admin permissions
    $stmt = $conn->prepare("
        SELECT e.id
        FROM event e
        INNER JOIN organization o ON e.organization_id = o.id
        INNER JOIN member m ON m.organization_id = o.id
        WHERE e.id = ? AND m.user_id = ? AND m.permission_level = 'Admin'
        LIMIT 1
    ");
    $stmt->bind_param("ii", $event_id, $user_id);
    $stmt->execute();
    $valid = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$valid) {
        die("Unauthorized: You cannot delete this event.");
    }

    // Delete tasks under this event
    $delTasks = $conn->prepare("DELETE FROM task WHERE event_id = ?");
    $delTasks->bind_param("i", $event_id);
    $delTasks->execute();
    $delTasks->close();

    // Delete the event itself
    $delete = $conn->prepare("DELETE FROM event WHERE id = ?");
    $delete->bind_param("i", $event_id);
    $delete->execute();
    $delete->close();

    log_audit($conn, $user_id, "Deleted event ID $event_id", $event_id);

    header("Location: planning.php");
    exit();
}
?>
