
<?php
require_once "db.php";

// Set the CSV filename
$filename = "student_records_" . date("Y-m-d") . ".csv";

// Set download headers
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Open output stream
$output = fopen("php://output", "w");

// Add UTF-8 BOM for Excel compatibility
fwrite($output, "\xEF\xBB\xBF");

// Add CSV column headings
fputcsv($output, [
    "Student ID",
    "Name",
    "Email",
    "Course",
    "Status",
    "Created At"
]);

// Fetch student records
$sql = "SELECT id, name, email, course, status, created_at
        FROM students
        ORDER BY id ASC";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row["id"],
            $row["name"],
            $row["email"],
            $row["course"],
            $row["status"],
            $row["created_at"]
        ]);
    }
}

fclose($output);
$conn->close();
exit;
?>
