<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
require_once 'audit.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $event_id = intval($_POST['event_id']);
    $status   = $_POST['status'] ?? '';

    // Validate status
    if (!in_array($status, ['Posted', 'Draft'])) {
        die("Invalid status value.");
    }

    // Ensure user is an admin of this event's organization
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
        die("Unauthorized: You are not allowed to update this event.");
    }

    // Update status
    $update = $conn->prepare("UPDATE event SET status = ? WHERE id = ?");
    $update->bind_param("si", $status, $event_id);
    $update->execute();
    $update->close();

    log_audit($conn, $user_id, "Updated event status for event ID $event_id to $status", $event_id);

    header("Location: planning.php?event_id=" . $event_id);
    exit();
}
?>
