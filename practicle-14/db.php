
<?php
// db.php — Database connection and shared security functions

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$dbname = "practical14_db";
$username = "root";
$password = ""; // Default XAMPP MySQL settings

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Database connection failed. Check your XAMPP and database settings.");
}

// Require a logged-in user.
function requireLogin(): void {
    if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
        http_response_code(401);
        exit("Please log in first.");
    }
}

// Restrict a page to administrators.
function requireAdmin(): void {
    requireLogin();

    if ($_SESSION['role'] !== 'admin') {
        header("Location: 403.php");
        exit;
    }
}

// Record an action performed by a logged-in user.
function recordAudit(PDO $pdo, string $action): void {
    requireLogin();

    $stmt = $pdo->prepare(
        "INSERT INTO audit_logs (user_id, action, ip_address)
         VALUES (:user_id, :action, :ip)"
    );

    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':action' => $action,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null
    ]);
}
?>
