<?php
// ===========================================
// UPDATE EVENT STATUS HANDLER
// ===========================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connect.php';
$user_id = $_SESSION['user_id'];

// ===========================================
// VALIDATE INPUT
// ===========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = intval($_POST['event_id']);
    $new_status = $_POST['status'] ?? '';

    // Only allow valid statuses
    if (!in_array($new_status, ['Posted', 'Draft'])) {
        die("Invalid status value.");
    }

    // ===========================================
    // VERIFY USER IS ADMIN FOR THIS EVENT
    // ===========================================
    $checkStmt = $conn->prepare("
        SELECT e.id 
        FROM event e
        INNER JOIN organization o ON e.organization_id = o.id
        INNER JOIN member m ON m.organization_id = o.id
        WHERE e.id = ? AND m.user_id = ? AND m.permission_level = 'Admin'
        LIMIT 1
    ");
    $checkStmt->bind_param("ii", $event_id, $user_id);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {
        $checkStmt->close();
        $conn->close();
        die("Unauthorized: You do not have permission to update this event.");
    }
    $checkStmt->close();

    // ===========================================
    // UPDATE EVENT STATUS
    // ===========================================
    $updateStmt = $conn->prepare("UPDATE event SET status = ? WHERE id = ?");
    $updateStmt->bind_param("si", $new_status, $event_id);
    $updateStmt->execute();
    $updateStmt->close();

    // ===========================================
    // REDIRECT BACK TO PLANNING BOARD
    // ===========================================
    header("Location: planning.php?event_id=" . $event_id . "&updated=1");
    exit();
}

$conn->close();
?>
