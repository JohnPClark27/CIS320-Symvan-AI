<?php
session_start();



// Clear session data
$_SESSION = [];
session_destroy();
header("Location: login.php");
exit();
?>