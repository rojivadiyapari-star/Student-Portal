
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_admin() {
    if (empty($_SESSION["admin_id"])) {
        header("Location: login.php");
        exit;
    }
}

function h($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

function set_flash($type, $message) {
    $_SESSION["flash"] = [
        "type" => $type,
        "message" => $message
    ];
}

function display_flash() {
    if (!empty($_SESSION["flash"])) {
        $flash = $_SESSION["flash"];
        $type = ($flash["type"] === "success")
            ? "success"
            : "error";

        echo '<div class="' . $type . '">';
        echo h($flash["message"]);
        echo '</div>';

        unset($_SESSION["flash"]);
    }
}

?>
