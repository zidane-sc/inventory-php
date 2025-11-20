<?php
session_start();

// Database Connection
$db_file = __DIR__ . '/inventory.db';
$db = new SQLite3($db_file);

if (!$db) {
    die("Connection failed: " . $db->lastErrorMsg());
}

// Enable foreign keys
$db->exec("PRAGMA foreign_keys = ON;");

// Helper Functions

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function flash($name, $text = '', $type = 'success') {
    if ($text != '') {
        $_SESSION[$name] = ['text' => $text, 'type' => $type];
    } elseif (isset($_SESSION[$name])) {
        $msg = $_SESSION[$name];
        unset($_SESSION[$name]);
        return '<div class="alert alert-' . $msg['type'] . '">' . $msg['text'] . '</div>';
    }
    return '';
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}
?>
