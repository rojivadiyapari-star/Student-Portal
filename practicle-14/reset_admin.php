<?php
// Temporary admin password reset script
$conn = new mysqli("localhost", "root", "", "practical14_db");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$email = "admin@example.com";
$newPassword = "Admin@123";
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE users SET password_hash = ? WHERE email = ?"
);
$stmt->bind_param("ss", $hash, $email);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Admin password reset successfully.";
} else {
    echo "No password changed. Check the email and existing password.";
}

$stmt->close();
$conn->close();
?>