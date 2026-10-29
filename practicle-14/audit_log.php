```php
<?php
session_start();
require_once 'db.php';

$logs = [];
$error = '';
$isAdmin = isset($_SESSION['user_id'])
    && (($_SESSION['role'] ?? '') === 'admin');

if (!$isAdmin) {
    $error = 'You are not logged in as an administrator. Please log in again.';
} else {
    try {
        $stmt = $pdo->query("SELECT * FROM audit_logs ORDER BY id DESC");
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Could not load audit logs. Check your database connection and table.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Audit Logs - Practical 14</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">Practical 14</span>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">
            Dashboard
        </a>
        <a href="logout.php" class="btn btn-warning btn-sm">
            Logout
        </a>
    </div>
</nav>

<div class="container py-5">
    <h2>Audit Logs</h2>
    <p class="text-muted">Administrator activity history</p>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
            <div class="mt-3">
                <a href="login.php" class="btn btn-primary btn-sm">Go to Login</a>
            </div>
        </div>

    <?php elseif (empty($logs)): ?>
        <div class="alert alert-info">
            No audit records found in the database yet.
        </div>

    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                Activity History
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <?php foreach (array_keys($logs[0]) as $column): ?>
                                    <th><?= htmlspecialchars((string)$column) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <?php foreach ($log as $value): ?>
                                        <td><?= htmlspecialchars((string)($value ?? '')) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
```