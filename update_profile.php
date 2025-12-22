<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$admin_id = $_SESSION['admin_id'];

// Validate and sanitize data
$first_name = isset($data['first_name']) ? trim($data['first_name']) : null;
$last_name = isset($data['last_name']) ? trim($data['last_name']) : null;
$phone = isset($data['phone']) ? trim($data['phone']) : null;
$location = isset($data['location']) ? trim($data['location']) : null;
$bio = isset($data['bio']) ? trim($data['bio']) : null;
$github_url = isset($data['github_url']) ? trim($data['github_url']) : null;
$linkedin_url = isset($data['linkedin_url']) ? trim($data['linkedin_url']) : null;
$portfolio_title = isset($data['portfolio_title']) ? trim($data['portfolio_title']) : null;
$profile_image = isset($data['profile_image']) ? trim($data['profile_image']) : null;

// Validate URLs if provided
if ($github_url && !filter_var($github_url, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid GitHub URL']);
    exit;
}

if ($linkedin_url && !filter_var($linkedin_url, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid LinkedIn URL']);
    exit;
}

if ($profile_image && !filter_var($profile_image, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid image URL']);
    exit;
}

// Update admin profile
$stmt = $conn->prepare("UPDATE admins SET first_name = ?, last_name = ?, bio = ?, phone = ?, location = ?, github_url = ?, linkedin_url = ?, portfolio_title = ?, profile_image = ? WHERE id = ?");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sssssssssi", $first_name, $last_name, $bio, $phone, $location, $github_url, $linkedin_url, $portfolio_title, $profile_image, $admin_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully!'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
