<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Get admin profile
$stmt = $conn->prepare("SELECT id, username, email, first_name, last_name, bio, phone, location, github_url, linkedin_url, portfolio_title, profile_image FROM admins WHERE id = ?");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Profile not found']);
    exit;
}

$profile = $result->fetch_assoc();
echo json_encode([
    'success' => true,
    'profile' => $profile
]);

$stmt->close();
$conn->close();
?>
