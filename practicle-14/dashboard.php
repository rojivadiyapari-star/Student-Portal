```php
<?php
session_start();
require_once 'db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['name'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'student';

$totalStudents = 0;
$totalEvents = 0;
$totalRegistrations = 0;
$latestRegistrations = [];
$error = '';

try {
    $totalStudents = (int) $pdo
        ->query("SELECT COUNT(*) FROM students")
        ->fetchColumn();

    $totalEvents = (int) $pdo
        ->query("SELECT COUNT(*) FROM events")
        ->fetchColumn();

    $totalRegistrations = (int) $pdo
        ->query("SELECT COUNT(*) FROM registrations")
        ->fetchColumn();

    $sql = "
        SELECT
            r.id,
            u.name AS student_name,
            e.title AS event_title,
            r.registered_at
        FROM registrations r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN events e ON r.event_id = e.id
        ORDER BY r.id DESC
        LIMIT 10
    ";

    $stmt = $pdo->query($sql);
    $latestRegistrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = 'Unable to load dashboard data. Please check your database tables.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Practical 14</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Practical 14</a>

        <div class="d-flex flex-wrap gap-2 align-items-center">
            <?php if ($userRole === 'admin'): ?>
                <a class="btn btn-outline-light btn-sm"
                   href="audit_log.php">Audit Logs</a>
            <?php endif; ?>

            <span class="text-white">
                <?= htmlspecialchars($userName) ?>
                (<?= htmlspecialchars($userRole) ?>)
            </span>

            <a class="btn btn-warning btn-sm" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <h2 class="mb-1">Dashboard</h2>
    <p class="text-muted">
        Welcome, <?= htmlspecialchars($userName) ?>!
    </p>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-primary h-100">
                <div class="card-body">
                    <h6 class="text-muted">Total Students</h6>
                    <h2><?= $totalStudents ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-success h-100">
                <div class="card-body">
                    <h6 class="text-muted">