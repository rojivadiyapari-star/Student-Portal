
<?php
session_start();
require_once "db.php";

// Redirect back with a message
function showMessage($message, $type = "error") {
    $_SESSION["message"] = $message;
    $_SESSION["message_type"] = $type;
    header("Location: index.php");
    exit;
}

// Only accept form submissions
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// Read form values
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$course = trim($_POST["course"] ?? "");

// Validate required fields
if (!$id || $id < 1 || $name === "" || $email === "" || $course === "") {
    showMessage("Please fill in all required fields.");
}

// Validate lengths
if (mb_strlen($name) > 100 || mb_strlen($email) > 150) {
    showMessage("Name or email is too long.");
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    showMessage("Please enter a valid email address.");
}

// Validate course
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

// Check for duplicate email belonging to another student
$check = $conn->prepare(
    "SELECT id FROM students WHERE email = ? AND id <> ? LIMIT 1"
);
$check->bind_param("si", $email, $id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    $conn->close();
    showMessage("This email is already used by another student.");
}
$check->close();

// Update the active student's details
$stmt = $conn->prepare(
    "UPDATE students
     SET name = ?, email = ?, course = ?
     WHERE id = ? AND status = 'Active'"
);

$stmt->bind_param("sssi", $name, $email, $course, $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $stmt->close();
    $conn->close();
    showMessage("Student details updated successfully.", "success");
}

// An unchanged record may report zero affected rows.
// Check whether the student still exists and is active.
$stmt->close();

$checkStudent = $conn->prepare(
    "SELECT id FROM students
     WHERE id = ? AND status = 'Active'"
);
$checkStudent->bind_param("i", $id);
$checkStudent->execute();
$checkStudent->store_result();

if ($checkStudent->num_rows > 0) {
    $checkStudent->close();
    $conn->close();
    showMessage("Student details saved successfully.", "success");
}

$checkStudent->close();
$conn->close();

showMessage("Student not found or could not be updated.");
?>
