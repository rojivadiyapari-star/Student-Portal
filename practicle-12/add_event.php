
<?php
require_once "auth.php";
require_admin();
require_once "db.php";

$title = "";
$description = "";
$eventDate = "";
$venue = "";
$error = "";

$maxBytes = 2 * 1024 * 1024; // Maximum 2 MB

$allowedMime = [
    "image/jpeg" => "jpg",
    "image/png"  => "png",
    "image/gif"  => "gif",
    "image/webp" => "webp"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $eventDate = trim($_POST["event_date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");

    // Validate required fields
    if (
        $title === "" ||
        $description === "" ||
        $eventDate === "" ||
        $venue === ""
    ) {
        $error = "Please fill in all event fields.";

    } elseif (
        mb_strlen($title) > 150 ||
        mb_strlen($venue) > 150
    ) {
        $error = "Title or venue is too long.";

    } elseif (
        !isset($_FILES["poster"]) ||
        $_FILES["poster"]["error"] === UPLOAD_ERR_NO_FILE
    ) {
        $error = "Please select a poster image.";

    } elseif ($_FILES["poster"]["error"] !== UPLOAD_ERR_OK) {
        if (
            $_FILES["poster"]["error"] === UPLOAD_ERR_INI_SIZE ||
            $_FILES["poster"]["error"] === UPLOAD_ERR_FORM_SIZE
        ) {
            $error = "Poster exceeds the allowed upload size.";
        } else {
            $error = "Poster upload failed. Please try again.";
        }

    } elseif ($_FILES["poster"]["size"] > $maxBytes) {
        $error = "Poster must be 2 MB or smaller.";

    } else {
        $tmpFile = $_FILES["poster"]["tmp_name"];

        // Check actual MIME type, not just the filename extension
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpFile);

        if (
            !isset($allowedMime[$mime]) ||
            @getimagesize($tmpFile) === false
        ) {
            $error = "Invalid file type. Upload JPG, PNG, GIF, or WEBP images only.";

        } else {
            $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!is_writable($uploadDir)) {
                $error = "The uploads folder is not writable.";

            } else {
                // Generate a unique filename
                $filename = bin2hex(random_bytes(16))
                    . "." . $allowedMime[$mime];

                $filePath = $uploadDir
                    . DIRECTORY_SEPARATOR . $filename;

                $relativePath = "uploads/" . $filename;

                if (!move_uploaded_file($tmpFile, $filePath)) {
                    $error = "Could not save the poster.";

                } else {
                    try {
                        $stmt = $conn->prepare(
                            "INSERT INTO events
                            (title, description, event_date, venue, poster)
                            VALUES (?, ?, ?, ?, ?)"
                        );

                        $stmt->bind_param(
                            "sssss",
                            $title,
                            $description,
                            $eventDate,
                            $venue,
                            $relativePath
                        );

                        $stmt->execute();
                        $stmt->close();

                        set_flash(
                            "Event created successfully.",
                            "success"
                        );

                        $conn->close();

                        header("Location: events.php");
                        exit;

                    } catch (Throwable $e) {
                        if (is_file($filePath)) {
                            unlink($filePath);
                        }

                        $error = "Could not save the event. Please check the database.";
                    }
                }
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topbar">
    <a class="brand" href="dashboard.php">Event Manager</a>

    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="events.php">Events</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="container narrow">
    <section class="panel">

        <h1>Create Event</h1>
        <p class="muted">Enter the event details and upload a poster.</p>

        <?php if ($error !== ""): ?>
            <div class="alert error">
                <?= h($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="add_event.php"
              enctype="multipart/form-data">

            <label for="title">Event Title</label>
            <input
                type="text"
                id="title"
                name="title"
                maxlength="150"
                value="<?= h($title) ?>"
                required
            >

            <label for="description">Event Description</label>
            <textarea
                id="description"
                name="description"
                rows="4"
                required
            ><?= h($description) ?></textarea>

            <label for="event_date">Event Date</label>
            <input
                type="date"
                id="event_date"
                name="event_date"
                value="<?= h($eventDate) ?>"
                required
            >

            <label for="venue">Venue</label>
            <input
                type="text"
                id="venue"
                name="venue"
                maxlength="150"
                value="<?= h($venue) ?>"
                required
            >

            <label for="poster">
                Event Poster (JPG, PNG, GIF, or WEBP; maximum 2 MB)
            </label>
            <input
                type="file"
                id="poster"
                name="poster"
                accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
                required
            >

            <p class="hint">
                Select an image file no larger than 2 MB.
            </p>

            <div class="action-row">
                <button class="button" type="submit">
                    Save Event
                </button>

                <a class="button secondary" href="events.php">
                    Cancel
                </a>
            </div>

        </form>
    </section>
</main>

</body>
</html>
