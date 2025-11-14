<?php

// Audit function to be used by other files.

function log_audit($conn, $user_id, $action_description, $affected_id) {
    $stmt = $conn->prepare("
        INSERT INTO audit_log (user_id, action_description, affected_id, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->bind_param("isi", $user_id, $action_description, $affected_id);
    $stmt->execute();
    $stmt->close();
}
?>