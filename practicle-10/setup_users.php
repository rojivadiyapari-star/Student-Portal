<?php
require "db.php";

$users = [
    ["Admin User", "admin@example.com", "Admin@123", "Admin"],
    ["Student User", "student@example.com", "Student@123", "Student"]
];

$stmt = $conn->prepare(
    "INSERT INTO users (name, email, password, role)
     VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
     name = VALUES(name),
     password = VALUES(password),
     role = VALUES(role)"
);

foreach ($users as $user) {
    [$name, $email, $plainPassword, $role] = $user;
    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
    $stmt->bind_param("ssss", $name, $email, $hash, $role);
    $stmt->execute();
}

echo "Admin and Student test accounts created successfully.";
?>