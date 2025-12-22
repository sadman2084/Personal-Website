<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include 'config.php';

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['username']) || !isset($data['email']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$username = trim($data['username']);
$email = trim($data['email']);
$password = $data['password'];

// Validate username
if (strlen($username) < 3) {
    echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters']);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
    echo json_encode(['success' => false, 'message' => 'Username can only contain letters, numbers, underscores, and hyphens']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Validate password length
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

// Check if username already exists
$check_username = $conn->prepare("SELECT id FROM admins WHERE username = ?");
$check_username->bind_param("s", $username);
$check_username->execute();
$result_username = $check_username->get_result();

if ($result_username->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username already taken']);
    $check_username->close();
    $conn->close();
    exit;
}

// Check if email already exists
$check_email = $conn->prepare("SELECT id FROM admins WHERE email = ?");
$check_email->bind_param("s", $email);
$check_email->execute();
$result_email = $check_email->get_result();

if ($result_email->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    $check_email->close();
    $check_username->close();
    $conn->close();
    exit;
}

// Hash password with bcrypt
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Insert admin into database
$insert = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
if (!$insert) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$insert->bind_param("sss", $username, $email, $hashed_password);

if ($insert->execute()) {
    // Get the inserted ID and timestamp
    $new_admin_id = $conn->insert_id;
    echo json_encode([
        'success' => true, 
        'message' => 'Admin account created successfully',
        'admin_id' => $new_admin_id,
        'username' => $username,
        'email' => $email
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $conn->error]);
}

$insert->close();
$check_email->close();
$check_username->close();
$conn->close();
?>

