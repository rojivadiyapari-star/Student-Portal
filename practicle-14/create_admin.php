
<?php
require_once 'db.php';

$email = 'admin@example.com';
$password = 'Admin@12345';
$hash = password_hash($password, PASSWORD_DEFAULT);

// Create the account or reset its password.
$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password_hash, role)
     VALUES (?, ?, ?, 'admin')
     ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        password_hash = VALUES(password_hash),
        role = 'admin'"
);

$stmt->execute(['Admin User', $email, $hash]);

// Verify that the account was saved correctly.
$stmt = $pdo->prepare(
    "SELECT email, role, password_hash
     FROM users WHERE email = ?"
);
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    echo "Admin account created and password verified successfully.<br>";
    echo "Database: " . htmlspecialchars($dbname ?? 'Check db.php') . "<br>";
    echo "Email: admin@example.com<br>";
    echo "Password: Admin@12345<br>";
    echo '<a href="login.php">Go to Login</a>';
} else {
    echo "Account verification failed. Check your database connection and users table.";
}
?>
