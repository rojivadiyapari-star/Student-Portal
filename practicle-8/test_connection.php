
<?php
require_once "db.php";

try {
    $sql = "SELECT
                s.student_id,
                s.student_name,
                s.email,
                e.event_name,
                r.registration_date
            FROM registrations r
            INNER JOIN students s
                ON r.student_id = s.student_id
            INNER JOIN events e
                ON r.event_id = e.event_id
            WHERE s.student_id >= :min_id
            ORDER BY s.student_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["min_id" => 1]);

    $registrations = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("Unable to retrieve registration records.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 8 - Database Connection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f4f6f9;
            color: #222;
        }

        h1 {
            color: #174a7e;
        }

        .success {
            color: #176b37;
            background: #e4f5e9;
            padding: 12px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #174a7e;
            color: white;
        }

        tr:nth-child(even) {
            background: #eef2f6;
        }
    </style>
</head>
<body>

    <h1>Practical 8: PHP PDO Connection</h1>

    <p class="success">
        Successfully connected to the MySQL database using PDO!
    </p>

    <h2>Student Event Registration Records</h2>

    <?php if (count($registrations) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Event Name</th>
                    <th>Registration Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registrations as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)$row["student_id"]) ?></td>
                        <td><?= htmlspecialchars($row["student_name"]) ?></td>
                        <td><?= htmlspecialchars($row["email"]) ?></td>
                        <td><?= htmlspecialchars($row["event_name"]) ?></td>
                        <td><?= htmlspecialchars($row["registration_date"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No registration records found.</p>
    <?php endif; ?>

</body>
</html>
