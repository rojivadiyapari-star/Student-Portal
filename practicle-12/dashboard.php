
<?php
require_once "auth.php";

// Only logged-in admins can access this page
require_admin();

require_once "db.php";

// Count all events
$totalResult = $conn->query(
    "SELECT COUNT(*) AS total FROM events"
);
$totalEvents = (int) $totalResult->fetch_assoc()["total"];

// Count upcoming events
$upcomingResult = $conn->query(
    "SELECT COUNT(*) AS total
     FROM events
     WHERE event_date >= CURDATE()"
);
$upcomingEvents = (int) $upcomingResult->fetch_assoc()["total"];

// Get the latest five events
$recentResult = $conn->query(
    "SELECT id, title, event_date, venue, poster
     FROM events
     ORDER BY created_at DESC
     LIMIT 5"
);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Event Manager</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topbar">
    <a class="brand" href="dashboard.php">Event Manager</a>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="events.php">Events</a>
        <a href="add_event.php">Create Event</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="container">

    <?php display_flash(); ?>

    <section class="page-heading">
        <div>
            <h1>Admin Dashboard</h1>
            <p class="muted">
                Welcome,
                <?= h($_SESSION["admin_username"] ?? "Admin") ?>!
                Manage your college events here.
            </p>
        </div>

        <a class="button" href="add_event.php">
            + Create Event
        </a>
    </section>

    <section class="stats">

        <article class="stat-card">
            <span>Total Events</span>
            <strong><?= $totalEvents ?></strong>
            <p class="muted">All events in the system</p>
        </article>

        <article class="stat-card">
            <span>Upcoming Events</span>
            <strong><?= $upcomingEvents ?></strong>
            <p class="muted">Events scheduled for today or later</p>
        </article>

    </section>

    <section class="panel">
        <h2>Quick Actions</h2>

        <div class="action-row">
            <a class="button" href="add_event.php">
                Create New Event
            </a>

            <a class="button secondary" href="events.php">
                View All Events
            </a>
        </div>
    </section>

    <section class="panel">
        <h2>Recent Events</h2>

        <?php if ($recentResult->num_rows === 0): ?>

            <p>No events have been created yet.</p>
            <a class="button" href="add_event.php">
                Create Your First Event
            </a>

        <?php else: ?>

            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding:10px;">
                                Poster
                            </th>
                            <th style="text-align:left; padding:10px;">
                                Event Title
                            </th>
                            <th style="text-align:left; padding:10px;">
                                Date
                            </th>
                            <th style="text-align:left; padding:10px;">
                                Venue
                            </th>
                            <th style="text-align:left; padding:10px;">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($event = $recentResult->fetch_assoc()): ?>
                            <tr>
                                <td style="padding:10px;">
                                    <img
                                        src="<?= h($event["poster"]) ?>"
                                        alt="Event poster"
                                        style="width:65px;height:55px;object-fit:cover;border-radius:5px;"
                                    >
                                </td>

                                <td style="padding:10px;">
                                    <?= h($event["title"]) ?>
                                </td>

                                <td style="padding:10px;">
                                    <?= h($event["event_date"]) ?>
                                </td>

                                <td style="padding:10px;">
                                    <?= h($event["venue"]) ?>
                                </td>

                                <td style="padding:10px;">
                                    <a href="edit_event.php?id=<?= (int)$event["id"] ?>">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </section>

</main>

</body>
</html>
