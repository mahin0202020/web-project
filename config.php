<?php
// ============================================================
//  Paws & Pour – Database Configuration
//  File: includes/config.php
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // ← Change to your MySQL username
define('DB_PASS', '');            // ← Change to your MySQL password
define('DB_NAME', 'pawsandpour');
define('SITE_NAME', 'Paws & Pour');
define('SITE_URL',  'http://localhost/pawsandpour');

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Create and return a mysqli database connection.
 */
function db_connect() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Database connection failed: ' . $conn->connect_error
        ]));
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

/**
 * Returns true if a user session is active.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Returns current user's data from the database.
 */
function current_user() {
    if (!is_logged_in()) return null;
    $conn = db_connect();
    $id   = (int)$_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, full_name, email, phone, avatar FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $conn->close();
    return $user;
}

/**
 * Redirect helper.
 */
function redirect($path) {
    header("Location: $path");
    exit;
}

/**
 * Send JSON response and exit.
 */
function json_response($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
