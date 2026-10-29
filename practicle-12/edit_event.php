
<?php
require_once "auth.php";
require_admin();
require_once "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    set_flash("error", "Invalid event ID.");
    header("Location: events.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    $conn->close();
    set_flash("error", "Event not found.");
    header("Location: events.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $event_date = trim($_POST["event_date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");
    $poster = $event["poster"];
    $newPosterPath = null;

    if ($title === "" || $description === "" ||
        $event_date === "" || $venue === "") {
        $error = "Please fill in all fields.";
    } else {
        // Validate the date format
        $dateObject = DateTime::createFromFormat("Y-m-d\TH:i", $event_date);

        if (!$dateObject) {
            $error = "Please enter a valid event date and time.";
        }
    }

    // Validate an optional new poster
    if (
        $error === "" &&
        isset($_FILES["poster"]) &&
        $_FILES["poster"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {
        $file = $_FILES["poster"];

        if ($file["error"] !== UPLOAD_ERR_OK) {
            $error = "Poster upload failed. Please try again.";
        } elseif ($file["size"] > 2 * 1024 * 1024) {
            $error = "Poster must be 2 MB or smaller.";
        } else {
            $allowedTypes = [
                "image/jpeg" => "jpg",
                "image/png" => "png",
                "image/gif" => "gif",
                "image/webp" => "webp"
            ];

            $mimeType = mime_content_type($file["tmp_name"]);
            $imageInfo = @getimagesize($file["tmp_name"]);

            if (
                !isset($allowedTypes[$mimeType]) ||
                $imageInfo === false
            ) {
                $error = "Invalid poster. Upload a JPG, PNG, GIF, or WEBP image.";
            } else {
                $extension = $allowedTypes[$mimeType];
                $fileName = bin2hex(random_bytes(16)) . "." . $extension;
                $uploadDirectory = __DIR__ . "/uploads/";

                if (!is_dir($uploadDirectory)) {
                    mkdir($uploadDirectory, 0755, true);
                }

                $newPosterPath = $uploadDirectory . $fileName;

                if (!move_uploaded_file($file["tmp_name"], $newPosterPath)) {
                    $error = "Could not save the poster.";
                    $newPosterPath = null;
                } else {
                    $poster = $fileName;
                }
            }
        }
    }

    if ($error === "") {
        try {
            $stmt = $conn->prepare(
                "UPDATE events
                 SET title = ?, description = ?, event_date = ?,
                     venue = ?, poster = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "sssssi",
                $title,
                $description,
                $event_date,
                $venue,
                $poster,
                $id
            );

            $stmt->execute();
            $stmt->close();

            // Remove the previous poster only after a successful update
            if ($newPosterPath !== null && !empty($event["poster"])) {
                $oldPoster = __DIR__ . "/uploads/" . basename($event["poster"]);

                if (is_file($oldPoster)) {
                    unlink($oldPoster);
                }
            }

            $conn->close();
            set_flash("success", "Event updated successfully.");
            header("Location: events.php");
            exit;

        } catch (Throwable $e) {
            if ($newPosterPath !== null && is_file($newPosterPath)) {
                unlink($newPosterPath);
            }

            $error = "Could not update the event. Please try again.";
        }
    }
}

// Convert database datetime into the value required by datetime-local
$dateValue = str_replace(" ", "T", substr($event["event_date"], 0, 16));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dateValue = $_POST["event_date"] ?? $dateValue;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Event</h1>

        <?php if ($error !== ""): ?>
            <p class="error"><?= h($error) ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label for="title">Event Title</label>
            <input
                type="text"
                id="title"
                name="title"
                value="<?= h($_POST["title"] ?? $event["title"]) ?>"
                required
            >

            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                required
            ><?= h($_POST["description"] ?? $event["description"]) ?></textarea>

            <label for="event_date">Event Date and Time</label>
            <input
                type="datetime-local"
                id="event_date"
                name="event_date"
                value="<?= h($dateValue) ?>"
                required
            >

            <label for="venue">Venue</label>
            <input
                type="text"
                id="venue"
                name="venue"
                value="<?= h($_POST["venue"] ?? $event["venue"]) ?>"
                required
            >

            <label>Current Poster</label>

            <?php if (!empty($event["poster"])): ?>
                <p>
                    <img
                        src="uploads/<?= rawurlencode(basename($event["poster"])) ?>"
                        alt="Current event poster"
                        style="max-width:200px; height:auto;"
                    >
                </p>
            <?php else: ?>
                <p>No poster uploaded.</p>
            <?php endif; ?>

            <label for="poster">Upload New Poster (optional)</label>
            <input
                type="file"
                id="poster"
                name="poster"
                accept=".jpg,.jpeg,.png,.gif,.webp"
            >
            <small>Allowed: JPG, PNG, GIF, WEBP. Maximum size: 2 MB.</small>

            <p>
                <button type="submit">Update Event</button>
                <a href="events.php" class="btn">Cancel</a>
            </p>
        </form>
    </div>
</body>
</html>
