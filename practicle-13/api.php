
<?php
header("Content-Type: application/json; charset=UTF-8");

function respond($status, $data) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

require_once "db.php";

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    $result = $conn->query(
        "SELECT id, name, email, course FROM students ORDER BY id DESC"
    );

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    respond(200, ["success" => true, "data" => $students]);
}

if ($method !== "POST") {
    respond(405, ["success" => false, "message" => "Method not allowed."]);
}

$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    respond(400, ["success" => false, "message" => "Invalid JSON data."]);
}

$action = $input["action"] ?? "";

if ($action === "add" || $action === "update") {
    $name = trim($input["name"] ?? "");
    $email = trim($input["email"] ?? "");
    $course = trim($input["course"] ?? "");

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || $course === "") {
        respond(422, [
            "success" => false,
            "message" => "Enter a name, valid email, and course."
        ]);
    }

    if ($action === "add") {
        $stmt = $conn->prepare(
            "INSERT INTO students (name, email, course) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $name, $email, $course);
        $stmt->execute();

        respond(201, [
            "success" => true,
            "message" => "Student added successfully."
        ]);
    }

    $id = filter_var($input["id"] ?? null, FILTER_VALIDATE_INT);
    if (!$id || $id < 1) {
        respond(400, ["success" => false, "message" => "Invalid student ID."]);
    }

    $stmt = $conn->prepare(
        "UPDATE students SET name = ?, email = ?, course = ? WHERE id = ?"
    );
    $stmt->bind_param("sssi", $name, $email, $course, $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $check = $conn->prepare("SELECT id FROM students WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();

        if ($check->get_result()->num_rows === 0) {
            respond(404, ["success" => false, "message" => "Student not found."]);
        }
    }

    respond(200, ["success" => true, "message" => "Student updated successfully."]);
}

if ($action === "delete") {
    $id = filter_var($input["id"] ?? null, FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        respond(400, ["success" => false, "message" => "Invalid student ID."]);
    }

    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        respond(404, ["success" => false, "message" => "Student not found."]);
    }

    respond(200, ["success" => true, "message" => "Student deleted successfully."]);
}

respond(400, ["success" => false, "message" => "Unknown action."]);
?>
