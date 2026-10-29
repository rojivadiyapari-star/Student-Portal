
<?php
require_once "db.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

// Get student ID from URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}

// Fetch the selected active student
$stmt = $conn->prepare(
    "SELECT id, name, email, course
     FROM students
     WHERE id = ? AND status = 'Active'"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$student = $result->fetch_assoc();

$stmt->close();

if (!$student) {
    $conn->close();
    exit("Student not found or is inactive.");
}

$courses = [
    "B.Tech CSE",
    "B.Tech IT",
    "BCA",
    "MCA",
    "BBA"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - Practical 11</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <section class="panel">

        <h1>Edit Student Details</h1>
        <p>Update the student's information below.</p>

        <form action="update_student.php" method="post" class="form-grid">

            <input
                type="hidden"
                name="id"
                value="<?= (int)$student['id'] ?>"
            >

            <label>
                Student Name
                <input
                    type="text"
                    name="name"
                    maxlength="100"
                    value="<?= e($student['name']) ?>"
                    required
                >
            </label>

            <label>
                Email Address
                <input
                    type="email"
                    name="email"
                    maxlength="150"
                    value="<?= e($student['email']) ?>"
                    required
                >
            </label>

            <label>
                Course
                <select name="course" required>
                    <?php foreach ($courses as $course): ?>
                        <option
                            value="<?= e($course) ?>"
                            <?= $student['course'] === $course
                                ? 'selected'
                                : '' ?>
                        >
                            <?= e($course) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <button type="submit">Update Student</button>

            <a href="index.php" class="button secondary">
                Cancel
            </a>

        </form>

    </section>
</div>

</body>
</html>

<?php
$conn->close();
?>
