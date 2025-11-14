<?php
session_start();
require_once 'audit.php'; // Include audit function

// Update audit_log
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    log_audit($conn, $user_id, 'User logged out', $user_id);
}
// Clear session data
$_SESSION = [];
session_destroy();
header("Location: login.php");
exit();
?>