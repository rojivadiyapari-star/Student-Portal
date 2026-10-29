
<?php
require_once "auth.php";
require_admin();
require_once "db.php";

// Get all events from the database
$sql = "SELECT id, title, description, event_date, venue, poster
        FROM events
        ORDER BY event_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Manage Events</h1>

    <?php display_flash(); ?>

    <p>
        <a href="dashboard.php" class="btn">Dashboard</a>
        <a href="add_event.php" class="btn">Add New Event</a>
        <a href="logout.php" class="btn">Logout</a>
    </p>

    <?php if ($result->num_rows > 0): ?>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Event Title</th>
                        <th>Description</th>
                        <th>Date and Time</th>
                        <th>Venue</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php while ($event = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int) $event["id"] ?></td>

                        <td>
                            <?php if (!empty($event["poster"])): ?>
                                <img
                                    src="uploads/<?= rawurlencode(basename($event["poster"])) ?>"
                                    alt="Event poster"
                                    style="width:100px; height:auto;"
                                >
                            <?php else: ?>
                                No poster
                            <?php endif; ?>
                        </td>

                        <td><?= h($event["title"]) ?></td>

                        <td>
                            <?= nl2br(h($event["description"])) ?>
                        </td>

                        <td>
                            <?= h(date(
                                "d-m-Y h:i A",
                                strtotime($event["event_date"])
                            )) ?>
                        </td>

                        <td><?= h($event["venue"]) ?></td>

                        <td>
                            <a
                                href="edit_event.php?id=<?= (int) $event["id"] ?>"
                                class="btn"
                            >Edit</a>

                            <a
                                href="delete_event.php?id=<?= (int) $event["id"] ?>"
                                class="btn btn-danger"
                            >Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <p>No events found. Click <strong>Add New Event</strong> to create your first event.</p>

    <?php endif; ?>

</div>

</body>
</html>

<?php
$result->free();
$conn->close();
?>
