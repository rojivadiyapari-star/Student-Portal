
<?php
session_start();
require_once "db.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

// Read search and filter values
$search = trim($_GET["search"] ?? "");
$course = trim($_GET["course"] ?? "");
$status = $_GET["status"] ?? "Active";

if (!in_array($status, ["Active", "Inactive", "All"], true)) {
    $status = "Active";
}

// Get available courses
$courses = [];
$courseResult = $conn->query(
    "SELECT DISTINCT course FROM students ORDER BY course"
);

while ($row = $courseResult->fetch_assoc()) {
    $courses[] = $row["course"];
}

// Build the student query
$sql = "SELECT id, name, email, course, status
        FROM students WHERE 1=1";

$params = [];
$types = "";

if ($search !== "") {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

if ($course !== "") {
    $sql .= " AND course = ?";
    $params[] = $course;
    $types .= "s";
}

if ($status !== "All") {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$students = $stmt->get_result();

// Get notification messages
$message = $_SESSION["message"] ?? "";
$messageType = $_SESSION["message_type"] ?? "success";

unset($_SESSION["message"], $_SESSION["message_type"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Practical 11 - Student Records</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Student Records Management System</h1>
    <p>Practical 11 — Student Records Management</p>

    <?php if ($message !== ""): ?>
        <div class="alert <?= e($messageType) ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <!-- Add Student Form -->
    <section class="panel">
        <h2>Add New Student</h2>

        <form action="add_student.php" method="post" class="form-grid">

            <input
                type="text"
                name="name"
                placeholder="Enter student name"
                maxlength="100"
                required
            >

            <input
                type="email"
                name="email"
                placeholder="Enter email address"
                maxlength="150"
                required
            >

            <select name="course" required>
                <option value="">Select Course</option>
                <option value="B.Tech CSE">B.Tech CSE</option>
                <option value="B.Tech IT">B.Tech IT</option>
                <option value="BCA">BCA</option>
                <option value="MCA">MCA</option>
                <option value="BBA">BBA</option>
            </select>

            <button type="submit">Add Student</button>

        </form>
    </section>

    <!-- Search and Filters -->
    <section class="panel">
        <h2>Search and Filter Students</h2>

        <form action="index.php" method="get" class="filters">

            <input
                type="search"
                name="search"
                placeholder="Search by name or email"
                value="<?= e($search) ?>"
            >

            <select name="course">
                <option value="">All Courses</option>

                <?php foreach ($courses as $c): ?>
                    <option
                        value="<?= e($c) ?>"
                        <?= $course === $c ? "selected" : "" ?>
                    >
                        <?= e($c) ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <select name="status">
                <?php foreach (["Active", "Inactive", "All"] as $s): ?>
                    <option
                        value="<?= e($s) ?>"
                        <?= $status === $s ? "selected" : "" ?>
                    >
                        <?= e($s) ?> Students
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Apply Filter</button>

            <a href="index.php" class="button secondary">Reset</a>

        </form>
    </section>

    <!-- Student Records Table -->
    <section class="panel">

        <div class="table-heading">
            <h2>Student Records</h2>
            <a href="export.php" class="button">Export CSV</a>
        </div>

        <div class="table-wrap">

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($students->num_rows === 0): ?>

                    <tr>
                        <td colspan="6">No matching students found.</td>
                    </tr>

                <?php else: ?>

                    <?php while ($student = $students->fetch_assoc()): ?>

                        <tr>
                            <td><?= (int)$student["id"] ?></td>

                            <td><?= e($student["name"]) ?></td>

                            <td><?= e($student["email"]) ?></td>

                            <td><?= e($student["course"]) ?></td>

                            <td>
                                <span class="status <?= strtolower(e($student["status"])) ?>">
                                    <?= e($student["status"]) ?>
                                </span>
                            </td>

                            <td class="actions">

                                <?php if ($student["status"] === "Active"): ?>

                                    <a
                                        href="edit_student.php?id=<?= (int)$student["id"] ?>"
                                        class="button small"
                                    >Edit</a>

                                    <a
                                        href="delete_student.php?id=<?= (int)$student["id"] ?>"
                                        class="button danger small"
                                    >Delete</a>

                                <?php else: ?>

                                    <span>Inactive record</span>

                                <?php endif; ?>

                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </section>

</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
