
<?php
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 Forbidden - Practical 14</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow text-center p-5" style="max-width: 600px;">
        <h1 class="display-1 fw-bold text-danger">403</h1>

        <h2 class="mb-3">Access Forbidden</h2>

        <p class="text-muted">
            You do not have permission to access this page.
            Only authorized administrators can view this resource.
        </p>

        <div class="d-flex justify-content-center gap-2">
            <a href="dashboard.php" class="btn btn-primary">
                Back to Dashboard
            </a>

            <a href="logout.php" class="btn btn-outline-secondary">
                Logout
            </a>
        </div>
    </div>
</div>

</body>
</html>
