<?php
session_start();

// Clear session data
$_Session = [];
session_destroy();
header("Location: login.php");
exit();
?>