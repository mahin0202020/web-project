<?php
// ============================================================
//  Paws & Pour – Authentication & API Handler
//  File: auth.php
//  All POST endpoints handled here
// ============================================================
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Method not allowed.']);
}

$action = trim($_POST['action'] ?? '');

// ── SIGNUP ──────────────────────────────────────────────────
if ($action === 'signup') {
    $name  = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');

    if (!$name || !$email || !$pass) {
        json_response(['success' => false, 'message' => 'Please fill all required fields.']);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response(['success' => false, 'message' => 'Invalid email address.']);
    }
    if (strlen($pass) < 6) {
        json_response(['success' => false, 'message' => 'Password must be at least 6 characters.']);
    }

    $conn = db_connect();

    // Check duplicate email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $conn->close();
        json_response(['success' => false, 'message' => 'Email already registered. Please log in.']);
    }

    $hashed = password_hash($pass, PASSWORD_DEFAULT);
    $stmt   = $conn->prepare("INSERT INTO users (full_name, email, password, phone) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $name, $email, $hashed, $phone);

    if ($stmt->execute()) {
        $new_id = $conn->insert_id;
        $_SESSION['user_id']   = $new_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $conn->close();
        json_response(['success' => true, 'message' => 'Welcome to Paws & Pour!', 'name' => $name, 'user_id' => $new_id]);
    } else {
        $conn->close();
        json_response(['success' => false, 'message' => 'Registration failed. Please try again.']);
    }
}

// ── LOGIN ───────────────────────────────────────────────────
if ($action === 'login') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';

    if (!$email || !$pass) {
        json_response(['success' => false, 'message' => 'Please enter your email and password.']);
    }

    $conn = db_connect();
    $stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $conn->close();

    if ($row && password_verify($pass, $row['password'])) {
        $_SESSION['user_id']    = $row['id'];
        $_SESSION['user_name']  = $row['full_name'];
        $_SESSION['user_email'] = $row['email'];
        json_response([
            'success' => true,
            'message' => 'Welcome back, ' . $row['full_name'] . '!',
            'name'    => $row['full_name'],
            'user_id' => $row['id'],
            'email'   => $row['email']
        ]);
    } else {
        json_response(['success' => false, 'message' => 'Incorrect email or password.']);
    }
}

// ── LOGOUT ──────────────────────────────────────────────────
if ($action === 'logout') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
    }
    session_destroy();
    json_response(['success' => true, 'message' => 'Logged out successfully.']);
}

// ── SESSION CHECK ───────────────────────────────────────────
if ($action === 'check_session') {
    if (is_logged_in()) {
        json_response([
            'success'   => true,
            'logged_in' => true,
            'name'      => $_SESSION['user_name'],
            'email'     => $_SESSION['user_email'] ?? '',
            'user_id'   => $_SESSION['user_id']
        ]);
    } else {
        json_response(['success' => true, 'logged_in' => false]);
    }
}

