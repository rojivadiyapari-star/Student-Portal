
<?php
require_once "auth.php";
require_admin();
require_once "db.php";

// Check whether an event ID was provided
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    set_flash("error", "Invalid event ID.");
    header("Location: events.php");
    exit;
}

// Find the event
$stmt = $conn->prepare(
    "SELECT id, title, poster FROM events WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    set_flash("error", "Event not found.");
    header("Location: events.php");
    exit;
}

// Delete only after the admin confirms the form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    // Remove the event poster if it exists
    if (!empty($event["poster"])) {
        $posterName = basename($event["poster"]);
        $posterPath = __DIR__ . "/uploads/" . $posterName;

        if (is_file($posterPath)) {
            unlink($posterPath);
        }
    }

    $conn->close();

    set_flash("success", "Event deleted successfully.");
    header("Location: events.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Delete Event</h1>

        <p>
            Are you sure you want to delete
            <strong><?= h($event["title"]) ?></strong>?
        </p>

        <p>This action cannot be undone.</p>

        <form method="POST"
              action="delete_event.php?id=<?= (int) $event["id"] ?>">
            <button type="submit" class="btn btn-danger">
                Yes, Delete Event
            </button>

            <a href="events.php" class="btn">
                Cancel
            </a>
        </form>
    </div>
</body>
</html>
