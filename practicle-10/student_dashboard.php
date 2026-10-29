<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php?denied=1");
    exit;
}

if (time() - $_SESSION["last_activity"] > 60) {
    header("Location: timeout.php?expired=1");
    exit;
}

if ($_SESSION["role"] !== "Student") {
    http_response_code(403);
    exit("Unauthorized Access: Student privileges required.");
}

$_SESSION["last_activity"] = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Student Dashboard</h2>
        <p>Welcome, <?= htmlspecialchars($_SESSION["name"]) ?>!</p>
        <p>Role: Student</p>
        <p>You have access to the student dashboard.</p>

        <p>Session ID regenerated after login:
            <?= !empty($_SESSION["session_regenerated"]) ? "Yes" : "No" ?>
        </p>

        <form action="logout.php" method="POST">
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>