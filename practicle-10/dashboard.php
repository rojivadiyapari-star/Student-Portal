
<?php
session_start();

// Deny access if the user is not logged in.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?denied=1");
    exit;
}

// Check for session inactivity timeout (60 seconds).
if (
    !isset($_SESSION["last_activity"]) ||
    time() - $_SESSION["last_activity"] > 60
) {
    header("Location: timeout.php?expired=1");
    exit;
}

// Update the last activity time.
$_SESSION["last_activity"] = time();

// Redirect to the correct role-specific dashboard.
if ($_SESSION["role"] === "Admin") {
    header("Location: admin_dashboard.php");
    exit;
}

if ($_SESSION["role"] === "Student") {
    header("Location: student_dashboard.php");
    exit;
}

// Reject any unexpected role.
session_unset();
session_destroy();

header("Location: login.php?denied=1");
exit;
?>
