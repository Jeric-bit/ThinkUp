<?php
// logout.php
session_start();

// 1. Clear all session variables
session_unset();

// 2. Destroy the session
session_destroy();

// 3. Redirect to the Log In page
header("Location: log-in.php");
exit();
?>