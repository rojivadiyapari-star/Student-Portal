
<?php
session_start();

// Check whether the user is logged in.
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?denied=1");
    exit;
}

// Check session timeout (60 seconds for practical testing).
if (
    !isset($_SESSION["last_activity"]) ||
    time() - $_SESSION["last_activity"] > 60
) {
    header("Location: timeout.php?expired=1");
    exit;
}

// Only Admin users can access this page.
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin") {
    http_response_code(403);
    exit("Unauthorized Access: Admin privileges required.");
}

// Update last activity.
$_SESSION["last_activity"] = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h2>Admin Dashboard</h2>

        <p>
            Welcome,
            <?php echo htmlspecialchars($_SESSION["name"] ?? "Admin"); ?>!
        </p>

        <p><strong>Role:</strong> Admin</p>

        <p>You have successfully accessed the Admin Dashboard.</p>

        <p>
            <strong>Session ID regenerated after login:</strong>
            <?php
            echo !empty($_SESSION["session_regenerated"]) ? "Yes" : "No";
            ?>
        </p>

        <form action="logout.php" method="POST">
            <button type="submit">Logout</button>
        </form>
    </div>

</body>
</html>
