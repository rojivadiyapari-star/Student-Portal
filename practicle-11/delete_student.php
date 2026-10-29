
<?php
session_start();
require_once "db.php";

// Get the student ID
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $_SESSION["message"] = "Invalid student ID.";
    $_SESSION["message_type"] = "error";
    header("Location: index.php");
    exit;
}

// Find the student
$stmt = $conn->prepare(
    "SELECT id, name FROM students
     WHERE id = ? AND status = 'Active'"
);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    $_SESSION["message"] = "Student not found or already inactive.";
    $_SESSION["message_type"] = "error";
    header("Location: index.php");
    exit;
}

// If the user confirms deletion
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $confirmId = filter_input(
        INPUT_POST,
        "id",
        FILTER_VALIDATE_INT
    );

    if (!$confirmId || $confirmId !== $id) {
        $_SESSION["message"] = "Invalid student ID.";
        $_SESSION["message_type"] = "error";
        header("Location: index.php");
        exit;
    }

    // Soft delete: change status instead of removing the record
    $delete = $conn->prepare(
        "UPDATE students
         SET status = 'Inactive'
         WHERE id = ? AND status = 'Active'"
    );
    $delete->bind_param("i", $id);
    $delete->execute();

    if ($delete->affected_rows > 0) {
        $_SESSION["message"] =
            "Student marked as inactive successfully.";
        $_SESSION["message_type"] = "success";
    } else {
        $_SESSION["message"] = "Unable to delete this student.";
        $_SESSION["message_type"] = "error";
    }

    $delete->close();
    $conn->close();

    header("Location: index.php?status=Inactive");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Student Deletion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="panel">
            <h2>Confirm Student Deletion</h2>

            <p>
                Are you sure you want to mark
                <strong>
                    <?= htmlspecialchars(
                        $student["name"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </strong>
                as inactive?
            </p>

            <p>The student record will not be permanently removed.</p>

            <form method="POST"
                  action="delete_student.php?id=<?= (int)$student["id"] ?>">
                <input type="hidden"
                       name="id"
                       value="<?= (int)$student["id"] ?>">

                <button type="submit">Yes, Confirm</button>

                <a href="index.php">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