// ── ADD PET ─────────────────────────────────────────────────
if ($action === 'add_pet') {
    if (!is_logged_in()) {
        json_response(['success' => false, 'message' => 'Not logged in.']);
    }

    $uid     = (int)$_SESSION['user_id'];
    $name    = trim($_POST['pet_name'] ?? '');
    $species = $_POST['species'] ?? 'dog';
    $breed   = trim($_POST['breed'] ?? '');
    $age     = trim($_POST['age'] ?? '');
    $weight  = !empty($_POST['weight']) ? (float)$_POST['weight'] : null;
    $notes   = trim($_POST['notes'] ?? '');

    if (!$name) {
        json_response(['success' => false, 'message' => 'Pet name is required.']);
    }

    // Validate species
    $valid_species = ['dog', 'cat', 'rabbit', 'bird', 'other'];
    if (!in_array($species, $valid_species)) {
        $species = 'other';
    }

    $conn = db_connect();
    $stmt = $conn->prepare(
        "INSERT INTO pets (user_id, name, species, breed, age, weight, notes) VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('issssds', $uid, $name, $species, $breed, $age, $weight, $notes);

    if ($stmt->execute()) {
        $pet_id = $conn->insert_id;
        $conn->close();
        json_response(['success' => true, 'message' => $name . ' added successfully! 🐾', 'pet_id' => $pet_id]);
    } else {
        $conn->close();
        json_response(['success' => false, 'message' => 'Could not add pet. Please try again.']);
    }
}

// ── GET PETS ─────────────────────────────────────────────────
if ($action === 'get_pets') {
    if (!is_logged_in()) {
        json_response(['success' => false, 'message' => 'Not logged in.']);
    }

    $uid  = (int)$_SESSION['user_id'];
    $conn = db_connect();
    $stmt = $conn->prepare("SELECT * FROM pets WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $pets   = [];
    while ($r = $result->fetch_assoc()) {
        $pets[] = $r;
    }
    $conn->close();
    json_response(['success' => true, 'pets' => $pets]);
}

// ── LOG PET ACTIVITY ────────────────────────────────────────
if ($action === 'log_activity') {
    if (!is_logged_in()) {
        json_response(['success' => false, 'message' => 'Not logged in.']);
    }

    $pet_id = (int)($_POST['pet_id'] ?? 0);
    $type   = $_POST['log_type'] ?? 'note';
    $desc   = trim($_POST['description'] ?? '');

    if (!$pet_id || !$desc) {
        json_response(['success' => false, 'message' => 'Missing pet ID or description.']);
    }

    // Validate log_type
    $valid_types = ['feeding', 'walk', 'play', 'grooming', 'vet', 'note'];
    if (!in_array($type, $valid_types)) {
        $type = 'note';
    }

    // Make sure this pet belongs to the logged-in user
    $uid  = (int)$_SESSION['user_id'];
    $conn = db_connect();
    $chk  = $conn->prepare("SELECT id FROM pets WHERE id = ? AND user_id = ? LIMIT 1");
    $chk->bind_param('ii', $pet_id, $uid);
    $chk->execute();
    if ($chk->get_result()->num_rows === 0) {
        $conn->close();
        json_response(['success' => false, 'message' => 'Pet not found.']);
    }

    $stmt = $conn->prepare("INSERT INTO pet_logs (pet_id, log_type, description) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $pet_id, $type, $desc);

    if ($stmt->execute()) {
        $conn->close();
        json_response(['success' => true, 'message' => 'Activity logged! 📝']);
    } else {
        $conn->close();
        json_response(['success' => false, 'message' => 'Could not log activity.']);
    }
}

// ── GET PET LOGS ────────────────────────────────────────────
if ($action === 'get_logs') {
    if (!is_logged_in()) {
        json_response(['success' => false, 'message' => 'Not logged in.']);
    }

    $pet_id = (int)($_POST['pet_id'] ?? 0);
    $uid    = (int)$_SESSION['user_id'];
    $conn   = db_connect();

    // Verify pet ownership
    $chk = $conn->prepare("SELECT id FROM pets WHERE id = ? AND user_id = ? LIMIT 1");
    $chk->bind_param('ii', $pet_id, $uid);
    $chk->execute();
    if ($chk->get_result()->num_rows === 0) {
        $conn->close();
        json_response(['success' => false, 'message' => 'Pet not found.']);
    }

    $stmt = $conn->prepare(
        "SELECT id, pet_id, log_type, description, logged_at FROM pet_logs WHERE pet_id = ? ORDER BY logged_at DESC LIMIT 30"
    );
    $stmt->bind_param('i', $pet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $logs   = [];
    while ($r = $result->fetch_assoc()) {
        $logs[] = $r;
    }
    $conn->close();
    json_response(['success' => true, 'logs' => $logs]);
}

// ── MAKE RESERVATION ─────────────────────────────────────────
if ($action === 'make_reservation') {
    if (!is_logged_in()) {
        json_response(['success' => false, 'message' => 'Please log in to reserve a table.']);
    }

    $uid    = (int)$_SESSION['user_id'];
    $name   = trim($_POST['customer_name'] ?? '');
    $date   = $_POST['date']      ?? '';
    $time   = $_POST['time_slot'] ?? '';
    $guests = (int)($_POST['guests'] ?? 1);
    $pet_id = !empty($_POST['pet_id']) ? (int)$_POST['pet_id'] : null;
    $notes  = trim($_POST['notes'] ?? '');

    if (!$date || !$time) {
        json_response(['success' => false, 'message' => 'Please select a date and time.']);
    }

    // Validate date is not in the past
    if (strtotime($date) < strtotime(date('Y-m-d'))) {
        json_response(['success' => false, 'message' => 'Please select a future date.']);
    }

    $conn = db_connect();
    $stmt = $conn->prepare(
        "INSERT INTO reservations (user_id, pet_id, date, time_slot, guests, notes) VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('iissis', $uid, $pet_id, $date, $time, $guests, $notes);

    if ($stmt->execute()) {
        $res_id = $conn->insert_id;
        $conn->close();
        json_response([
            'success'        => true,
            'message'        => 'Table reserved! See you soon 🐾',
            'reservation_id' => $res_id
        ]);
    } else {
        $conn->close();
        json_response(['success' => false, 'message' => 'Could not complete reservation. Please try again.']);
    }
}

// ── GET MY RESERVATIONS ──────────────────────────────────────
if ($action === 'get_reservations') {
    if (!is_logged_in()) {
        json_response(['success' => false, 'message' => 'Not logged in.']);
    }

    $uid  = (int)$_SESSION['user_id'];
    $conn = db_connect();
    $stmt = $conn->prepare(
        "SELECT r.*, p.name AS pet_name FROM reservations r 
         LEFT JOIN pets p ON r.pet_id = p.id 
         WHERE r.user_id = ? ORDER BY r.date DESC, r.time_slot ASC LIMIT 20"
    );
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservations = [];
    while ($r = $result->fetch_assoc()) {
        $reservations[] = $r;
    }
    $conn->close();
    json_response(['success' => true, 'reservations' => $reservations]);
}

// ── GET MENU ITEMS ───────────────────────────────────────────
if ($action === 'get_menu') {
    $conn     = db_connect();
    $category = trim($_POST['category'] ?? '');

    if ($category && $category !== 'all') {
        $stmt = $conn->prepare("SELECT * FROM menu_items WHERE available = 1 AND category = ? ORDER BY id");
        $stmt->bind_param('s', $category);
    } else {
        $stmt = $conn->prepare("SELECT * FROM menu_items WHERE available = 1 ORDER BY category, id");
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $items  = [];
    while ($r = $result->fetch_assoc()) {
        $items[] = $r;
    }
    $conn->close();
    json_response(['success' => true, 'items' => $items]);
}

// ── UNKNOWN ACTION ───────────────────────────────────────────
json_response(['success' => false, 'message' => 'Invalid action: ' . htmlspecialchars($action)]);
