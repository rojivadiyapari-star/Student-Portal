
<?php
require_once "db.php";

$message = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if ($username === "" || $email === "" ||
        $password === "" || $confirm === "") {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id FROM users
             WHERE username = ? OR email = ?"
        );
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Username or email already exists.";
            $stmt->close();
        } else {
            $stmt->close();

            $hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users
                 (username, email, password_hash)
                 VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $username, $email, $hash);

            if ($stmt->execute()) {
                $message = "Registration successful!";
                $success = true;
            } else {
                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 9 - User Registration</title>
</head>
<body>
    <h2>User Registration</h2>

    <?php if ($message !== ""): ?>
        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" action="register.php">
        <label>Username:</label><br>
        <input type="text" name="username"><br><br>

        <label>Email:</label><br>
        <input type="text" name="email"><br><br>

        <label>Password:</label><br>
        <input type="password" name="password"><br><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password"><br><br>

        <button type="submit">Register</button>
    </form>
    <?php endif; ?>
</body>
</html>
