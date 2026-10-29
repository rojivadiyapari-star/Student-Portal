
<?php

// Display MySQLi errors during development
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database connection details
$host = "localhost";
$username = "root";
$password = "";
$database = "practicle12_db";

try {
    // Connect to MySQL database
    $conn = new mysqli(
        $host,
        $username,
        $password,
        $database
    );

    // Set character encoding
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {
    http_response_code(500);

    exit(
        "Database connection failed. Please check that MySQL is running "
        . "and the practicle12_db database exists. Error: "
        . htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            "UTF-8"
        )
    );
}

?>
