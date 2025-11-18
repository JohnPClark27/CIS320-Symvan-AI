<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $event_id = intval($_POST['event_id']);
    $details  = trim($_POST['details'] ?? '');

    if ($details === "") {
        die("Description cannot be empty.");
    }

    // Validate admin access
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
        die("Unauthorized: You cannot edit this event.");
    }

    // Update description
    $update = $conn->prepare("UPDATE event SET details = ? WHERE id = ?");
    $update->bind_param("si", $details, $event_id);
    $update->execute();
    $update->close();

    header("Location: planning.php?event_id=" . $event_id);
    exit();
}
?>
