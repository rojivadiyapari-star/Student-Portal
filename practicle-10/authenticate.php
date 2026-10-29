
<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    header("Location: login.php?error=1");
    exit;
}

// Find the user by email.
$stmt = $conn->prepare(
    "SELECT id, name, email, password, role
     FROM users
     WHERE email = ?
     LIMIT 1"
);

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Verify the password hash.
if (!$user || !password_verify($password, $user["password"])) {
    header("Location: login.php?error=1");
    exit;
}

// Regenerate session ID after successful login.
session_regenerate_id(true);

// Store user information in the session.
$_SESSION["user_id"] = $user["id"];
$_SESSION["name"] = $user["name"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = $user["role"];
$_SESSION["last_activity"] = time();
$_SESSION["session_regenerated"] = true;

// Redirect according to the user's role.
if ($user["role"] === "Admin") {
    header("Location: admin_dashboard.php");
    exit;
}

if ($user["role"] === "Student") {
    header("Location: student_dashboard.php");
    exit;
}

session_unset();
session_destroy();

header("Location: login.php?denied=1");
exit;
?>
