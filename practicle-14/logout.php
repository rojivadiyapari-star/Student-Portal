
<?php
session_start();

// Clear all session variables
$_SESSION = [];

// Destroy the current session
session_destroy();

// Prevent the browser from caching authenticated pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Redirect to the login page
header("Location: login.php");
exit;
?>
