
<?php
session_start();

require_once "db.php";

$error = "";

// Create a default admin account if one does not exist
$defaultUsername = "admin";
$defaultPassword = "Admin@123";

$stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
$stmt->bind_param("s", $defaultUsername);
$stmt->execute();
$adminExists = $stmt->get_result()->num_rows > 0;
$stmt->close();

if (!$adminExists) {
    $passwordHash = password_hash($defaultPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO admins (username, password) VALUES (?, ?)"
    );
    $stmt->bind_param("ss", $defaultUsername, $passwordHash);
    $stmt->execute();
    $stmt->close();
}

// Process login form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter your username and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, username, password FROM admins WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($password, $admin["password"])) {
            session_regenerate_id(true);

            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_username"] = $admin["username"];

            $conn->close();

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Event Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Event Management System</h1>
    <h2>Admin Login</h2>

    <?php if ($error !== ""): ?>
        <p class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            required
            autocomplete="username"
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            required
            autocomplete="current-password"
        >

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
