
<?php
session_start();
require_once "db.php";

// Redirect back to the main page with a message
function showMessage($message, $type = "error") {
    $_SESSION["message"] = $message;
    $_SESSION["message_type"] = $type;

    header("Location: index.php");
    exit;
}

// Accept submissions only through POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// Read form fields
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$course = trim($_POST["course"] ?? "");

// Validate required fields
if ($name === "" || $email === "" || $course === "") {
    showMessage("Please fill in all required fields.");
}

// Validate field lengths
if (mb_strlen($name) > 100 || mb_strlen($email) > 150) {
    showMessage("Name or email is too long.");
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    showMessage("Please enter a valid email address.");
}

// Allow only courses offered in index.php
$allowedCourses = [
    "B.Tech CSE",
    "B.Tech IT",
    "BCA",
    "MCA",
    "BBA"
];

if (!in_array($course, $allowedCourses, true)) {
    showMessage("Please select a valid course.");
}

// Check whether the email already exists
$check = $conn->prepare(
    "SELECT id FROM students WHERE email = ? LIMIT 1"
);
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    $conn->close();
    showMessage("This email is already registered. Use another email.");
}

$check->close();

// Insert new student
$stmt = $conn->prepare(
    "INSERT INTO students (name, email, course, status)
     VALUES (?, ?, ?, 'Active')"
);

$stmt->bind_param("sss", $name, $email, $course);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    showMessage("Student added successfully.", "success");
}

$stmt->close();
$conn->close();

showMessage("Unable to add student. Please try again.");
?>
