
<?php
// Display errors while developing and testing locally.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

// If the user is already logged in, redirect to the dashboard.
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$message = '';
$messageType = '';

if (isset($_GET['error'])) {
    $message = 'Invalid email or password.';
    $messageType = 'error';
} elseif (isset($_GET['logged_out'])) {
    $message = 'You have logged out successfully.';
    $messageType = 'success';
} elseif (isset($_GET['denied'])) {
    $message = 'Please log in to access the dashboard.';
    $messageType = 'error';
} elseif (isset($_GET['expired'])) {
    $message = 'Your session expired. Please log in again.';
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h2>User Login</h2>
        <p class="subtitle">Enter your email and password to continue.</p>

        <?php if ($message !== ''): ?>
            <div class="message <?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="authenticate.php" method="POST">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
            >

            <button type="submit">Login</button>
        </form>
    </div>

</body>
</html>
