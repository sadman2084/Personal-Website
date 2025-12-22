<?php
header('Content-Type: application/json');
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['title']) || !isset($data['description'])) {
    echo json_encode(['success' => false, 'message' => 'Title and description are required']);
    exit;
}

$admin_id = $_SESSION['admin_id'];
$title = trim($data['title']);
$description = trim($data['description']);
$github_link = isset($data['github_link']) ? trim($data['github_link']) : null;
$live_link = isset($data['live_link']) ? trim($data['live_link']) : null;
$image_url = isset($data['image_url']) ? trim($data['image_url']) : null;
$tech_stack = isset($data['tech_stack']) ? trim($data['tech_stack']) : null;

// Validate URLs if provided
if ($github_link && !filter_var($github_link, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid GitHub URL']);
    exit;
}

if ($live_link && !filter_var($live_link, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Live Demo URL']);
    exit;
}

// Insert project
$stmt = $conn->prepare("INSERT INTO projects (admin_id, title, description, github_link, live_link, image_url, tech_stack) VALUES (?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("issssss", $admin_id, $title, $description, $github_link, $live_link, $image_url, $tech_stack);

if ($stmt->execute()) {
    $project_id = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Project added successfully!',
        'project_id' => $project_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add project: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>
